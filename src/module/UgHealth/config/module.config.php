<?php
/**
 * Copyright (c) 2016-2016} Andreas Heigl<andreas@heigl.org>
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2016-2016 Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     08.01.2016
 * @link      http://github.com/heiglandreas/php.ug
 */
return array(
    'usergroupHealthPlugins' => array(
        'UgHealth\HttpStatus',
        'UgHealth\IcalendarState',
        'UgHealth\TwitterStatus',
    ),
    'service_UgHealth\Controller\manager' => array(
        'invokables' => array(
            'UgHealth\HttpStatus' => 'UgHealth\HttpStatus',
            'UgHealth\IcalendarState' => 'UgHealth\IcalendarState',
            'UgHealth\TwitterStatus' => 'UgHealth\TwitterStatus',
        ),
        'factories' => array(
            'TwitterHelper' => 'UgHealth\Service\TwitterHelperService',
        ),
    ),
    'console' => [
        'router' => [
            'routes' => [
                'usergroup-healthcheck-twitter' => [
                    'options' => [
                        'route' => 'checkhealth:twitter <usergroup>',
                        'defaults' => [
                            '__NAMESPACE__' => 'UgHealth\Controller',
                            'controller' => 'TwitterHealthController',
                            'action' => 'twitter',
                        ]
                    ]
                ],
                'usergroup-healthcheck-website' => [
                    'options' => [
                        'route' => 'checkhealth:website <usergroup>',
                        'defaults' => [
                            '__NAMESPACE__' => 'UgHealth\Controller',
                            'controller' => 'WebsiteHealthController',
                            'action' => 'website',
                        ]
                    ]
                ]
            ]
        ]
    ],
    'controllers' => array(
        'factories' => array(
            'UgHealth\Controller\TwitterHealthController' => 'UgHealth\Service\TwitterHealthControllerFactory',
            'UgHealth\Controller\WebsiteHealthController' => 'UgHealth\Service\WebsiteHealthControllerFactory',
        ),
    ),
    'service_manager' => [
        'factories' => [
            'TwitterHelper' => 'UgHealth\Service\TwitterHelperService',
            'GuzzleHelper'  => 'UgHealth\Service\GuzzleHelperService',
        ],
    ],
);
