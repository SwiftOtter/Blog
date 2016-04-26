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
 * @copyright Swift Otter Studios, 5/6/15
 * @package default
 **/

class SwiftOtter_Blog_Model_Request_Abstract
{
    protected $_json;
    protected $_type = 'abstract';
    protected $_url;
    protected $_pageNum = false;

    const TYPE_SINGLE = 'single';
    const TYPE_PAGE = 'page';
    const TYPE_SEARCH = 'search';
    const TYPE_FEED = 'feed';
    const TYPE_COMMENT = 'comment';

    public function init($json)
    {
        $this->setJson($json);
        return $this;
    }

    /**
     * @return array
     */
    public function getJson()
    {
        return $this->_json;
    }

    /**
     * @param array $json
     * @return $this;
     */
    public function setJson($json)
    {
        $this->_json = $json;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param string $type
     * @return $this;
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    public function getPageNumber()
    {
        return $this->_pageNum;
    }

    public function getCurrentVisible()
    {
        if (isset($this->_json['count'])) {
            return $this->_json['count'];
        } else {
            return 1;
        }
    }

    public function getTotalPosts()
    {
        if (isset($this->_json['count_total'])) {
            return $this->_json['count_total'];
        } else {
            return 1;
        }
    }

    public function getTotalPages()
    {
        if (isset($this->_json['pages'])) {
            return $this->_json['pages'];
        } else {
            return 1;
        }
    }


}