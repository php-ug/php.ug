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
            ),
            'invokables' => array(
                'usersGroupAssertion' => 'Phpug\Acl\UsersGroupAssertion',
                'contactsRow'   => 'Phpug\View\Helper\ContactsRow',
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
				'Phpug\Controller\IndexController' => 'Phpug\Controller\IndexController',
				'Phpug\Controller\Map'   => '\Phpug\Controller\MapController',	
                'Phpug\Controller\UsergroupController' => 'Phpug\Controller\UsergroupController',
			    'Phpug\Api\Rest\ListtypeController' => 'Phpug\Api\Rest\ListtypeController',
			    'Phpug\Api\Rest\Listtype' => '\Phpug\Api\Rest\ListtypeController',
			    'Phpug\Api\Rest\Usergroup' => 'Phpug\Api\Rest\UsergroupController',
			    'Phpug\Api\v1\Usergroup' => 'Phpug\Api\v1\UsergroupController',
			),
		),
        'view_helpers'    => array(
            'invokables' => array(
                'showForm'      => 'Phpug\View\Helper\ShowForm',
                'tbElement'     => 'Phpug\View\Helper\TBElement',
                'contactsRow'   => 'Phpug\View\Helper\ContactsRow',
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
				),
				'template_path_stack' => array(
						__DIR__ . '/../view',
				),
                'strategies' => array(
                    'ViewJsonStrategy',
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
);
