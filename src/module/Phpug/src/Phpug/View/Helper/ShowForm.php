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

class ShowForm extends AbstractHelper
{

    protected $submitElements = array();
    /**
     * Outputs message depending on flag
     *
     * @return string
     */
    public function __invoke(Form $form)
    {
        $form->prepare();

        $output = $this->getView()->form()->openTag($form);


        foreach ($form as $element) {
            $output .= $this->renderElement($element);
        }

        $output .= '<div class="form-group"><div class="col-sm-2"></div><div class="col-sm-2">';
        foreach ($this->submitElements as $element) {
            $element->setAttribute('class', 'form-control btn-primary btn-lg');
            $output .= $this->getView()->formElement($element) . '&nbsp;';
        }
        $output .= '</div></div>';

        $output .= $this->getView()->form()->closeTag();

        return $output;

    }

    protected function renderElement($element)
    {
        $output = '';
        if ($element instanceof Submit) {
            $this->submitElements[] = $element;
        } else if ($element instanceof Button && 'submit' === $element->getAttribute('type')) {
            $this->submitElements[] = $element;
        } else if ($element instanceof Csrf
            || $element instanceof Hidden
        ) {
            $output .= $this->getView()->formElement($element);
        } else if ($element instanceof \Zend\Form\Element\Collection) {
//            $output .= $this->getView()->formCollection($element);
            $output .= $this->getElement($element);
        } else if ($element instanceof \Zend\Form\Fieldset) {
            $output .= sprintf('<fieldset id="%s"><legend>%s</legend>', $element->getName(), $element->getAttribute('legend'));
            foreach($element as $item) {
                $output .= $this->renderElement($item);
            }
            $output .= '</fieldset>';
        } else {
            $output .= $this->getElement($element);
        }

        return $output;
    }

    /**
     * @param Zend\Form\Element $element
     *
     * @return string
     */
    protected function getElement(Element $element)
    {
        $element->setLabelAttributes(array('class' => 'control-label col-sm-2'));
        $element->setAttribute('data-placement', 'bottom')
                ->setAttribute('class', 'form-control')
                ->setAttribute('data-content', $element->getOption('description'))
                ->setAttribute('data-trigger', 'focus')
        ;
        if (!$element->getLabel()) {
            $element->setLabel('öö');
        }

        $output  = sprintf(
            '<div class="form-group%1$s">',
            $element->getMEssages() && ! $element instanceof \Zend\Form\Element\Collection?' has-error':''
        );
        $output .= $this->getView()->formLabel($element);
        $output .= '<div class="col-sm-10">';
        if ($element->getMessages() && ! $element instanceof \Zend\Form\Element\Collection) {
            $output .= sprintf(
                '<div class="input-group"><span data-toggle="tooltip" data-placement="bottom" class="input-group-addon" data-html="true" title="%1$s"><i class="fa-warning fa"></i></span>',
                $this->getView()->formElementErrors($element)
            );
        }
        if ($element instanceof \Zend\Form\Element\Collection) {
            $output .= $this->getView()->formCollection($element);
        } else {
            $output .= $this->getView()->formElement($element);
        }

        if ($element->getMessages()) {
            $output .= '</div>'
            ;

        }
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }
} 