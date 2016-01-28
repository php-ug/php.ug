<?php
/**
 * Copyright (c)2013-2013 heiglandreas
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
 * LIBILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category  HybridAuth
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright ©2012-2013 Andreas Heigl
 * @license   http://www.opesource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     11.01.13
 * @link      https://github.com/heiglandreas/
 */
namespace OrgHeiglHybridAuth;

return array('OrgHeiglHybridAuth' => array(
    'hybrid_auth' => array(
        'base_url' => 'http://php.ug.local',
        'providers' => array(
            'Twitter' => array('enabled' => true, 'keys' => array('key' => '[TWITTER_KEY]', 'secret' => '[TWITTER_SECRET]')),
            'Facebook' => array('enabled' => true, 'keys' => array('key' => '', 'secret' => '')),
        ),
        'debug_mode' => true,
        'debug_file' => __DIR__ . '/../../../log/hybrid_auth.log',
    ),
    'session_name' => 'orgheiglhybridauth',

//    'backend'         => 'twitter',
    'backend'         => array('Twitter'),
//        'backend'         => array('Twitter', 'Facebook', '...'),
//    'link'            => '<a class="hybridauth" href="%2$s">%1$s</a>', // Will be either inserted as first parameter into item or simply returned as complete entry
//    'item'            => '<li%2$s>%1$s</li>',
//    'itemlist'        => '<ul%2$s>%1$s</ul>',
//    'logincontainer'  => '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">%1$s<b class="caret"></b></a>%2$s</li>',
//    'logoffcontainer' => '<li>%1$s</li>',
//    'logoffstring'    => 'Logout %1$s',
//    'loginstring'     => 'Login%1$s',
//    'listAttribs'     => null, // Will be inserted as 2nd parameter into item
//    'itemAttribs'     => null, // Will be inserted as 2nd parameter into itemlist

));
