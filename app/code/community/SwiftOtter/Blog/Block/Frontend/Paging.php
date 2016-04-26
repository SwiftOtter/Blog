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
 * @copyright Swift Otter Studios, 9/4/15
 * @package default
 **/

class SwiftOtter_Blog_Block_Frontend_Paging extends Mage_Core_Block_Template
{
    protected $_count = 0;
    protected $_totalPosts = 0;
    protected $_maxPages = 0;
    protected $_currentPage = 1;

    CONST PAGES_TO_SHOW_EACH_WAY = 7;

    public function startup($currentPage, $count, $totalPosts, $maxPages)
    {
        $this->_currentPage = $currentPage;
        $this->_count = $count;
        $this->_countTotal = $totalPosts;
        $this->_maxPages = $maxPages;

        return $this;
    }

    public function getCurrentPage()
    {
        return $this->_currentPage;
    }

    public function getStartPage()
    {
        return max($this->_currentPage - self::PAGES_TO_SHOW_EACH_WAY, 1);
    }

    public function getEndPage()
    {
        return min($this->_currentPage + self::PAGES_TO_SHOW_EACH_WAY, $this->_maxPages);
    }

    /**
     * @param $page
     * @return string
     */
    public function getLink($page)
    {
        $params = Mage::helper('SwiftOtter_Blog')->getBlogParamsArray();

        if ($page > 1) {
            $params['page'] = $page;
        } else if (isset($params['page'])) {
            unset($params['page']);
        }

        $query = '';
        if (isset($params['_query'])) {
            $query = $params['_query'];
            unset($params['_query']);
        }

        return Mage::getUrl(SwiftOtter_Blog_Model_Observer_Router::FRONT_NAME . DS . Mage::helper('SwiftOtter_Blog')->assembleParamsIntoString($params), array('_query' => $query));
    }
}