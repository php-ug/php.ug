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
 * @since     07.02.14
 * @link      https://github.com/heiglandreas/
 */

namespace Phpug\Cache;

use Phpug\Cache\CacheInterface;
use Phpug\Entity\Cache;
use Phpug\Entity\Usergroup;
use Zend\ServiceManager\ServiceLocatorInterface;

class Country implements CacheInterface
{
    /**
     * Holds the Usergroup for which to fetch cached informations
     *
     * @var Usergroup $usergroup
     */
    protected $usergroup;

    /**
     * The type of cache
     *
     * @var string $type
     */
    protected $type = 'country';

    /**
     * The serviceManager
     *
     * @var ServiceLocatorInterface $serviceManager
     */
    protected $serviceManager;

    /**
     * Get the cache-value
     *
     * @param string $type
     *
     * @return Cache
     */
    public function getCache()
    {
        $caches = $this->usergroup->getCaches();
        $myCache = null;
        foreach($caches as $cache) {
            if ($this->type != $cache->getType()) {
                continue;
            }
            $myCache = $cache;
            break;
        }

        if (! $myCache) {
            $myCache = $this->serviceManager->get('Phpug\Entity\Cache');
            $myCache->setType($this->type);
            $this->usergroup->caches->add($myCache);
            $myCache->setGroup($this->usergroup);
        }

        $config = $this->serviceManager->get('config');
        $cacheLifeTime = $config['phpug']['entity']['cache'][$this->type]['cacheLifeTime'];
        $cacheLifeTime = new \DateInterval($cacheLifeTime);
        if ($myCache->getLastChangeDate()->add($cacheLifeTime) < new \DateTime()) {
            $this->populateCache($myCache);

        }
        return $myCache;
    }

    /**
     * Do the actual Cache-Popularion
     *
     * @var Cache $cache
     *
     * @return Cache
     */
    protected function populateCache(Cache $cache)
    {
        $geocoder = $this->serviceManager->get('Phpug\Service\Geocoder');

        try {
            $geocode = $geocoder->reverse(
                $this->usergroup->getLatitude(),
                $this->usergroup->getLongitude()
            );
            $cache->setCache($geocode->getCountry());

            $cache->setLastChangeDate(new \DateTime());
            $em = $this->serviceManager->get('doctrine.entitymanager.orm_default');
            $em->persist($cache);
            $em->flush();
        }catch(Exception $e)
        {
            //
        }

        return $cache;
    }

    /**
     * Set the usergroup
     *
     * @param Usergroup $usergroup
     *
     * @return self
     */
    public function setUsergroup(Usergroup $usergroup)
    {
        $this->usergroup = $usergroup;

        return $this;
    }

    /**
     * Set the serviceManager
     *
     * @param ServiceManager $serviceManager
     *
     * @return self
     */
    public function setServiceManager(ServiceLocatorInterface $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

    public function __construct(Usergroup $usergroup = null, ServiceLocatorInterface $serviceManager = null)
    {
        if ($usergroup) {
            $this->setUsergroup($usergroup);
        }
        if ($serviceManager) {
            $this->setServiceManager($serviceManager);
        }
    }
}