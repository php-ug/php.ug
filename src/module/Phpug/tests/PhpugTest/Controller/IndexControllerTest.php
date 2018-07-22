<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace PhpugTest\Controller;

use Doctrine\ORM\EntityManager;
use PhpugTest\Framework\TestCase;
use Phpug\Controller\IndexController;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Mockery as M;
use Zend\Permissions\Acl\Acl;

class IndexControllerTest extends TestCase
{

    /**
     * @var IndexController $controller
     */
    public $controller;
    public $event;
    public $request;
    public $response;
    private $em;
    private $acl;
    
    public function setUp()
    {
        $this->em = M::mock(EntityManager::class);
        $this->acl = M::mock(Acl::class);

        $this->controller = new IndexController($this->em, $this->acl);
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'index'));
        $this->event      = new MvcEvent();
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
    }
    
    public function testInstantiationAction()
    {
        $this->assertAttributeSame($this->em, 'em', $this->controller);
        $this->assertAttributeSame($this->acl, 'acl', $this->controller);
    }
}
