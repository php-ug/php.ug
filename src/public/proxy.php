<?php
/**
 * Copyright (c)2013-2013 Andreas Heigl<andreas@heigl.org>
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
 * @category 
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright ©2013-2013 Andreas Heigl
 * @license   http://www.opesource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     03.06.13
 * @link      https://github.com/php_ug/php_ug
 */

$mapping = array(
    'phpug' => '../src/module/Phpug',
    'orgheiglcontact' => 'org_heigl/contact',
    'orgheiglmailproxy' => 'org_heigl/mailproxy',

);

$mimeMapping = array(
    'js'    => 'text/javascript',
    'img'   => 'auto',
    'css'   => 'text/css',
    'fonts' => 'auto',
    'lib'   => 'auto',
);

$allowedBasePath = '/Volumes/Sites/Sites/php.ug';
$allowedPaths = array(
    'vendor/org_heigl/contact/public',
    'vendor/org_heigl/mailproxy/public',
    'src/module/Phpug/public',
);

$endMapping = array(
    'js' => 'text/javascript',
    'css' => 'text/css',
    'svg' => 'application/svg+xml'
);

$filepath = __DIR__
          . DIRECTORY_SEPARATOR
          . '..'
          . DIRECTORY_SEPARATOR
          . '..'
          . DIRECTORY_SEPARATOR
          . 'vendor'
          . DIRECTORY_SEPARATOR
          . '%1$s'
          . DIRECTORY_SEPARATOR
          . 'public'
          . DIRECTORY_SEPARATOR
          . '%2$s'
          . DIRECTORY_SEPARATOR
          . '%3$s'
    ;
$realpath = sprintf($filepath, $mapping[$_REQUEST['module']], $_REQUEST['type'], $_REQUEST['file']);
$realpath = realpath($realpath);

// Filter out hidden files
if (0 === strpos(basename($realpath), '.')) {
    header('HTTP/1.1 404 File Not Found');
    echo 'This is not the page you were looking for';
    exit;
}

// Filter out not existing files
if (! $realpath) {
    header('HTTP/1.1 404 File Not Found');
    echo 'This is not the page you were looking for';
    exit;
}

// Filter out files not in one of the allowed paths
$allowed = false;
foreach ($allowedPaths as $allowedPath) {
    if (0 === strpos($realpath, $allowedBasePath . DIRECTORY_SEPARATOR . $allowedPath)) {
        $allowed = true;
        break;
    }
}
if ($allowed === false) {
    header('HTTP/1.1 404 File Not Found');
    echo 'Who\'s that naughty boy? That request has been logged!';
    exit;
}

// Get the mime-type of the requestet file
$mimeType = $mimeMapping[$_REQUEST['type']];
$ending = substr($realpath, strrpos($realpath, '.')+1);
if (isset($endMapping[$ending])) {
    $mimeType = $endMapping[$ending];
}
if ('auto' === $mimeType) {
    $mime = new finfo(FILEINFO_MIME);
    $mimeType = $mime->file($realpath);
}
//    echo '/*-' . $mimeType . '--*/';

header('Content-Type: ' . $mimeType);
readfile($realpath);


