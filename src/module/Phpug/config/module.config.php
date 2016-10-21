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

use Phpug\Controller\EventCacheController;
use Phpug\Controller\EventControllerFactory;

return array(
    'router' => array(
        'routes' => array(
            'default' => array(
                'type'    => 'Segment',
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
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Phpug\Controller',
                        'controller' => 'IndexController',
                        'action'     => 'index',
                    ),
                ),
            ),
            'noSubdomain' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => 'http://php.ug',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Phpug\Controller',
                        'controller' => 'IndexController',
                        'action'     => 'index',
                    ),
                ),
            ),
            'api' => array(
                'may_terminate' => false,
                'type'          => 'Segment',
                'options'       => array(
                    'route' => '/api',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Phpug\Api',
                        'controller'    => 'Rest\ListtypeController',
                    ),
                ),
                'child_routes' => array(
                    'rest'    => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/rest/:controller[.:format][/:id]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'format'     => '(json|sphp)',
                                'id'         => '[1-9][0-9]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Phpug\Api\Rest',
                                'controller'    => 'ListtypeController',
                                'format'        => 'json',
                            ),
                        ),
                    ),
                    'v1' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/v1/:controller/:action[/:id]',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Phpug\Api\v1',
                                'controller'    => 'UsergroupController',
                                'action'        => 'nextEvent',
                            ),
                        ),
                    ),

                ),
            ),
            'ug' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/ug',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Phpug\Controller',
                        'controller'    => 'IndexController',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller]/[:action]',
                            'constraints'=> array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'IndexController',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'imprint' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route'    => '/imprint',
                            'defaults' => array(
                                'action'     => 'imprint',
                            ),
                        ),
                    ),
                    'legal' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/legal',
                            'defaults' => array(
                                'action'	 => 'legal',
                            ),
                        ),
                    ),
                    'about' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route'    => '/about',
                            'defaults' => array(
                                'action'     => 'about',
                            ),
                        ),
                    ),
                    'team' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route'    => '/team',
                            'defaults' => array(
                                'action'     => 'team',
                            ),
                        ),
                    ),
                    'edit' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/edit/:id',
                            'defaults' => array(
                                'action' => 'edit',
                                'controller' => 'UsergroupController',
                                '__NAMESPACE__' => 'Phpug\Controller',
                            ),
                        ),
                    ),
                    'promote' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/promote',
                            'defaults' => array(
                                'action' => 'promote',
                                'controller' => 'UsergroupController',
                                '__NAMESPACE__' => 'Phpug\Controller',
                            ),
                        ),
                    ),
                    'validate' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/validate',
                            'defaults' => array(
                                'action' => 'validate',
                                'controller' => 'UsergroupController',
                                '__NAMESPACE__' => 'Phpug\Controller',
                            ),
                        ),
                    ),'thankyou' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/thankyou',
                            'defaults' => array(
                                'action' => 'thankYou',
                                'controller' => 'UsergroupController',
                                '__NAMESPACE__' => 'Phpug\Controller',
                            ),
                        ),
                    ),
                    'tips' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/tips',
                            'defaults' => array(
                                'action' => 'tips',
                            ),
                        ),
                    ),
                ),
            ),
            'event' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/event[.:format][/:id]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Phpug\Controller',
                        'controller'    => 'EventController',
                        'format'        => 'json',
                    ),
                ),
            ),
            'mentoring' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/mentoring',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Phpug\Controller',
                        'controller'    => 'MentoringController',
                        'action'        => 'getlist',
                    ),
                ),
                'child_routes' => array(
                    'app' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/app',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Phpug\Controller',
                                'controller' => 'MentoringAppController',
                                'action' => 'getList',
                            ),
                        ),
                    ),
                ),
            ),
            'features' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/feature/:action',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Phpug\Controller',
                        'controller' => 'FeatureController',
                    ),
                ),
            ),
            'subdomain' => array(
                'type' => 'Hostname',
                'options' => array(
                    'route' => ':ugid.php.ug',
                    'defaults' => array(
                        'controller' => 'Phpug\Controller\IndexController',
                        'action' => 'redirect',
                    ),
                ),
            ),
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'getmentoringappjson' => array(
                    'options' => array(
                        'route' => 'getmentoringapp [--json|-j]',
                        'defaults' => array(
                            '__NAMESPACE__' => 'Phpug\Controller',
                            'controller' => 'MentoringAppController',
                            'action' => 'getmentoring'
                        ),
                    ),
                ),
                'getjoindinjson' => array(
                    'options' => array(
                        'route' => 'getjoindin',
                        'defaults' => array(
                            '__NAMESPACE__' => 'Phpug\Controller',
                            'controller'    => 'EventCacheController',
                            'action'        => 'getJoindin'
                        ),
                    ),
                ),
                'gettwitter' => array(
                    'options' => array(
                        'route' => 'gettwitter',
                        'defaults' => array(
                            '__NAMESPACE__' => 'Phpug\Controller',
                            'controller'    => 'TwitterController',
                            'action'        => 'getUgList',
                        ),
                    ),
                ),
                'getugcalendars' => array(
                    'options' => array(
                        'route' => 'getugcalendars',
                        'defaults' => array(
                            '__NAMESPACE__' => 'Phpug\Controller',
                            'controller'    => 'EventCacheController',
                            'action'        => 'getUsergroups',
                        ),
                    ),
                ),
            ),
        )
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Home',
                'route' => 'home',
                'pages' => [
                    array(
                        'label' => 'Usergroups',
                        'route' => 'home',
                        'fragment' => '',
                    ),
                    array(
                        'label' => 'Mentoring-PHP',
                        'route' => 'home',
                        'fragment' => 'mentoring',
                    ),
                    array(
                        'label' => 'Events',
                        'route' => 'home',
                        'fragment' => 'events',
                    ),
                    array(
                        'label' => 'Call for Papers',
                        'route' => 'home',
                        'fragment' => 'cfp',
                    )
                ],
            ),
            array(
                'label' => 'Blog',
                'uri'   => 'http://php-ug.github.io/php.ug/',
            ),
            array(
                'label' => 'Tips & Tricks',
                'route' => 'ug/tips',
            ),
            array(
                'label'  => 'Features',
                'route'  => 'features',
                'action' => 'twitternicklist',
                'pages' => array(
                    array(
                        'label'  => 'Twitter-List',
                        'route'  => 'features',
                        'action' => 'twitternicklist',
                        'icon' => 'fa fa-twitter',
                    ),
                    array(
                        'label' => 'Event-List',
                        'route' => 'api/v1',
                        'action' => 'list',
                        'controller' => 'calendar',
                        'icon'       => 'fa fa-list',
                    ),
                    array(
                        'label'  => 'Event-Calendar',
                        'route'  => 'features',
                        'action' => 'calendar',
                        'icon'   => 'fa fa-calendar',
                    ),
                ),
            ),
            array(
                'label' => 'Include your Usergroup',
                'route' => 'ug/promote',
//                'resource' => 'ug',
//                'privilege' => 'promote',
            )
        ),
        'footer' => array(
            array(
                'label' => 'PHP.ug-Team',
                'route' => 'ug/team',
            ),
            array(
                'label' => 'Imprint',
                'route' => 'ug/imprint',
            ),
            array(
                'label' => 'Contact',
                'route' => 'contact',
            ),
            array(
                'label' => 'Legal',
                'route' => 'ug/legal',
            ),
            array(
                'label' => 'About',
                'route' => 'ug/about',
            ),
            array(
                'label' => 'Slack',
                'uri'   => 'https://phpug.slack.com',
                'pages' => array(
                    array(
                        'label' => 'Usergroup-Team',
                        'uri'   => 'https://phpug.slack.com',
                    ),
                    array(
                        'label' => 'Get an Invitation',
                        'uri'   => 'http://murmuring-forest-7062.herokuapp.com',
                    )
                ),
            ),

        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'acl'        => 'Phpug\Service\AclFactory',
            'roleManager' => 'Phpug\Service\RoleManagerFactory',
            'PromoteUsergroupForm' => 'Phpug\Service\PromoteUsergroupFormFactory',
            'UsergroupFieldset'    => 'Phpug\Service\UsergroupFieldsetFactory',
            'Phpug\Service\UsergroupMessage' => 'Phpug\Service\UsergroupMessageFactory',
            'Phpug\Service\Transport' => 'Phpug\Service\TransportFactory',
            'Phpug\Service\Geocoder' => 'Phpug\Service\GeocoderFactory',
            'Phpug\Cache\Country'     => 'Phpug\Service\CountryCacheFactory',
            'Phpug\Cache\CountryCode'     => 'Phpug\Service\CountryCodeCacheFactory',
            'Phpug\Entity\Cache'  => 'Phpug\Service\CacheFactory',
            'Phpug\Service\Logger'    => 'Phpug\Service\LoggerFactory',
            'TwitterInfoService'      => 'Phpug\Service\TwitterInfoFactory',
            'ViewIcalendarRenderer' => 'Phpug\Mvc\Service\ViewIcalendarRendererFactory',
            'ViewIcalendarStrategy' => 'Phpug\Mvc\Service\ViewIcalendarStrategyFactory',
            \Phpug\Event\NotifyAdminListener::class => \Phpug\Event\NotifyAdminListenerFactory::class,

        ),
        'invokables' => array(
            'usersGroupAssertion'   => 'Phpug\Acl\UsersGroupAssertion',
            'contactsRow'           => 'Phpug\View\Helper\ContactsRow',
            'Phpug\Service\Message' => 'Zend\Mail\Message',
            'Zend\Mail\Transport' => 'Zend\Mail\Transport\File',
        ),
        'shared' => array(
            'Phpug\Cache\Country' => false,
            'Phpug\Cache\CountryCode' => false,
            'Phpug\Entity\Cache'  => false,
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            \Phpug\Controller\FeatureController::class => \Phpug\Controller\FeatureController::class,
        ),
        'factories' => [
            \Phpug\Controller\IndexController::class      => \Phpug\Controller\IndexControllerFactory::class,
            \Phpug\Api\v1\CalendarController::class       => \Phpug\Api\v1\CalendarControllerFactory::class,
            'Phpug\Api\v1\Calendar'                       => \Phpug\Api\v1\CalendarControllerFactory::class,
            \Phpug\Api\v1\LocationController::class       => \Phpug\Api\v1\LocationControllerFactory::class,
            'Phpug\Api\v1\Usergroup'                      => \Phpug\Api\v1\UsergroupControllerFactory::class,
            \Phpug\Api\v1\UsergroupController::class      => \Phpug\Api\v1\UsergroupControllerFactory::class,
            \Phpug\Api\Rest\ListtypeController::class     => \Phpug\Api\Rest\ListtypeControllerFactory::class,
            'Phpug\Api\Rest\Listtype'                     => \Phpug\Api\Rest\ListtypeControllerFactory::class,
            \Phpug\Api\Rest\TwitterController::class      => \Phpug\Api\Rest\TwitterControllerFactory::class,
            'Phpug\Api\Rest\Twitter'                      => \Phpug\Api\Rest\TwitterControllerFactory::class,
            \Phpug\Api\Rest\UsergroupController::class    => \Phpug\Api\Rest\UsergroupControllerFactory::class,
            'Phpug\Api\Rest\Usergroup'                    => \Phpug\Api\Rest\UsergroupControllerFactory::class,
            \Phpug\Controller\EventCacheController::class => \Phpug\Controller\EventCacheControllerFactory::class,
            \Phpug\Controller\EventController::class      => \Phpug\Controller\EventControllerFactory::class,
//            'Phpug\Controller\Map'                       => \Phpug\Controller\MapControllerFactory::class,
            \Phpug\Controller\MentoringAppController::class => \Phpug\Controller\MentoringAppControllerFactory::class,
            \Phpug\controller\TwitterController::class      => \Phpug\Controller\TwitterControllerFactory::class,
            \Phpug\Controller\UsergroupController::class    => \Phpug\Controller\UsergroupControllerFactory::class,
        ],
    ),
    'view_helpers'    => array(
        'invokables' => array(
            'showForm'         => 'Phpug\View\Helper\ShowForm',
            'tbElement'        => 'Phpug\View\Helper\TBElement',
            'contactsRow'      => 'Phpug\View\Helper\ContactsRow',
            'dateRangePrinter' => 'Phpug\View\Helper\DateRangePrinter',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'  => __DIR__ . '/../view/layout/layout.phtml',
            'index/index'    => __DIR__ . '/../view/phpug/index/index.phtml',
            'error/404'      => __DIR__ . '/../view/error/404.phtml',
            'error/index'    => __DIR__ . '/../view/error/index.phtml',
            'partial/navigation' => __DIR__ . '/../view/partial/navigation.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
            'ViewIcalendarStrategy',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        )
    ),
    'php.ug.log' => array(
        'debuglog' => array(
            'location' => getcwd() . '/log/debug.log',
            'handler'  => 'RotatingFile',
            'maxFiles' => 7,
            'level'    => 100,
        )
    ),
    'php.ug.event' => array(
        'url' => 'http://api.joind.in/v2.1/events?filter=upcoming&verbose=yes&resultsperpage=100&tags[]=php',
        'cachefile' => __DIR__ . '/../../../../tmp/joind.in',
    ),
    'php.ug.mentoring' => array(
        'github_access_token' => '',
        'file' => realpath(__DIR__ . '/../../../../tmp/') . '/mentoring.json',
    ),
    'php.ug.mentoringapp' => array(
        'github_access_token' => '',
        'file' => realpath(__DIR__ . '/../../../../tmp') . '/mentoringapp.json',
    ),
    'phpug' => array(
        'entity' => array(
            'cache' => array(
                'country' => array(
                    'cacheLifeTime' => 'P1M',
                ),
                'countrycode' => array(
                    'cacheLifeTime' => 'P1M',
                ),
                'event' => array(
                    'cacheLifeTime' => 'P1W',
                ),
            ),
        ),
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'map' => array(
                'css/phpug' => __DIR__ . '/../public/css',
                'js/phpug' => __DIR__ . '/../public/js',
            ),
            'paths' => array(
                __DIR__ . '/../public',
            ),
        ),
    ),
);
