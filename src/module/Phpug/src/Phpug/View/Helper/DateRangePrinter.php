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
 * @category 
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright ©2013-2013 Andreas Heigl
 * @license   http://www.opesource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     07.11.13
 * @link      https://github.com/heiglandreas/
 */

namespace Phpug\View\Helper;

use Zend\Form\Element\Hidden;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Button;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\View\Helper\AbstractHelper;

class DateRangePrinter extends AbstractHelper
{

    protected $format = 'd.m.Y';

    /**
     * Outputs message depending on flag
     *
     * @param DateTimeInterface $start
     * @param DateTimeInterface $end
     * @param string            $format
     * @param DateTimezone      $timezone
     * @param string            $separator
     *
     *
     * @return string
     */
    public function __invoke($start, $end, $format = null, \DateTimeZone $timezone = null, $separator = ' - ')
    {
        if (null !== $timezone) {
            $start->setTimezone($timezone);
            $end->setTimezone($timezone);
        }

        if (! $start instanceof \DateTimeInterface) {
            return '';
        }

        if (! $end instanceof \DateTimeInterface) {
            return '';
        }

        if (null !== $format) {
            $this->format = $format;
        }

        if ($start->format('Y') != $end->format('Y')) {
            return $start->format('d.m.Y') . $separator . $end->format('d.m.Y');
        }

        if ($start->format('m') != $end->format('m')) {
            return $start->format('d.m.') . $separator . $end->format('d.m.Y');
        }

        if ($start->format('d') != $end->format('d')) {
            return $start->format('d.') . $separator . $end->format('d.m.Y');
        }

        if ($start->format('H') != $end->format('H') || $start->format('i') != $end->format('i')) {
            return $start->format('d.m.Y H:i') . $separator . $end->format('H:i');
        }

        return '';

    }
} 