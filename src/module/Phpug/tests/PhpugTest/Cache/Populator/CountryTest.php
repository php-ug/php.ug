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
 * @since     12.02.14
 * @link      https://github.com/heiglandreas/
 */

namespace PhpugTest\Cache\Populator;

use Phpug\Cache\Populator\Country;
use Mockery as M;

class CountryTest extends \PHPUnit_Framework_TestCase
{
    public function testActualCaching()
    {
        $return = M::mock('stdObject');
        $return->shouldReceive('getCountry')
            ->andReturn('foo');
        $Geocoding = M::mock('stdObject');
        $Geocoding->shouldReceive('reverse')
            ->once()
            ->andReturn($return);

        $sm = M::mock('\Zend\ServiceManager\ServiceManager');
        $sm->shouldReceive('get')
            ->with('Phpug\Service\Geocoder')
            ->andReturn($Geocoding);

        $ug = M::mock('Phpug\Entity\Usergroup');
        $ug->shouldReceive('getLatitude')->once()->andReturn('a');
        $ug->shouldReceive('getLongitude')->once()->andReturn('b');

        $m = new Country();

        $this->assertEquals('foo', $m->populate($ug, $sm));
    }

    public function testCachingWithThrownException()
    {
        $Geocoding = M::mock('stdObject');
        $Geocoding->shouldReceive('reverse')->andThrow('Exception');
        $sm = M::mock('\Zend\ServiceManager\ServiceManager');
        $sm->shouldReceive('get')
            ->with('Phpug\Service\Geocoder')
            ->andReturn($Geocoding);

        $ug = M::mock('Phpug\Entity\Usergroup');
        $ug->shouldReceive('getLatitude')->once()->andReturn('a');
        $ug->shouldReceive('getLongitude')->once()->andReturn('b');


        $m = new Country();

        $this->assertEquals('', $m->populate($ug, $sm));
    }

    public function testGettingType()
    {
        $testClass = new Country();

        $this->assertEquals('country', $testClass->getType());
    }

}
 