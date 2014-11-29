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

namespace Phpug\View\Strategy;


use Phpug\View\Model\IcalendarModel;

class IcalendarStrategy  extends \Zend\EventManager\AbstractListenerAggregate
{
    /**
     * @var \Phpug\Mvc\Renderer\IcalendarRenderer
     */
    protected $renderer;

    protected $charset = 'UTF8';

    /**
     * Constructor
     * @param \Phpug\Mvc\Renderer\XmlRenderer $oRenderer
     */
    public function __construct(\Phpug\Mvc\Renderer\IcalendarRenderer $oRenderer)
    {
        $this->renderer = $oRenderer;
    }

    /**
     * @param \Zend\EventManager\EventManagerInterface $oEventManager
     * @param int $iPriority
     */
    public function attach(\Zend\EventManager\EventManagerInterface $oEventManager, $iPriority = 1)
    {
        $this->listeners[] = $oEventManager->attach(\Zend\View\ViewEvent::EVENT_RENDERER, array($this, 'selectRenderer'), $iPriority);
        $this->listeners[] = $oEventManager->attach(\Zend\View\ViewEvent::EVENT_RESPONSE, array($this, 'injectResponse'), $iPriority);
    }

    /**
     * Detect if we should use the IcalendarRenderer based on model type and/or
     * Accept header
     *
     * @param \Zend\View\ViewEvent $oEvent
     *
     * @return null|\Phpug\Mvc\Renderer\IcalendarRenderer
     */
    public function selectRenderer(\Zend\View\ViewEvent $oEvent)
    {
        $oModel = $oEvent->getModel();
        // No IcalendarModel; do nothing
        if (!$oModel instanceof IcalendarModel) {
            return;
        }

        return $this->renderer;
    }

    /**
     * Inject the response with the Icalendar payload and appropriate Content-Type header
     *
     * @param \Zend\View\ViewEvent $oEvent
     *
     * @return void
     */
    public function injectResponse(\Zend\View\ViewEvent $oEvent)
    {
        $oRenderer = $oEvent->getRenderer();
        if ($oRenderer !== $this->renderer) {
            // Discovered renderer is not ours; do nothing
            return;
        }
        $sResult = $oEvent->getResult();
        if (!is_string($sResult)) {
            // We don't have a string, and thus, no XML
            return;
        }
        // Populate response
        $oResponse = $oEvent->getResponse();
        $oResponse->setContent($sResult);
        $oHeaders = $oResponse->getHeaders();
        $oHeaders->addHeaderLine('content-type', 'text/calendar; charset=' . $this->charset);

    }
}
