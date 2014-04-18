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
 * @since     05.02.14
 * @link      https://github.com/heiglandreas/
 */

namespace Phpug\View\Strategy;

use Phpug\Exception\UnauthenticatedException;
use Phpug\Exception\UnauthorizedException;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Application;
use Zend\Mvc\View\Http\ExceptionStrategy;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 *
 * @since   1.0
 * @author  Tim Roediger <superdweebie@gmail.com>
 */
class UnauthorizedStrategy extends ExceptionStrategy
{

    /**
     * @var $template
     */
    protected $template;

    /**
     * @param string $template name of the template to use on unauthorized requests
     */
    public function __construct($template)
    {
        $this->template = (string) $template;
    }

    /**
     * Attach the aggregate to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            array($this, 'handleExceptions'),
            100
        );
    }

    /**
     * Create an exception json view model, and set the HTTP status code
     *
     * @todo   dispatch.error does not halt dispatch unless a response is
     *         returned. As such, we likely need to trigger rendering as a low
     *         priority dispatch.error event (or goto a render event) to ensure
     *         rendering occurs, and that munging of view models occurs when
     *         expected.
     * @param  MvcEvent $e
     * @return void
     */
    public function handleExceptions(MvcEvent $e)
    {
        $response = $e->getResponse();

        $viewVariables = array(
            'error'      => $e->getParam('error'),
            'identity'   => $e->getParam('identity'),
        );

        $exception = $e->getParam('exception');

        if (! $exception) {
            return true;
        }

        if (! $exception instanceof UnauthenticatedException && ! $exception instanceof UnauthorizedException) {
            return true;
        }

        $viewVariables['header'] = $exception->getHeader();
        $viewVariables['message'] = $exception->getMessage();

        $model    = new ViewModel($viewVariables);
        $response = $response ?: new HttpResponse();

        $model->setTemplate($this->template);
        $e->getViewModel()->addChild($model);
        $response->setStatusCode($exception->getHttpStatusCode());
        foreach ($exception->getAdditionalHeaderFields() as $header => $value) {
            $e->getResponse()->getHeaders()->addHeader($header::fromString($value));
        }

        $e->setResponse($response);
        $e->stopPropagation();

    }
}