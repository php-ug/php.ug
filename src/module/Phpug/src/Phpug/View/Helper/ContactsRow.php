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

use Zend\Form\Element;
use Zend\Form\View\Helper\FormElement;
use Zend\Form\View\Helper\FormElementErrors;
use Zend\Form\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\View;

class ContactsRow extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * Storage of the ServiceLocator
     *
     * @var ServiceLocatorInterface $serviceLocator
     */
    protected $serviceLocator = null;

    /**
     * The view helper
     *
     * @var Zend\View\ViewHelper
     */
    protected $viewHelper = null;

    /**
     * Outputs message depending on flag
     *
     * @return string
     */
    public function __invoke(Element $element)
    {

        return $this->render($element);

    }

    public function render(Element $element)
    {
        $formElement = $this->getElementHelper();
        $formElementErrors = new FormElementErrors();
        $output = sprintf(
            '<li>%1$s%2$s</li>',
            $this->renderElement($element->get('service')),
            $this->renderElement($element->get('name'))
        );

        return $output;
    }

    protected $elementHelper = null;

    protected function getElementHelper()
    {
        if ($this->elementHelper) {
            return $this->elementHelper;
        }
        if (method_exists($this->view, 'plugin')) {
            $this->elementHelper = $this->view->plugin('form_element');
        }

        if (!$this->elementHelper instanceof FormElement) {
            $this->elementHelper = new FormElement();
        }

        if ($this->hasTranslator()) {
            $this->elementHelper->setTranslator(
                $this->getTranslator(),
                $this->getTranslatorTextDomain()
            );
        }

        return $this->elementHelper;
    }

    /**
     * Set the element helper.
     *
     * This has to be an instance of FormElement
     *
     * @param FormElement $elementHelper
     *
     * @return self
     */
    public function setElementHelper(FormElement $elementHelper)
    {
        $this->elementHelper = $elementHelper;

        return $this;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return self
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set the view helper
     *
     * @param View $viewHelper
     *
     * @erturn ContactsRow
     */
    public function setViewHelper(View $viewHelper)
    {
        $this->viewHelper = $viewHelper;

        return $this;
    }

    /**
     * Get the viewHelper
     *
     * @return View
     */
    public function getViewHelper()
    {
        return $this->viewHelper;
    }

    public function renderElement(Element $element)
    {
        $element->setLabelAttributes(array('class' => 'sr-only'));
        $element->setAttribute('class', 'form-control');
        $labelHelper = $this->view->plugin('form_label');
        $elementHelper = $this->getElementHelper();
        $elementXhtml = '%1$s';
        if ($element->getMessages() && ! $element instanceof \Zend\Form\Element\Collection) {
            $elementXhtml = '<div class="input-group"><span data-toggle="tooltip" data-placement="bottom" class="input-group-addon" data-html="true" title="%2$s"><i class="fa-warning fa"></i></span>%1$s</div>';
        }
        $output = '<div class="form-group%3$s">%1$s%2$s</div>';
        return sprintf(
            $output,
            $labelHelper($element),
            sprintf(
                $elementXhtml,
                $elementHelper($element),
                $this->view->formElementErrors($element)
            ),
            $element->getMessages()?' has-error':''
        );
    }
}