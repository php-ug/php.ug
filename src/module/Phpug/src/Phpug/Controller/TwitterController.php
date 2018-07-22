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
 * @since     13.05.14
 * @link      https://github.com/heiglandreas/
 */

namespace Phpug\Controller;

use Doctrine\ORM\EntityManager;
use Phpug\Parser\Mentoring;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Http\Client as HttpClient;
use Phpug\Service\Twitter;

class TwitterController extends AbstractActionController
{
    protected $em;

    protected $config;

    public function __construct(EntityManager $em, array $config)
    {
        $this->em = $em;
        $this->config = $config;
    }

    /**
     * Parse the phpmentoring.org-page for apprentices and mentors
     *
     * This method is the endpoint for the console-action.
     *
     * @return void
     */
    public function getUgListAction()
    {
        $twitterConf = array(
            'access_token'        => array(
                'token'  => $this->config['access_token'],
                'secret' => $this->config['access_token_secret'],
            ),
            'oauth_options'       => array(
                'consumerKey'    => $this->config['consumer_key'],
                'consumerSecret' => $this->config['consumer_key_secret'],
            ),
            'http_client_options' => array(
                'adapter'     => 'Zend\Http\Client\Adapter\Curl',
                'curloptions' => array(
                    //           CURLOPT_SSL_VERIFYHOST => false,
                    //           CURLOPT_SSL_VERIFYPEER => false,
                ),
            ),
        );

        $twitter = $this->em->getRepository('Phpug\Entity\Service')->findBy(array('name' => 'Twitter'));
        $twitters = $this->em->getRepository('Phpug\Entity\Groupcontact')->findBy(array('service' => $twitter[0]->id));

        $result = array();
        $i = 0;
        foreach ($twitters as $twitter) {
            $result[floor($i++/100)][] = $twitter->getName();
        }

        $twitter = new Twitter($twitterConf);

        $count = count($result);
        $info = array();
        for ($i = 0; $i < $count; $i++) {
            $res = $twitter->usersLookup($result[$i]);
            $info = array_merge($info, Json::decode($res->getRawResponse(), Json::TYPE_ARRAY));
        }

        echo Json::encode($info);
    }
}
