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
 * @since     02.10.13
 * @link      https://github.com/heiglandreas/
 */

namespace Phpug\Acl;

use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use OrgHeiglHybridAuth\UserInterface;
use Phpug\Entity\Usergroup;

class UsersGroupAssertion implements  AssertionInterface
{
    /**
     * The user-object
     *
     * @var UserInterface $user
     */
    protected $user = null;

    /**
     * The group-entity
     *
     * @var Usergroup $group
     */
    protected $group = null;

    /**
     * Set the user-object
     *
     * @param Userinterface $user
     *
     * @return UsersGroupAssertion
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Set the usergroup-object
     *
     * @param Usergroup $group
     *
     * @return UsersGroupAssertion
     */
    public function setGroup(Usergroup $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Assert that the users social nick is associated with this group
     *
     * @param Acl $acl
     * @param RoleInterface $role
     * @param ResourceInterface $resource
     * @param string $privilege
     *
     * @return boolean
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        if (! $this->user) {
            return false;
        }

        if (! $this->group) {
            return false;
        }
        $service = strtolower($this->user->getService());
        $uid     = strtolower($this->user->getDisplayName());

        foreach($this->group->getContacts() as $contact) {
            if (strtolower($contact->getServiceName()) !== $service) {
                continue;
            }
            if (strtolower($contact->name) !== $uid) {
                continue;
            }
            return true;
        }

        return false;
    }
}