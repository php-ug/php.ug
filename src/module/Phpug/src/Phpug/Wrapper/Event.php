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
 * @since     02.12.14
 * @link      https://github.com/heiglandreas/
 */

namespace Phpug\Wrapper;


class Event 
{

    protected $content = array();

    public static function factory($item)
    {
        $event = new self();
        if ($item->SUMMARY) {
            $event->setName($item->SUMMARY->getValue());
        }
        if ($item->DTSTART) {
            $event->setStartDAte($item->DTSTART->getDateTime());
        }

        if ($item->DTEND) {
            $event->setEndDate($item->DTEND->getDateTime());
        }

        if ($item->DESCRIPTION) {
            $event->setDescription($item->DESCRIPTION->getValue());
        }
              //->setUrl($item->URL->getValue())

        return $event;
    }

    public function setName($name)
    {
        $this->content['name'] = $name;

        return $this;
    }

    public function getName()
    {
        return $this->content['name'];
    }

    public function setStartDate($date)
    {
        $this->content['dtstart'] = $date;

        return $this;
    }

    public function getStartDate()
    {
        return $this->content['dtstart'];
    }

    public function setEndDate($date)
    {
        $this->content['dtend'] = $date;

        return $this;
    }

    public function getEndDate()
    {
        return $this->content['dtend'];
    }

    public function setDescription($description)
    {
        $this->content['description'] = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->content['description'];
    }

    public function setUrl($url)
    {
        $this->content['url'] = $url;

        return $this;
    }

    public function getUrl()
    {
        if (! isset($this->content['url'])) {
            return '';
        }

        return $this->content['url'];
    }


}