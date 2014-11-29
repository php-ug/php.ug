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
 * @since     28.11.14
 * @link      https://github.com/heiglandreas/
 */

namespace Phpug\View\Model;

use Phpug\Wrapper\IcalendarDataWrapper;
use \Zend\View\Model\ViewModel;

class IcalendarModel extends ViewModel
{
    /**
     * XML probably won't need to be captured into a parent container by default.
     * @var string
     */
    protected $captureTo = null;

    /**
     * JSON is usually terminal
     * @var boolean
     */
    protected $terminate = true;

    /**
     * Serialize to iCalendar-Format
     *
     * @return string
     */
    public function serialize()
    {
        $variables = $this->getVariables();

        if ($variables instanceof \Traversable) {
            $variables = \Zend\Stdlib\ArrayUtils::iteratorToArray($variables);
        }

        if (count($variables) == 1) {
            $variable = current($variables);
            if ($variable instanceof IcalendarDataWrapper) {
                return $variable->serialize();
            }
        }

        return false;
    }
}
