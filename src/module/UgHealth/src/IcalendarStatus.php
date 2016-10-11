<?php
/**
 * Copyright (c) 2016-2016} Andreas Heigl<andreas@heigl.org>
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
 * @copyright 2016-2016 Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     08.01.2016
 * @link      http://github.com/heiglandreas/php.ug
 */

namespace UgHealth;

use GuzzleHttp\Client;
use Phpug\Entity\Usergroup;
use Sabre\VObject\Component\VEvent;
use Sabre\VObject\Node;
use Sabre\VObject\Reader;

class IcalendarStatus implements UsergroupHealthPluginInterface
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function check(Usergroup $usergroup)
    {
        $icalendarUri = $usergroup->getIcalendar_url();

        if (empty($icalendarUri)) {
            return self::UNKNOWN;
        }

        try {
            $response = $this->client->get($icalendarUri);
        } catch (Exception $e) {
            var_Dump($e->getMessage());
            return self::UNKNOWN;
        }
        if ($response->getStatusCode() >= 400) {
            return self::UNKNOWN;
        }

        $events = [];
        $now = new \DateTimeImmutable();
        $then = $now->add(new \DateInterval('P1Y'));
        try {
            $ical = Reader::read($response->getBody()->getContents());
            $ical = $ical->expand($now, $then);
            foreach ($ical->children() as $event) {
                if (!$event instanceof VEvent) {
                    continue;
                }

                $result = $event->validate(Node::REPAIR);
                if ($result) {
                    error_log(print_r($result, true));
                    continue;
                }
                if ($event->isInTimeRange($now, $then)) {
                    $events[] = $event;
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            return self::UNKNOWN;
        }


        if (0 < count($events)) {
            return self::ACTIVE;
        }

        return self::STALE;
    }

    public function getName()
    {
        return 'iCalendarStatus';
    }
}
