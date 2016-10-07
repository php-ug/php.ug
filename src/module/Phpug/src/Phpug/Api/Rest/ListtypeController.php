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

use Phpug\Acl\RoleManager;
use Phpug\Acl\UsersGroupAssertion;
use Phpug\Cache\Cache;
use Zend\Mvc\Controller\AbstractRestfulController;
use Doctrine\ORM\EntityManager;
use Phpug\Entity\Usergroup;
use Zend\Permissions\Acl\Acl;

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
    protected $em;

    protected $countryCodeCache;

    protected $groupAssertion;

    protected $role;

    public function __construct(EntityManager $em, Cache $countryCodeCache, Acl $acl, UsersGroupAssertion $assertion, RoleManager $role)
    {
        $this->em = $em;
        $this->countryCodeCache = $countryCodeCache;
        $this->acl = $acl;
        $this->groupAssertion = $assertion;
        $this->role = $role;
    }

    public function get($id)
    {
        $adapter  = $this->getAdapter();
        $response = $this->getResponse();
        $content  = array();
         
        $id    = $this->getEvent()->getRouteMatch()->getParam('id');
        $types = $this->em->getRepository('Phpug\Entity\Grouptype')->findBy(array('id' => $id));
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
        foreach ($types[0]->getUsergroups() as $group) {
            $currentGroup = $group->toArray();
            $this->groupAssertion->setGroup($group);
            if ($this->acl->isAllowed($this->role, 'ug', 'edit')) {
                $currentGroup['edit'] = true;
            }
            $this->countryCodeCache->setUserGroup($group);
            unset($currentGroup['caches']);
            $currentGroup['country'] = $this->countryCodeCache->getCache()->getCache();
            if (Usergroup::ACTIVE == $group->getState()) {
                $content['groups'][] = $currentGroup;
                continue;
            }
            if ($this->acl->isAllowed((string) $this->role, 'ug', 'edit')) {
                $content['groups'][] = $currentGroup;
                continue;
            }
        }
        $response->setContent($adapter->serialize($content));
        $response->getHeaders()
            ->addHeaderLine('Access-Control-Allow-Origin','*')
            ->addHeaderLine('Access-Control-Allow-Methods','POST PUT DELETE GET');

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
        $groups = $this->em->getRepository('Phpug\Entity\Grouptype')->findAll();
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
