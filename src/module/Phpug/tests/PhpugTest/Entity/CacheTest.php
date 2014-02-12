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

namespace PhpugTest\Entity;

use Phpug\Entity\Cache;
use Mockery as M;

class CacheTest extends \PHPUnit_Framework_TestCase
{

    public function testSettingId()
    {
        $entity = new Cache();
        $this->assertSame($entity, $entity->setId(12));
        $this->assertAttributeEquals(12, 'id', $entity);
        $this->assertSame(12, $entity->getId());
    }

    public function testSettingType()
    {
        $entity = new Cache();
        $this->assertSame($entity, $entity->setType('foo'));
        $this->assertAttributeEquals('foo', 'type', $entity);
        $this->assertSame('foo', $entity->getType());
    }

    public function testSettingLastChangeDate()
    {
        $entity = new Cache();
        $date = new \DateTime();
        $this->assertSame($entity, $entity->setLastChangeDate($date));
        $this->assertAttributeEquals($date, 'lastChangeDate', $entity);
        $this->assertSame($date, $entity->getLastChangeDate());
    }

    public function testGEttingUnsetLastChangeDate()
    {
        $entity = new Cache();
        $this->assertAttributeEquals(null, 'lastChangeDate', $entity);
        $this->assertEquals(new \DateTime('@0'), $entity->getLastChangeDate());

    }

    public function testSettingCache()
    {
        $entity = new Cache();
        $this->assertSame($entity, $entity->setCache('foo'));
        $this->assertAttributeEquals('foo', 'cache', $entity);
        $this->assertSame('foo', $entity->getCache());

    }

    public function testSettingUsergroups()
    {
        $entity = new Cache();
        $usergroup = M::mock('\Phpug\Entity\Usergroup');

        $this->assertSame($entity, $entity->setGroup($usergroup));
        $this->assertAttributeEquals($usergroup, 'usergroup', $entity);
        $this->assertSame($usergroup, $entity->getGroup());
    }

}
 