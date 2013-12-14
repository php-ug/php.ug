<?php
/**
 * Copyright (c)2013-2013 heiglandreas
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
 * @copyright ©2013-2013 Andreas Heigl
 * @license   http://www.opesource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     30.10.13
 * @link      https://github.com/heiglandreas/
 */

namespace PhpugTest\Acl;


use Phpug\Acl\RoleManager;
use Mockery as M;

class RoleManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var RoleManager $roleManager
     */
    protected $roleManager = null;

    public function setUp()
    {
        $this->roleManager = new RoleManager();
        $this->roleManager->setAdmins(array(
            'foo' => array('bar', 'baz'),
            'bza' => array('foobar', 'foobaz'),
        ));
    }

    /**
     * @param $auth
     * @param $username
     * @param $service
     * @param $result
     *
     * @dataProvider validRoleRetrievalProvider
     */
    public function testValidRoleRetrieval($auth, $username, $service, $result)
    {
        $user = M::mock('OrgHeiglHybridAuth\UserToken')
                ->shouldReceive('isAuthenticated')->zeroOrMoreTimes()->andReturn($auth)
                ->shouldReceive('getService')->zeroOrMoreTimes()->andReturn($service)
                ->shouldReceive('getDisplayName')->zeroOrMoreTimes()->andReturn($username)
                ->mock();
        $this->roleManager->setUserToken($user);
        $this->assertEquals($result, $this->roleManager->getRole());
        $this->assertEquals($result, (string) $this->roleManager);
    }

    public function validRoleRetrievalProvider()
    {
        return array(
            array(true, 'baz', 'foo', 'admin'),
            array(false, 'baz', 'foo', 'guest'),
            array(true, 'baz', 'fooo', 'member'),
            array(true, 'bza', 'foobar', 'member'),
        );
    }
}
 