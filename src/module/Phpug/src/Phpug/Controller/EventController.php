<?php
/**
 * Copyright (c) 2011-2012 Andreas Heigl<andreas@heigl.org>
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
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category  php.ug
 * @package   Phpug
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2011-2012 php.ug
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     06.03.2012
 * @link      http://github.com/heiglandreas/php.ug
 */

namespace Phpug\Controller;

use Phpug\Form\PromoteUsergroupForm;
use Zend\Mvc\View\Http\ViewManager;
use Zend\View\Helper\ViewModel;

use Zend\Mvc\Controller\AbstractRestfulController;


/**
 * The Controller for de default actions
 *
 * @category  php.ug
 * @package   Phpug
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2011-2012 php.ug
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     06.03.2012
 * @link      http://github.com/heiglandreas/php.ug
 */
class EventController extends AbstractRestfulController
{

    protected $config = null;

    public function getList()
    {
        $response = $this->getResponse();
        $adapter = $this->getAdapter();
        $url = 'http://api.joind.in/v2.1/events?filter=upcoming&verbose=yes&resultsperpage=100';

        $client = new \Zend\Http\Client();
        $req = new \Zend\Http\Request();
        $req->setUri($url)->setMethod('GET');


        $clientResponse = $client->dispatch($req);

        if (! $clientResponse->isSuccess()) {
            throw new \UnexpectedValueException('Foo');
        }

        $content = json_decode($clientResponse->getContent());

        $response->setContent($adapter->serialize($content));

        return $response;

    }

    protected function getAdapter()
    {
        $format = $this->getEvent()->getRouteMatch()->getParam('format');
        switch ($format) {
            case 'sphp':
                $contentType = 'text/plain';
                $adapter = '\Zend\Serializer\Adapter\PhpSerialize';
                break;
            case 'json':
            default:
                $contentType = 'application/json';
                $adapter = '\Zend\Serializer\Adapter\Json';
                break;
        }
        $this->getResponse()->getHeaders()->addHeaderLine('Content-Type', $contentType);

        return new $adapter;
    }
}