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
class UsergroupController extends AbstractRestfulController
{
    /**
    * Store the EntityManager
    *
    * @var EntityManager $em
    */
    protected $em;

    protected $roleManager;

    protected $assertion;

    protected $acl;

    public function __construct(EntityManager $em, RoleManager $roleManager, UsersGroupAssertion $assertion, Acl $acl)
    {
        $this->em = $em;
        $this->roleManager = $roleManager;
        $this->assertion = $assertion;
        $this->acl = $acl;
    }
    
    public function get($id)
    {
        $adapter  = $this->getAdapter();
        $response = $this->getResponse();
        $content  = array();
         
        $id    = $this->getEvent()->getRouteMatch()->getParam('id');
        $types = $this->em->getRepository('Phpug\Entity\Usergroup')->findBy(array('id' => $id));
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
        $content['group'] = $types[0]->toArray();

        $this->assertion->setGroup($types[0]);

        if ($this->acl->isAllowed($this->roleManager, 'ug', 'edit')) {
            $content['edit'] = true;
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
        $groups = $this->getEntityManager()->getRepository('Phpug\Entity\Usergroup')->findAll();
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
