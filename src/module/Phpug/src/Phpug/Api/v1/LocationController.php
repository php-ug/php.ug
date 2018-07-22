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

use Doctrine\ORM\EntityManager;
use Phpug\ORM\Query\AST\Functions\DistanceFrom;
use Zend\Mvc\Controller\AbstractActionController;
use Sabre\VObject;
use Zend\Json\Json;

class LocationController extends AbstractActionController
{
    private $acceptCriteria = array(
        'Zend\View\Model\JsonModel' => array(
            'application/json',
            'text/html',
        ),
        'Zend\View\Model\FeedModel' => array('application/rss+xml'),
    );

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get a list of groups that are nearest to the given coordinates.
     *
     * Coordinates are given via the parameters <var>lat</var and <var>lon</var>,
     * the maximum distance from the current location is given via <var>distance</var>
     * and the maximum number of entries is given via the parameter <var>max</var>
     *
     * This method will then return the maximum number of entries within the given
     * range. If less than the maximum number of entries is found within the distance
     * only that number of entries will be returned. When no distance is given or the
     * distance is given as "0" the maximum number of entries will be retrieved.
     *
     * @return mixed|\Zend\View\Model\ModelInterface
     * @throws \UnexpectedValueException
     */
    public function nextGroupsAction()
    {
        $viewModel =  $this->getViewModel();

        Json::$useBuiltinEncoderDecoder = true;

        // Get Latitude, Longitude, distance and/or number of groups to retrieve
        $latitude  = $this->params()->fromQuery('latitude');
        $longitude = $this->params()->fromQuery('longitude');
        $distance  = $this->params()->fromQuery('distance', null);
        $number    = $this->params()->fromQuery('count', null);

        $groups = $this->findGroupsWithinRangeAndDistance($latitude, $longitude, $distance, $number);
        $return = array(
            'currentLocation' => array(
                'latitude' => $latitude,
                'longitude' => $longitude,
            ),
            'groups' => array(),
        );
       // $hydrator = $this->getServiceManager('Phpug\Hydrator\Usergroup');
        foreach ($groups as $group) {
            $grp = array(
                'name' => $group[0]->getName(),
                'latitude' => $group[0]->getLatitude(),
                'longitude' => $group[0]->getLongitude(),
                'shortname' => $group[0]->getShortname(),
                'distance'  => $group['distance'],
                'icalendar_url' => $group[0]->getIcalendar_url(),
                'url' => $group[0]->getUrl(),
                'contacts' => array(),
                'uri' => '',
            );

            foreach ($group[0]->getContacts() as $contact) {
                $grp['contacts'][] = array(
                    'service' => $contact->getServiceName(),
                    'name' => $contact->getName(),
                    'uri' => $contact->getUrl(),
                );
            }

            $return['groups'][] = $grp;
        }
        $viewModel->setVariable('groups', $return);

        return $viewModel;
    }

    protected function getAdapter()
    {
        $format = $this->params()->fromQuery('format', null);
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

    protected function findGroupsWithinRangeAndDistance($lat, $lon, $distance = null, $number = null)
    {
        DistanceFrom::setLatitudeField('latitude');
        DistanceFrom::setLongitudeField('longitude');
        DistanceFrom::setRadius(6367);
        $this->em->getConfiguration()->addCustomNumericFunction(
            'DISTANCEFROM',
            'Phpug\ORM\Query\AST\Functions\DistanceFrom'
        );

        $qs = 'SELECT p, DISTANCEFROM('
            . (float) $lat
            . ','
            . (float) $lon
            . ') AS distance FROM \Phpug\Entity\Usergroup p WHERE p.state = 1 ';


        if ($distance) {
            $qs .= ' AND DISTANCEFROM(' . (float) $lat . ',' . (float) $lon . ') <= ' . (float) $distance;
        }

        $qs .= ' ORDER BY distance';

        $query = $this->em->createQuery($qs);
        if ($number) {
            $query->setMaxResults($number);
        }

        return $query->getResult();
    }
}
