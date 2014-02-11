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

namespace PhpugTest\Cache;


use Phpug\Cache\Country;
use Phpug\Entity\Cache;
use Mockery as M;

class CountryTest extends \PHPUnit_Framework_TestCase
{

    protected $ug;

    protected $sm;

    protected $country;

    public function setup()
    {
        $this->ug = M::mock('\Phpug\Entity\Usergroup');
        $this->sm = M::mock('\Zend\ServiceManager\ServiceManager');
        $this->country = new Country();
        $this->country->setUsergroup($this->ug);
        $this->country->setServiceManager($this->sm);
    }

    public function testSettingUsergroup()
    {
        $country = new Country();
        $this->assertAttributeEmpty('usergroup', $country);
        $this->assertSame($country, $country->setUsergroup($this->ug));
        $this->assertAttributeEquals($this->ug, 'usergroup', $country);
    }

    public function testSettingServiceManager()
    {
        $country = new Country();
        $this->assertAttributeEmpty('serviceManager', $country);
        $this->assertSame($country, $country->setServiceManager($this->sm));
        $this->assertAttributeEquals($this->sm, 'serviceManager', $country);
    }

    public function testGettingCacheEntity()
    {
        $mockConfig = array('phpug'=> array('entity'=>array('cache'=>array('country'=>array('cacheLifeTime'=>'P1M')))));
        $mockCache  = M::mock('\Phpug\Entity\Cache');
        $mockCache->shouldReceive('setType')
                  ->andReturn($mockCache);
        $mockCache->shouldReceive('setGroup')
                  ->andReturn($mockCache);
        $mockCache->shouldReceive('add');
        $mockCache->shouldReceive('getLastChangeDate')
                  ->andReturn(new \DateTime());

        $sm = M::mock('\Zend\ServiceManager\ServiceManager');
        $sm->shouldReceive('get')
           ->with('Phpug\Entity\Cache')
           ->andReturn($mockCache);
        $sm->shouldReceive('get')
           ->with('config')
           ->andReturn($mockConfig);

        $ug = M::mock('\Phpug\Entity\Usergroup', array('foo'))
            ->shouldReceive('getCaches')
            ->once()
            ->andReturn(array())
            ->mock();

        $country = new Country();
        $country->setUsergroup($ug);
        $this->assertSame($country, $country->setServiceManager($sm));
        $this->assertAttributeSame($sm, 'serviceManager', $country);
        $this->assertSame($mockCache, $country->getCache());
    }

    public function testGettingCacheEntityWithInvalidCache()
    {
        $mockConfig = array('phpug'=> array('entity'=>array('cache'=>array('country'=>array('cacheLifeTime'=>'P1M')))));
        $mockCache  = M::mock('\Phpug\Entity\Cache');
        $mockCache->shouldReceive('setType')
            ->andReturn($mockCache);
        $mockCache->shouldReceive('setGroup')
            ->andReturn($mockCache);
        $mockCache->shouldReceive('add');
        $mockCache->shouldReceive('getLastChangeDate')
            ->andReturn((new \DateTime())->sub(new \DateInterval('P1M1D')));
        $mockCache->shouldReceive('setCache');
        $mockCache->shouldReceive('setLastChangeDate');

        $mockGeocode = M::mock('stdObject');
        $mockGeocode->shouldReceive('getCountry')
                    ->once()
                    ->andReturn('string');
        $mockGeocoder = M::mock('\Geocoder\Geocoder');
        $mockGeocoder->shouldReceive('reverse')
                     ->once()
                     ->with(0,0)
                     ->andReturn($mockGeocode);

        $mockEm = M::mock('stdObject');
        $mockEm->shouldReceive('persist')->once();
        $mockEm->shouldReceive('flush')->once();

        $sm = M::mock('\Zend\ServiceManager\ServiceManager');
        $sm->shouldReceive('get')
            ->with('Phpug\Entity\Cache')
            ->andReturn($mockCache);
        $sm->shouldReceive('get')
            ->with('config')
            ->andReturn($mockConfig);
        $sm->shouldReceive('get')
           ->with('Phpug\Service\Geocoder')
           ->andReturn($mockGeocoder);
        $sm->shouldReceive('get')
           ->with('doctrine.entitymanager.orm_default')
           ->andReturn($mockEm);

        $ug = M::mock('\Phpug\Entity\Usergroup', array('foo'))
            ->shouldReceive('getCaches')
            ->once()
            ->andReturn(array())
            ->mock();
        $ug->shouldReceive('getLatitude')
           ->once()
           ->andReturn(0);
        $ug->shouldReceive('getLongitude')
           ->once()
           ->andReturn(0);



        $country = new Country();
        $country->setUsergroup($ug);
        $this->assertSame($country, $country->setServiceManager($sm));
        $this->assertAttributeSame($sm, 'serviceManager', $country);
        $this->assertSame($mockCache, $country->getCache());
    }

