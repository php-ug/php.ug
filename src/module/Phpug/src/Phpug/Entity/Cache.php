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
 * @since     05.12.2012
 * @link      http://github.com/heiglandreas/php.ug
 * @ORM\Entity
 * @ORM\Table(name="cache")
 * @property \DateTime $lastChangeDate
 * @property text $cache
 * @property string $type
 * @property Usergroup $usergroup
 */
class Cache
{
    /**
    * @ORM\Id
    * @ORM\Column(type="integer");
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $lastChangeDate;

    /**
     * @ORM\Column(type="text")
     */
    protected $cache;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\ManyToOne(targetEntity="Usergroup", inversedBy="caches")
     */
    protected $usergroup;

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
     * Set the cache for this Object
     *
     * @param string $cache
     *
     * @return self
     */
    public function setCache($cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Get the content of this cache-entry
     *
     * @return string
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set the datetime of this cache-entry
     *
     * @param DateTime
     *
     * @return self
     */
    public function setLastChangeDate(\DateTime $lastChangeDate)
    {
        $this->lastChangeDate = $lastChangeDate;

        return $this;
    }

    /**
     * Get the last change date
     *
     * @return \DateTime
     */
    public function getLastChangeDate()
    {
        if (! $this->lastChangeDate instanceof \DateTime) {
            return new \DateTime('@0');
        }
        return $this->lastChangeDate;
    }

    /**
     * Set the cache-type
     *
     * @param string type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = (string) $type;

        return $this;
    }

    /**
     * Get the cache-type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the id
     *
     * @param int $id
     *
     * @return Groupcontact
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the ID of this cache
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the group
     *
     * @param Usergroup $group
     *
     * @return self
     */
    public function setGroup(Usergroup $group)
    {
        $this->usergroup = $group;

        return $this;
    }

    /**
     * Get the group
     *
     * @return Usergroup
     */
    public function getGroup()
    {
        return $this->usergroup;
    }

}
