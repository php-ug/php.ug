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
 * @since     05.12.2012
 * @link      http://github.com/heiglandreas/php.ug
 */

namespace Phpug\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * The Persistent-Storage Entity
 *
 * @category  php.ug
 * @package   Phpug
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2011-2012 php.ug
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     19.08.2015
 * @link      http://github.com/heiglandreas/php.ug
 * @ORM\Entity
 * @ORM\Table(name="tag")
 * @property string $tagname
 * @property text $description
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $tagname;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\ManyToMany(targetEntity="Usergroup", mappedBy="tags")
     * @var \Doctrine\Common\Collections\ArrayCollection $usergroups
     */
    protected $usergroups;

    public function __construct()
    {
        $this->usergroups = new \Doctrine\Common\Collections\ArrayCollection();
    }
    /**
    * Magic getter to expose protected properties.
    *
    * @param string $property
    * @return mixed
    */
    public function __get($property) {
        return $this->$property;
    }

    /**
     * Magic setter to save protected properties.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value) {
        $this->$property = $value;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray() {
        return array();
    }

    /**
     * Set the tagname for this Object
     *
     * @param string $tagname
     *
     * @return self
     */
    public function setTagname($tagname)
    {
        $this->tagname = $tagname;

        return $this;
    }

    /**
     * Get the name of this tag
     *
     * @return string
     */
    public function getTagname()
    {
        return $this->tagname;
    }

    /**
     * Set the description of this tag
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * add a group to this tag
     *
     * @param Usergroup $group
     *
     * @return self
     */
    public function addGroup(Usergroup $group)
    {
        $this->usergroups->add($group);

        return $this;
    }

    /**
     * Remove the usergroup from this entry
     *
     * @param Usergroup $group
     *
     * @return self
     */
    public function removeGroup(Usergroup $group)
    {

        $this->usergroups->removeElement($group);

        return $this;
    }

    /**
     * Get all groups for this tag
     *
     * @return Usergroup[]
     */
    public function getGroups()
    {
        return $this->usergroups->toArray();
    }

}
