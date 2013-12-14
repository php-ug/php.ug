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
 * @since     07.10.13
 * @link      https://github.com/heiglandreas/
 */

namespace Phpug\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

class AclFactory implements FactoryInterface
{
    const DEFAULT_ROLE = 'guest';
    /**
     * Create the service using the configuration from the modules config-file
     *
     * @param ServiceLocator $services The ServiceLocator
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     * @return Hybrid_Auth
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $config = $config['acl'];

        if (!isset($config['roles']) || !isset($config['resources'])) {
            throw new \Exception('Invalid ACL Config found');
        }

        $roles = $config['roles'];
        if (!isset($roles[self::DEFAULT_ROLE])) {
            $roles[self::DEFAULT_ROLE] = '';
        }

        $this->admins = $config['admins'];
        if (! isset($this->admins)) {
            throw new \UnexpectedValueException('No admin-user set');
        }

        $acl = new Acl();

        foreach ($roles as $name => $parent) {
            if (!$acl->hasRole($name)) {
                if (empty($parent)) {
                    $parent = array();
                } else {
                    $parent = explode(',', $parent);
                }

                $acl->addRole(new Role($name), $parent);
            }
        }

        foreach ($config['resources'] as $permission => $controllers) {
            foreach ($controllers as $controller => $actions) {
                if ($controller == 'all') {
                    $controller = null;
                } else {
                    if (!$acl->hasResource($controller)) {
                        $acl->addResource(new Resource($controller));
                    }
                }

                foreach ($actions as $action => $role) {
                    if ($action == 'all') {
                        $action = null;
                    }
                    $assert = null;
                    if (is_array($role)) {
                        $assert = $serviceLocator->get($role['assert']);
                        $role   = $role['role'];
                    }

                    $role = explode(',', $role);

                    foreach( $role as $roleItem) {
                        if ($permission == 'allow') {
                            $acl->allow($roleItem, $controller, $action, $assert);
                        } elseif ($permission == 'deny') {
                            $acl->deny($roleItem, $controller, $action, $assert);
                        } else {
                            continue;
                        }
                    }
                }
            }
        }

        return $acl;
    }
}