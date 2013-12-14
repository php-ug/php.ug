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
 * @since     08.10.13
 * @link      https://github.com/heiglandreas/
 */

namespace PhpugTest\Service;

use Phpug\Service\AclFactory;
use DoctrineORMModuleTest\Util\ServiceManagerFactory;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;
use Mockery;

class AclFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testAclFactory()
    {
        $this->markTestSkipped('Errors in mocking');
//        $serviceLocator = ServiceManagerFactory::getServiceManager(); // see comment below
        $serviceLocator = $this->getServiceManager();

        $config = new \stdClass();
        $config->acl = array(
            'roles'=> array(
                'foo'    => null,
                'foobar' => 'foo',
                'admin'  => null,
            ),
            'resources'=>array(
                'allow' => array(
                    'all' => array(
                        'all' => 'admin',
                    ),
                    'bar' => array(
                        'baz' => 'foo',
                    ),
                    'baz' => array(
                        'foobaz' => 'foobar,foo',
                    ),
                    'bazbar' => array(
                        'bazfoo' => array(
                            'role' => 'foo',
                            'assert' => 'usersGroupAssertion',
                        )
                    ),

                ),
            ),
            'admins' => array('foo'),
        );

        $serviceLocator->setAllowOverride(true);
        // replacing connection service with our fake one
        $serviceLocator->setService('config', $config);

        $assertion = Mockery::mock('Zend\\Permissions\\Acl\\Assertion\\AssertionInterface')
            ->shouldReceive(null)
            ->andReturn('true')
        //    ->mock()
        ;
        $serviceLocator->setService('usersGroupAssertion', $assertion);
        $factory = new AclFactory();

        $acl = $factory->createService($serviceLocator);
        $this->assertInstanceof('Zend\\Permissions\\Acl\\Acl', $acl);
        $this->assertTrue($acl->isAllowed('foo', 'bar', 'baz'));
        $this->assertTrue($acl->isAllowed('foobar', 'bar', 'baz'));
        $this->assertTrue($acl->isAllowed('foobar', 'baz', 'foobaz'));
        $this->assertTrue($acl->isAllowed('foo', 'bazbar', 'bazfoo'));


    }

    public function getServiceManager()
    {
        $serviceManager = new ServiceManager(
            new ServiceManagerConfig(
                array()
            )
        );

        return $serviceManager;
    }

}
