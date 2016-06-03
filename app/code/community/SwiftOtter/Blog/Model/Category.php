<?php
/**
 * SwiftOtter_Base is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SwiftOtter_Base is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with SwiftOtter_Base. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright: 2013 (c) SwiftOtter Studios
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 5/5/15
 * @package default
 **/

class SwiftOtter_Blog_Model_Category extends SwiftOtter_Blog_Model_Abstract
{
    protected $_children = array();
    protected $_level;

    public function __construct()
    {
        $this->_init('SwiftOtter_Blog/Category');
    }

    public function getUrl()
    {
        return Mage::app()->getStore()->getUrl(sprintf('blog/category/%s', $this->getSlug()));
    }

    /**
     * @param $level integer
     */
    public function setLevel($level)
    {
        $this->_level = (int) $level;
    }

    public function getLevel()
    {
        if ($this->_level) {
            return (int) $this->_level;
        }

        return 0;
    }

    public function getChildren()
    {
        return $this->_children;
    }

    public function addChild($child)
    {
        $this->_children[] = $child;
    }

    public function getSpaces()
    {
        $space = "&nbsp;&nbsp;&nbsp;";
        $totalSpaces = "";

        for ($i = 0; $i < $this->getLevel(); $i++) {
            $totalSpaces .= $space;
        }

        return $totalSpaces;
    }
}