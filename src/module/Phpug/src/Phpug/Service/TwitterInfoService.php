<?php
/**
 * Copyright (c)2014-2014 heiglandreas
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
 * @copyright ©2014-2014 Andreas Heigl
 * @license   http://www.opesource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     11.07.14
 * @link      https://github.com/heiglandreas/
 */

namespace Phpug\Service;

use Zend\Json\Json;

class TwitterInfoService
{
    protected $infos = array();

    public function __construct($path)
    {
        if (! is_readable($path)) {
            return;
        }


        $cache = Json::decode(file_get_contents($path), Json::TYPE_ARRAY);
        foreach ($cache as $item) {
            $this->infos[$item['screen_name']] = $item;
        }
    }

    public function getInfoForUser($info, $user, $default = '')
    {

        if (! isset($this->infos[$user])) {
            return $default;
        }

        if (! isset($this->infos[$user][$info])) {
            return $default;
        }

        return $this->infos[$user][$info];
    }
}
