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

class TBElement extends AbstractHelper
{

    /**
     * Outputs message depending on flag
     *
     * @return string
     */
    public function __invoke(Element $element)
    {
        $element->setLabelAttributes(array('class' => 'control-label'));
        $element->setAttribute('data-placement', 'bottom')
                ->setAttribute('data-content', $element->getOption('description'));
        if (!$element->getLabel()) {
            $element->setLabel('öö');
        }

        $output  = '<div class="control-group">';
        $output .= $this->getView()->formLabel($element);
        $output .= '<div class="controls">';
        $output .= $this->getView()->formElement($element);
        if ($element->getOption('description')) {
            $output.= '<div class="description">' . $element->getOption('description') . '</div>';
        }
        if ($element->getMessages()) {
            $output .= '<div class="alert alert-error">'
                     . $this->getView()->formElementErrors($element)
                     . '</div>';

        }
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }
} 