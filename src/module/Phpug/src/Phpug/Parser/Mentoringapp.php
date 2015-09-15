<?php
/**
 * Copyright (c)2014-2014 heiglandreas
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIBILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category 
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright ©2014-2014 Andreas Heigl
 * @license   http://www.opesource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     12.05.14
 * @link      https://github.com/heiglandreas/
 */

namespace Phpug\Parser;


use Zend\Json\Json;

/**
 * Parse the phpmentoring-page for mentors and apprentices.
 *
 * @package Phpug\Parser
 */
class Mentoringapp
{
    /**
     * @var string $githubPath
     */
    protected $githubPath;

    /**
     * @var array $config
     */
    protected $config;

    /**
     * Create an instance of the class
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Parse the given file for apprentices and mentors.
     *
     * @param string $file Path to the File to parse.
     *
     * @return array
     */
    public function parse($uri)
    {
        $return = [];

        $content = file_get_contents($uri);

        $content = Json::decode($content);
        foreach ($content as $entry) {
            if ($entry->isEnabled != 1) {
                continue;
            }
            $values = $this->parseMentoringApiEntry($entry);
            $return[$values['name']] = $values;
        }

        return $return;
    }

    /**
     * Parse a JSON-Entry
     *
     * @param Object $entry
     *
     * @return array
     */
    public function parseMentoringApiEntry($entry)
    {

        $user = array(
            'name' => '',
            'github' => '',
            'lat' => 0,
            'lon' => 0,
            'description' => '',
            'type' => '',
            'tags' => array(
                'mentor' => [],
                'apprentice' => [],
            ),
            'thumbnail' => '',
            'id' => '',
            'githubUid' => '',
        );

        $user['id'] = $entry->id;
        $user['name'] = $entry->name;
        echo sprintf('parsing user %1$s' . "\n", $user['name']);
        $user['githubUid'] = $entry->githubUid;
        $user['description'] = $entry->profile_markdown;
        if ($entry->isMentee && $entry->isMentor) {
            $user['type'] = 'both';
        } else if ($entry->isMentee) {
            $user['type'] = 'apprentice';
        } else if ($entry->isMentor){
            $user['type'] = 'mentor';
        }

        foreach ($entry->mentorTags as $tag) {
            $user['tags']['mentor'][] = $tag->description;
        }

        foreach ($entry->apprenticeTags as $tag) {
            $user['tags']['apprentice'][] = $tag->description;
        }

        $user['thumbnail'] = $entry->imageUrl;

        if (! $user['github']) {
            try {
                $user['github'] = $this->getUserInfoFromGithubId($user['githubUid']);
            } catch (\Exception $e) {
                return $user;
            }
        }

        if (! $user['github']) {
            return $user;
        }

        $userInfo = $this->getUserInfoFromGithub($user['github']);

        if (! isset($userInfo['location'])) {
            return $user;
            // Return when no location can be retrieved.
        }

        $user['location'] = $userInfo['location'];

        $geo = $this->getLatLonForLocation($user['location']);
        if ($geo) {
            $user['lat'] = $geo['lat'];
            $user['lon'] = $geo['lon'];
        }

        return $user;
    }

    /**
     * Get the informations for a user via the GitHub-API
     *
     * @param string $user
     *
     * @return array|mixed
     */
    protected function getUserInfoFromGithub($user)
    {
        $config = $this->getConfig();


        $ch = curl_init('https://api.github.com/users/' . $user);//Here is the file we are downloading, replace spaces with %20
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            sprintf('Authorization: token %s', $config['github_access_token']),
            'User-Agent: php.ug-checkForPHPMentoring - For more information contact info@php.ug',
        ));
        $info = curl_exec($ch); // get curl response
        curl_close($ch);

        try {
            return Json::decode($info, Json::TYPE_ARRAY);
        }catch(Exception $e) {
            return array('location' => '');
        }
    }

    /**
     * Get the user-info from a Github-ID
     *
     * @param int $githubId
     *
     * @throws \Exception
     * @return array|mixed
     */
    public function getUserInfoFromGithubId($githubId)
    {
        $config = $this->getConfig();

        $ch = curl_init('https://api.github.com/users?per_page=1&since=' . ($githubId - 1));//Here is the file we are downloading, replace spaces with %20
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            sprintf('Authorization: token %s', $config['github_access_token']),
            'User-Agent: php.ug-checkForPHPMentoring - For more information contact info@php.ug',
        ));
        $info = curl_exec($ch); // get curl response
        curl_close($ch);

        $info = Json::decode($info, Json::TYPE_ARRAY);

        if (! isset($info[0])) {
            throw new \Exception('Transport Error occured');
        }
        return $info[0]['login'];
    }

    /**
     * Get Geocordiantes for a given location
     *
     * This returns an array containing the keys 'lat' and 'lon'
     *
     * @param string $location
     *
     * @return array|bool
     */
    protected function getLAtLonForLocation($location)
    {
        $ch = curl_init('http://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($location));
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $info = curl_exec($ch); // get curl response
        curl_close($ch);

        $info = Json::decode($info, Json::TYPE_ARRAY);
        if (! $info) {
            return false;
        }

        return array('lat' => $info[0]['lat'], 'lon' => $info[0]['lon']);
    }

    /**
     * Get the configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
} 