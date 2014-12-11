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
 * @since     13.05.14
 * @link      https://github.com/heiglandreas/
 */

namespace Phpug\Controller;

use Phpug\Parser\Mentoring;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class CalendarController extends AbstractActionController
{

    public function getcalendarsAction()
    {
        echo sprintf('Getting calendars' . "\n");

        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $q = $em->createQuery('SELECT u FROM Phpug\Entity\Usergroup u WHERE u.icalendar_url IS NOT NULL');

        $result = $q->getResult();

        /** @var \Phpug\Entity\Usergroup $usergroup */
        $i = 0;
        foreach ($q->getResult() as $usergroup) {
            $eventCache = null;
            /** @var \Phpug\Entity\Cache $cache */
            $caches = $usergroup->getCaches();
            foreach ($caches as $cache) {
                if ($cache->type == 'event') {
                    $eventCache = $cache;
                    break;
                }
            }
            if (! $eventCache instanceof \Phpug\Entity\Cache) {
                $eventCache = new \Phpug\Entity\Cache();
                $eventCache->type = 'event';
                $usergroup->addCaches(new \Doctrine\Common\Collections\ArrayCollection(array($eventCache)));
            }
            set_time_limit(30);
            $icalendar = @file_get_contents($usergroup->icalendar_url);
            $eventCache->cache = $icalendar;
            $eventCache->lastChangeDate = new \DateTime();
            echo '.';
            $em->persist($usergroup);
            $em->flush();
            $i++;
        }

        echo sprintf("\n" . 'Updated caches for %s usergroups' . "\n", $i);

    }
}