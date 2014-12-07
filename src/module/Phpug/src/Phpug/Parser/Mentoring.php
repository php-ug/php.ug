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
class Mentoring
{

    /**
     * @var DOMObject $dom
     */
    protected $dom;

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
    public function parse($file)
    {
        $return = array('mentors' => array(), 'apprentices' => array());

        $content = file_Get_contents($file);
        $content = str_Replace('<local-time', '<span tag="local-time"', $content);
        $content = str_Replace('</local-time', '</span', $content);
        $content = str_Replace('<time', '<span tag="time"', $content);
        $content = str_Replace('</time', '</span', $content);

        $this->dom = new \DomDocument('1.0', 'UTF-8');
        $this->dom->strictErrorChecking = false;
        libxml_use_internal_errors(true);
        $this->dom->loadHTML('<?xml encoding="UTF-8" ?>' . $content);
        libxml_use_internal_errors(false);

        $xpathMentors = new \DOMXPath($this->dom);
        $mentors = $xpathMentors->query('//a[@id="user-content-mentors-currently-accepting-an-apprentice"]/../following-sibling::ul[1]/li');

        foreach ($mentors as $mentor) {
            $user = $this->parseUser($mentor);
            if (! $user) {
                continue;
            }
            $user['type'] = 'mentor';
            $return['mentors'][] = $user;
        }

        $xpathApprentices = new \DOMXPath($this->dom);
        $apprentices = $xpathApprentices->query('//a[@id="user-content-apprentices-currently-accepting-mentors"]/../following-sibling::ul[1]/li');

        foreach ($apprentices as $apprentice) {
            $user = $this->parseUser($apprentice);
            if (! $user) {
                continue;
            }
            $user['type'] = 'apprentice';
            $return['apprentices'][] = $user;
        }

        return $return;
    }

    /**
     * Parse the DOMElement for the actual user-information
     *
     * @param \DOMElement $userNode
     *
     * @return array
     */
    protected function parseUser(\DOMElement $userNode)
    {
        $user = array(
            'name' => '',
            'github' => '',
            'lat' => 0,
            'lon' => 0,
            'description' => '',
            'type' => '',
        );

        if ($userNode->getElementsByTagName('del')->length != 0) {
            return false;
        }

        $text = $userNode->firstChild->textContent;
        if (! preg_match('/([^\(]+)\(.*\)(.*)/', $text, $results)) {
            return false;
        }
        $user['name'] = trim($results[1]);
        $user['description'] = trim($results[2]);

        echo sprintf('parsing user %1$s' . "\n", $user['name']);

        $githubPath = new \DOMXPath($this->dom);
        $githubs = $githubPath->query('.//a[contains(@href,"github.com")]', $userNode);
        if ($githubs->length == 0) {
            return false;
        }

        $user['github'] = $githubs->item(0)->getAttribute('href');
        $user['github'] = substr($user['github'], strrpos($user['github'], '/')+1);

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