    public function testGettingExistingCacheEntity()
    {
        $mockConfig = array('phpug'=> array('entity'=>array('cache'=>array('country'=>array('cacheLifeTime'=>'P1M')))));
        $mockCache  = M::mock('\Phpug\Entity\Cache');
        $mockCache->shouldReceive('setType')
            ->andReturn($mockCache);
        $mockCache->shouldReceive('getType')
            ->andReturn('country');
        $mockCache->shouldReceive('setGroup')
            ->andReturn($mockCache);
        $mockCache->shouldReceive('add');
        $mockCache->shouldReceive('getLastChangeDate')
            ->andReturn(new \DateTime());

        $mockCache2 = M::mock('\Phpug\Entity\Cache');
        $mockCache2->shouldReceive('getType')
                   ->andReturn('foo');

        $sm = M::mock('\Zend\ServiceManager\ServiceManager');
        $sm->shouldReceive('get')
            ->with('Phpug\Entity\Cache')
            ->andReturn($mockCache);
        $sm->shouldReceive('get')
            ->with('config')
            ->andReturn($mockConfig);

        $ug = M::mock('\Phpug\Entity\Usergroup', array('foo'))
            ->shouldReceive('getCaches')
            ->once()
            ->andReturn(array($mockCache2, $mockCache))
            ->mock();

        $country = new Country();
        $country->setUsergroup($ug);
        $this->assertSame($country, $country->setServiceManager($sm));
        $this->assertAttributeSame($sm, 'serviceManager', $country);
        $this->assertSame($mockCache, $country->getCache());
    }

    public function testInstantiation()
    {
        $m = new Country($this->ug, $this->sm);
        $this->assertInstanceof('\Phpug\Cache\CacheInterface', $m);
        $this->assertInstanceof('\Phpug\Cache\Country', $m);
        $this->assertAttributeSame($this->ug, 'usergroup', $m);
        $this->assertAttributeSame($this->sm, 'serviceManager', $m);

    }

    public function testActualCaching()
    {
        $return = M::mock('stdObject');
        $return->shouldReceive('getCountry')
               ->andReturn('foo');
        $Geocoding = M::mock('stdObject');
        $Geocoding->shouldReceive('reverse')
                  ->once()
                  ->andReturn($return);

        $mockEm = M::mock('stdObject');
        $mockEm->shouldReceive('persist')->once();
        $mockEm->shouldReceive('flush')->once();

        $sm = M::mock('\Zend\ServiceManager\ServiceManager');
        $sm->shouldReceive('get')
            ->with('Phpug\Service\Geocoder')
            ->andReturn($Geocoding);

        $sm->shouldReceive('get')
            ->with('doctrine.entitymanager.orm_default')
            ->andReturn($mockEm);

        $this->ug->shouldReceive('getLatitude')->once()->andReturn('a');
        $this->ug->shouldReceive('getLongitude')->once()->andReturn('b');

        $m = new Country($this->ug, $sm);

        $cache = M::mock('Phpug\Entity\Cache');
        $cache->shouldReceive('setCache')
              ->with('foo')
              ->once()
              ->andReturn($cache);
        $cache->shouldReceive('setCache');
        $cache->shouldReceive('setLastChangeDate');


        $method = \UnitTestHelper::getMethod($m, 'populateCache');
        $result = $method->invoke($m, $cache);
        $this->assertSame($cache,$result);
    }
}
 