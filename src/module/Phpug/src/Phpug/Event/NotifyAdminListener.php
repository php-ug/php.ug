<?php
/**
 * Copyright (c) Andreas Heigl<andreas@heigl.org>
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @since     07.10.2016
 * @link      http://github.com/heiglandreas/php.ug
 */

namespace Phpug\Event;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;

class NotifyAdminListener implements ListenerAggregateInterface
{
    protected $listeners = array();

    protected $message;

    protected $transport;

    public function __construct(Message $message, TransportInterface $transport)
    {
        $this->message = $message;
        $this->transport = $transport;
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('notifyAdmin', array($this, 'doEvent'));
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function doEvent(EventInterface $event)
    {
        $this->message->setBody(sprintf(
            $this->message->getBody(),
            $event->getParam('name'),
            $event->getParam('shortname')
        ));
        $this->message->setSubject(sprintf(
            $this->message->getSubject(),
            $event->getParam('name')
        ));

        $this->transport->send($this->message);
    }
}
