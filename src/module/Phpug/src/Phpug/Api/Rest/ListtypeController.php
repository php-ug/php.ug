<?php
/**
 * Copyright (c) 2011-2012 Andreas Heigl<andreas@heigl.org>
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
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category  php.ug
 * @package   Phpug
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2011-2012 php.ug
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     06.03.2012
 * @link      http://github.com/heiglandreas/php.ug
 */

namespace Phpug\Api\Rest;

use Zend\Mvc\Controller\AbstractRestfulController;
use Doctrine\ORM\EntityManager;
use Phpug\Entity\Usergroup;

/**
 * The Controller for de default actions
 *
 * @category  php.ug
 * @package   Phpug
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2011-2012 php.ug
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     05.12.2012
 * @link      http://github.com/heiglandreas/php.ug
 */
class ListtypeController extends AbstractRestfulController
{
    /**
    * Store the EntityManager
    *
    * @var EntityManager $em
    */
    protected $em = null;

    /**
     * Get the EntityManager for this Controller
     *
     * @return MapController
     */
    protected function getEntityManager()
    {
        if (null === $this->em) {
	        $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
	    }
   		return $this->em;
    }
    
    public function get($id)
    {
        $adapter  = $this->getAdapter();
        $response = $this->getResponse();
        $content  = array();
         
        $id    = $this->getEvent()->getRouteMatch()->getParam('id');
        $types = $this->getEntityManager()->getRepository('Phpug\Entity\Grouptype')->findBy(array('id' => $id));
        if (! $types) {
            $content['error'] = 'No group with that ID available';
            $response->setContent($adapter->serialize($content));
            return $response;
        }
        
        if (1 < count($types)) {
            $content['error'] = 'more than one group with that ID available';
            $response->setContent($adapter->serialize($content));
            return $response;
        }
        
        $content['error'] = null;
        $content['list']   = $types[0]->toArray();
        $currentUser = $this->getServiceLocator()->get('OrgHeiglHybridAuthToken');
        $acl = $this->getServiceLocator()->get('acl');
        $role = $this->getServiceLocator()->get('roleManager')->setUserToken($currentUser);
        foreach ($types[0]->getUsergroups() as $group) {
            $currentGroup = $group->toArray();
            $countryCache = $this->getServiceLocator()->get('Phpug\Cache\CountryCode');
            $countryCache->setUserGroup($group);
            unset($currentGroup['caches']);
            $currentGroup['country'] = $countryCache->getCache()->getCache();
            if (Usergroup::ACTIVE == $group->getState()) {
                $content['groups'][] = $currentGroup;
                continue;
            }
            if ($acl && $acl->isAllowed((string) $role, 'ug', 'edit')) {
                $content['groups'][] = $currentGroup;
                continue;
            }
        }
        $response->setContent($adapter->serialize($content));
        return $response;   
    } 
    
    public function create($values)
    {
        $adapter  = $this->getAdapter();
        $response = $this->getResponse();
        $content  = array();
        $response->setContent($adapter->serialize($content));
        return $response;   
    }
    
    public function update($id, $values) 
    {
        $adapter  = $this->getAdapter();
        $response = $this->getResponse();
        $content  = array();
        $response->setContent($adapter->serialize($content));
        return $response;   
    }
    
    public function delete($id)
    {
        $adapter  = $this->getAdapter();
        $response = $this->getResponse();
        $content  = array();
        $response->setContent($adapter->serialize($content));
        return $response;   
    }
    
    public function getList()
    {
        $groups = $this->getEntityManager()->getRepository('Phpug\Entity\Grouptype')->findAll();
        $content = array ();
        foreach ($groups as $group) {
            $content[] = array(
                        'name' => $group->getName(),
                        'id'   => $group->getId(),
                        'description' => $group->getDescription(),
                    );
        }
        
        $adapter = $this->getAdapter();
        $response = $this->getResponse();
        $response->setContent($adapter->serialize($content));
        return $response;
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
}
