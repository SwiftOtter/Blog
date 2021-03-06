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
 * @copyright Swift Otter Studios, 5/22/15
 * @package default
 **/

class SwiftOtter_Blog_Block_Frontend_Sidebar extends SwiftOtter_Base_Block_Template
{
    public function getTags()
    {
        return Mage::getResourceModel('SwiftOtter_Blog/Tag_Collection')->setOrder('post_count')->setPageSize(15)->setCurPage(1);
    }

    public function getCacheKeyInfo()
    {
        $output = parent::getCacheKeyInfo();
        $output[] = Mage::helper('SwiftOtter_Blog')->getUserSubscriptionStatusText();
        $output[] = Mage::helper('SwiftOtter_Blog')->getSearchTerm();

        return $output;
    }
}