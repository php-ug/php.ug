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

namespace Phpug\Mvc\Renderer;

use Phpug\View\Model\IcalendarModel;
use Zend\View\Renderer\RendererInterface;

class IcalendarRenderer implements RendererInterface
{
    /**
     * @var \Zend\View\Resolver\ResolverInterface
     */
    protected $resolver;

    /**
     * Return the template engine object, if any
     * If using a third-party template engine, such as Smarty, patTemplate,
     * phplib, etc, return the template engine object. Useful for calling methods
     * on these objects, such as for setting filters, modifiers, etc.
     *
     * @return \Phpug\Mvc\Renderer\IcalendarRenderer
     */
    public function getEngine()
    {
        return $this;
    }
    /**
     * Set the resolver used to map a template name to a resource the renderer may consume.
     * @param \Zend\View\Resolver\ResolverInterface $oResolver
     * @return \Phpug\Mvc\Renderer\IcalendarRenderer
     */
    public function setResolver(\Zend\View\Resolver\ResolverInterface $oResolver)
    {
        $this->resolver = $oResolver;
        return $this;
    }

    /**
     * Renders values as Icalendar
     *
     * @param string|\Zend\View\Model\ModelInterface $oNameOrModel : the script/resource process, or a view model
     * @param null|array|\ArrayAccess $aValues : values to use during rendering
     *
     * @throws \DomainException
     * @return string The script output.
     */
    public function render($oNameOrModel, $aValues = null)
    {
        // Use case 1: View Models
        // Serialize variables in view model
        if (! $oNameOrModel instanceof IcalendarModel) {
            throw new \DomainException(__METHOD__ . ': Do not know how ' .
               'to handle operation when both $oNameOrModel and $aValues are populated');
        }

        return $oNameOrModel->serialize();
    }
}
