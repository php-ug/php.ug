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

use Doctrine\Common\Collections\ArrayCollection;
use \DateTime;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;

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
class EventCacheController extends AbstractActionController
{

    protected $config;

    protected $em;

    public function __construct(EntityManager $em, $config)
    {
        $this->em = $em;
        $this->config = $config;
    }

    public function getJoindinAction()
    {
        $config = $this->config;

        $url = $config['php.ug.event']['url'];
        $file = $config['php.ug.event']['cachefile'];
        if (! realpath(dirname($file))) {
            throw new \UnexpectedValueException(sprintf(
                '"%s" does not exist',
                dirname($file)
            ));
        }
        if (! is_writable(realpath(dirname($file)))) {
            throw new \UnexpectedValueException(sprintf(
                '"%s" is not writeable',
                realpath(dirname($file))
            ));
        }

        if (! is_writeable(realpath($file))) {
            throw new \UnexpectedValueException(sprintf(
                '"%s" is not writeable',
                realpath($file)
            ));
        }
        $file = realpath($file);
        echo sprintf('Fetching the joind.in data from "%s"' . "\n", $url);
        $fh = fopen($file, 'w');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $fh);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch); // get curl response
        curl_close($ch);

        fclose($fh);
        echo sprintf('Wrote the joind.in-data to "%s"' . "\n", $file);

        return false;
    }

    public function getUsergroupsAction()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $ugs = $this->em->getRepository('Phpug\Entity\Usergroup')->findAll();
        //createQuery('u')->where('icalendar_url IS NOT NULL')->execute();

        /** @var \Phpug\Entity\Usergroup $ug */
        foreach ($ugs as $ug) {
            $remove = new ArrayCollection();
            $caches = $ug->getCaches();
            /** @var \Phpug\Entity\Cache $cache */
            foreach ($caches as $cache) {
                if ($cache->getType() != 'event') {
                    continue;
                }
                $remove->add($cache);
                $em->remove($cache);
            }
            $ug->removeCaches($remove);
            $iurl = $ug->getIcalendar_url();
            if (! $iurl) {
                $em->persist($ug);
                $em->flush();
                continue;
            }

            echo sprintf('Fetching calendar of group "%s"' . "\n", $ug->getName());


            $add = new ArrayCollection();
            $cal = new \Phpug\Entity\Cache();
            $cal->setType('event');
            $ch = curl_init($ug->getIcalendar_url());
            curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $cache = curl_exec($ch); // get curl response
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);
            if (false === strpos($contentType, 'text/calendar')) {
                $em->persist($ug);
                $em->flush();
                continue;
            }
            $cal->setCache($cache);
            $cal->setLastChangeDate(new DateTime());
            $add->add($cal);
            $ug->addCaches($add);
            $em->persist($ug);
            $em->flush();
        }
    }
}
