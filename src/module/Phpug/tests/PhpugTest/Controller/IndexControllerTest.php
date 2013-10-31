<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace PhpugTest\Controller;

use PhpugTest\Framework\TestCase;
use Phpug\Controller\IndexController;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;


class IndexControllerTest extends TestCase
{

    /**
     * @var IndexController $controller
     */
    public $controller;
    public $event;
    public $request;
    public $response;
    
    public function setUp()
    {
        $this->controller = new IndexController();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'index'));
        $this->event      = new MvcEvent();
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        
        $this->controller->setServiceLocator($this->getLocator());
    }
    
    public function testSample()
    {
        $this->assertInstanceOf('Zend\Di\LocatorInterface', $this->getLocator());
    }
    
    public function testIndexAction()
    {
        $this->assertAttributeEquals(null, 'em', $this->controller);
        $this->assertInstanceof('Doctrine', $this->controller->getEntityManager());
    }
}
