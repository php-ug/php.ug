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
 * @since     04.02.14
 * @link      https://github.com/heiglandreas/
 */

namespace Phpug\Api\v1;

use Zend\Mvc\Controller\AbstractActionController;
use Sabre\VObject;
use Zend\Json\Json;

class UsergroupController extends AbstractActionController
{
    private $acceptCriteria = array(
        'Zend\View\Model\JsonModel' => array(
            'application/json',
            'text/html',
        ),
        'Zend\View\Model\FeedModel' => array('application/rss+xml'),
    );



    public function nextEventAction()
    {
        $adapter = $this->getAdapter();
        $response = $this->getResponse();
        $viewModel =  $this->getViewModel();

        Json::$useBuiltinEncoderDecoder = true;

        $id    = $this->getEvent()->getRouteMatch()->getParam('id');
        if (! $id) {
            return $response->setContent($adapter->serialize(null));
        }

        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $result = $em->getRepository('Phpug\Entity\Usergroup')->findBy(array('shortname' => $id));
        if (! $result) {
            throw new \UnexpectedValueException(sprintf(
                'Fehler!!'
            ));
        }

        if (1 < count($result)) {
            throw new \UnexpectedValueException(sprintf(
                'Fehler!!'
            ));
        }
        $uri = $result[0]->icalendar_url;

        $data = @file_get_contents($uri);
        if (! $data) {
            throw new \UnexpectedValueException(sprintf(
                'Could not read data'
            ));
        }
        $now = new \DateTime();
        $then = (new \DateTime())->add(new \DateInterval('P1Y'));
        $ical = VObject\Reader::read($data);
        $ical->expand($now, $then);
        $nextEvent = null;
        if (!isset($ical->VEVENT)) {
            throw new \UnexpectedValueException(sprintf(
                'No Event available'
            ));
        }
        foreach ($ical->VEVENT as $event) {
            if (null === $nextEvent || $nextEvent->DTSTART->getDateTime() > $event->DTSTART->getDateTime()) {
                $nextEvent = $event;
            }
        }
        if (! $nextEvent) {
            throw new \UnexpectedValueException(sprintf(
                'No Event defined'
            ));
        }
        $content = array(
            'start' => $nextEvent->DTSTART->getDateTime()->format(\DateTime::RFC2822),
            'end'   => $nextEvent->DTEND->getDateTime()->format(\DateTime::RFC2822),
            'location' => '',
            'summary'  => 'Next event',
            'url'      => '',
            'description' => '',
        );
        if (isset($nextEvent->LOCATION)) {
            $content['location'] = $nextEvent->LOCATION->getValue();;
        }
        if (isset($nextEvent->SUMMARY)) {
            $content['summary'] = $nextEvent->SUMMARY->getValue();
        }
        if (isset($nextEvent->URL)) {
            $content['url'] = $nextEvent->URL->getValue();
        }
        if (isset($nextEvent->DESCRIPTION)) {
            $content['description'] = $nextEvent->DESCRIPTION->getValue();;
        }

        return $viewModel->setVariables($content);
    }

    protected function getAdapter()
    {
        $format = $this->getEvent()->getRouteMatch()->getParam('format');
        switch ($format) {
            case 'sphp':
                $contentType = 'text/plain';
                $adapter = '\Zend\Serializer\Adapter\PhpSerialize';
                break;
            case 'json':
            default:
                $contentType = 'application/json';
                $adapter = '\Zend\Serializer\Adapter\Json';
                break;
        }
        $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', $contentType);

        return new $adapter;
    }

    protected function getViewModel()
    {
        return $this->acceptableViewModelSelector($this->acceptCriteria);
    }
}