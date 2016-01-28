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
//namespace Phpug;

 return array(
     'service_manager' => array(
         'factories' => array(
             'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
             'footer' => 'Phpug\Navigation\Service\FooterNavigationFactory',
         ),
         'invokables' => array(
             'Zend\Mail\Transport' => 'Zend\Mail\Transport\File',
             'Zend\Mail\TransportOptions' => 'Zend\Mail\Transport\FileOptions',
         ),
     ),
     'php.ug.mentoring' => array(
         'github_access_token' => '[GITHUB_TOKEN]',
     ),
     'php.ug.mentoringapp' => array(
         'github_access_token' => '[GITHUB_TOKEN]',
     ),
     'phpug' => array(
        'notification' => array(
            'message' => array(
                'message' => 'The usergroup %1$s has been promoted and awaits acknowledgement.'."\n\n" . 'You can find it at http://php.ug?center=%2$s',
                'subject' => '[php.ug-AdminAction] Usergroup %1$s has been promoted',
                'from' => array(
                    'name' => 'PHP.ug',
                    'address' => 'info@php.ug',
                ),
                'recipients' => array(
                    array(
                        'name' => 'Andreas Heigl',
                        'address' => 'andreas@heigl.org',
                    ),
                 ),
            ),
            'transport' => array(
                'class' => 'Zend\Mail\Transport',
                'optionclass' => 'Zend\Mail\Transport\FileOptions',
                'options' => array(
                    'path' => '/tmp',
                    'callback' => function(\Zend\Mail\Transport\File $transport) {
                            return 'Message_' . microtime(true) . '_' . mt_rand() . '.txt';
                        },
                ),
            ),
        ),
        'mapquest_api_token' => '[MAPQUEST_API_TOKEN]',
     ),
     'twitter' => array(
         'access_token'        => '[TWITTER_ACCESS_TOKEN]',
         'access_token_secret' => '[TWITTER_ACCESS_TOKEN_SECRET]',
         'consumer_key'        => '[TWITTER_CONSUMER_KEY]',
         'consumer_key_secret' => '[TWITTER_CONSUMER_KEY_SECRET]',
         'cache_path'          => '/Users/Shared/Development/Sites/php.ug/twitter.cache',
     ),
);


