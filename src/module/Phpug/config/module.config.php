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
namespace Phpug;

return array(
    'di' => array(
        'definition' => array(
            'class' => array(
                'Zend\Mvc\Router\RouteStack' => array(
                    'instantiator' => array(
                        'Zend\Mvc\Router\Http\TreeRouteStack',
                        'factory'
                    ),
                ),
            ),
        ),
        'instance' => array(
            'orm_driver_chain' => array('parameters' => array(
                'drivers' => array(
                    'Usergroup' => array(
                        'class'     => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                        'namespace' => __NAMESPACE__ . '\Entity',
                        'paths'     => array(
                            __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'
                        ),
                    ),
                ),
            )),
            'Phpug\Controller\MapController' => array(
                'parameters' => array(
                    'em' => 'doctrine_em',
                ),
            ),
            'Phpug\Controller\IndexController' => array(
                'parameters' => array(
                    'em' => 'doctrine_em',
                ),
            ),
            // Inject the plugin broker for controller plugins into
            // the action controller for use by all controllers that
            // extend it.
            'Zend\Mvc\Controller\ActionController' => array(
                'parameters' => array(
                    'broker'       => 'Zend\Mvc\Controller\PluginBroker',
                ),
            ),
            'Zend\Mvc\Controller\PluginBroker' => array(
                'parameters' => array(
                    'loader' => 'Zend\Mvc\Controller\PluginLoader',
                ),
            ),

            // Setup the View layer
            'Zend\View\Resolver\AggregateResolver' => array(
                'injections' => array(
                    'Zend\View\Resolver\TemplateMapResolver',
                    'Zend\View\Resolver\TemplatePathStack',
                ),
            ),
            'Zend\View\Resolver\TemplateMapResolver' => array(
                'parameters' => array(
                    'map'  => array(
                        'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
                    ),
                ),
            ),
            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths'  => array(
                        'application' => __DIR__ . '/../view',
                    ),
                ),
            ),
            'Zend\View\Renderer\PhpRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\Resolver\AggregateResolver',
                ),
            ),
            'Zend\Mvc\View\DefaultRenderingStrategy' => array(
                'parameters' => array(
                    'layoutTemplate' => 'layout/layout',
                ),
            ),
            'Zend\Mvc\View\ExceptionStrategy' => array(
                'parameters' => array(
                    'displayExceptions' => true,
                    'exceptionTemplate' => 'error/index',
                ),
            ),
            'Zend\Mvc\View\RouteNotFoundStrategy' => array(
                'parameters' => array(
                    'displayNotFoundReason' => true,
                    'displayExceptions'     => true,
                    'notFoundTemplate'      => 'error/404',
                ),
            ),

            // Setup the router and routes
            'Zend\Mvc\Router\RouteStack' => array(
                'parameters' => array(
                    'routes' => array(
                        'default' => array(
                            'type'    => 'Zend\Mvc\Router\Http\Segment',
                            'options' => array(
                                'route'    => '/:ugid',
                                'constraints' => array(
                                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                ),
                                'defaults' => array(
                                    'controller' => 'Phpug\Controller\IndexController',
                                    'action'     => 'redirect',
                                ),
                            ),
                        ),
                        'home' => array(
                            'type' => 'Zend\Mvc\Router\Http\Literal',
                            'options' => array(
                                'route'    => '/',
                                'defaults' => array(
                                    'controller' => 'Phpug\Controller\IndexController',
                                    'action'     => 'index',
                                ),
                            ),
                        ),
                        'poi' => array(
                            'type' => 'Zend\Mvc\Router\Http\Literal',
                            'options' => array(
                                'route'    => '/m/map/poi',
                                'defaults' => array(
                                    'controller' => 'Phpug\Controller\MapController',
                                    'action'     => 'poi',
                                ),
                            ),
                        ),
                        'imprint' => array(
                            'type' => 'Zend\Mvc\Router\Http\Literal',
                            'options' => array(
                                'route'    => '/m/index/imprint',
                                'defaults' => array(
                                    'controller' => 'Phpug\Controller\IndexController',
                                    'action'     => 'imprint',
                                ),
                            ),
                        ),
                        'about' => array(
                            'type' => 'Zend\Mvc\Router\Http\Literal',
                            'options' => array(
                                'route'    => '/m/about',
                                'defaults' => array(
                                    'controller' => 'Phpug\Controller\IndexController',
                                    'action'     => 'about',
                                ),
                            ),
                        ),
                        'team' => array(
                            'type' => 'Zend\Mvc\Router\Http\Literal',
                            'options' => array(
                                'route'    => '/m/team',
                                'defaults' => array(
                                    'controller' => 'Phpug\Controller\IndexController',
                                    'action'     => 'team',
                                ),
                            ),
                        ),
                        'mailproxy' => array(
                            'type' => 'Zend\Mvc\Router\Http\Literal',
                            'options' => array(
                                'route'    => '/m/mailproxy',
                                'defaults' => array(
                                    'controller' => 'Phpug\Controller\IndexController',
                                    'action'     => 'mailproxy',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
