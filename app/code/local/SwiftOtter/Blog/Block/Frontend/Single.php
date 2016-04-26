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
 * Copyright: 2015 (c) SwiftOtter Studios
 *
 * @author Jesse Maxwell
 * @copyright Swift Otter Studios, 5/13/15
 * @package default
 **/

class SwiftOtter_Blog_Block_Frontend_Single extends SwiftOtter_Base_Block_Template
{
    public function getPost()
    {
        if ($post = $this->getData('post')) {
            return $post;
        } else {
            return Mage::registry('current_post');
        }
    }

    /**
     * Returns true if the page is not a child of a list of posts
     *
     * @return bool
     */
    public function getIsSingle()
    {
        if (!in_array($this->getParentBlock()->getType(), Mage::helper('SwiftOtter_Blog')->getListTypes())) {
            return true;
        }
    }

    public function getCacheKeyInfo()
    {
        $output = parent::getCacheKeyInfo();
        if ($this->getPost()) {
            $output['id'] = $this->getPost()->getId();
        }

        return $output;
    }
}