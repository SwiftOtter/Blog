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
 * @copyright Swift Otter Studios, 5/12/15
 * @package default
 **/

class SwiftOtter_Blog_Block_Frontend_List extends SwiftOtter_Base_Block_Template
{
    protected $_pages;
    protected $_isHomePage;

    public function getList()
    {
        $this->_pages = Mage::registry('post_list');
        return $this->_pages;
    }

    public function getIsHomepage()
    {
        return $this->_isHomePage;
    }

    protected function _beforeToHtml()
    {
        /** @var SwiftOtter_Blog_Model_Request_Abstract $request */
        $request = Mage::registry('current_page_request');

        /** @var SwiftOtter_Blog_Block_Frontend_Paging $paging */
        if (($paging = $this->getChild('blog_paging')) && $request) {
            $params = Mage::helper('SwiftOtter_Blog')->getBlogParamsArray();

            $paging->startup(
                $request->getPageNumber(),
                $request->getCurrentVisible(),
                $request->getTotalPosts(),
                $request->getTotalPages()
            );
        }

        if ($request && ($request->getPageType() == SwiftOtter_Blog_Model_Request_Page::PAGE_TYPE_INDEX || Mage::getStoreConfig('swiftotter_config/blog/truncate_after_index_list'))) {
            $this->_isHomePage = true;
        }

        return parent::_beforeToHtml();
    }


}