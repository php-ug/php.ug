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

namespace Phpug\Acl;


class RoleManager
{
    /**
     * @var \OrgHeiglHybridAuth\UserTokenInterface $user
     */
    protected $user = null;

    /**
     * @var array $admins
     */
    protected $admins = array();

    /**
     * The default role
     *
     * @var string $defaultRole
     */
    protected $defaultRole = 'guest';

    /**
     * The role to be used for logged in users
     *
     * @var string $loggedInRole
     */
    protected $loggedInRole = 'member';

    /**
     * The role for admin-users
     *
     * @var string $adminRole
     */
    protected $adminRole = 'admin';

    /**
     * Set the current user
     *
     * @param \OrgHeiglHybridAuth\UserTokenInterface $user
     *
     * @return RoleManager
     */
    public function setUserToken(\OrgHeiglHybridAuth\UserTokenInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Set the default role
     *
     * @param string $defaultRole
     */
    public function setDefaultRole($defaultRole)
    {
        $this->defaultRole = $defaultRole;

        return $this;
    }

    /**
     * Set the role for logged in users
     *
     * @param string $role
     *
     * @return RoleManager
     */
    public function setLoggedInRole($role)
    {
        $this->loggedInRole = $role;

        return $this;
    }

    /**
     * Set the rolename for admin users
     *
     * @param string $role
     *
     * @return RoleManager
     */
    public function setAdminRole($role)
    {
        $this->adminRole = $role;

        return $this;
    }

    /**
     * Set the Admin-users
     * The given array has to contain the service-name as key and an array with
     * usernames for that service as value
     *
     * @param array $admins
     *
     * @return RoleManager
     */
    public function setAdmins(array $array)
    {
        $this->admins = $array;

        return $this;
    }

    /**
     * Get the role of the currently set user
     *
     * @return string
     */
    public function getRole()
    {
        if (! $this->user->isAuthenticated()) {
            return $this->defaultRole;
        }

        if (! array_key_Exists($this->user->getService(), $this->admins)) {
            return $this->loggedInRole;
        }

        if (! in_array($this->user->getDisplayName(), $this->admins[$this->user->getService()])) {
            return $this->loggedInRole;
        }

        return $this->adminRole;
    }

    /**
     * Magic method to return the role.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getRole();
    }

} 