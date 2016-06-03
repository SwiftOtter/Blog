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

/**
 * Immutable object that holds the parsed properties for
 *
 * Class SwiftOtter_Blog_Model_Request_Abstract
 */
abstract class SwiftOtter_Blog_Model_Request_Abstract
{
    protected $_response;
    protected $_type = 'abstract';
    protected $_pageNum = false;

    protected $_includedKeys = ['id'];
    protected $_cachePrefix = 'abstract';
    protected $_cacheGroupSeparator = '_';
    protected $_cacheValueSeparator = ':';

    protected $_baseCacheTag = 'blog';
    protected $_cacheTags = [];

    const TYPE_SINGLE = 'single';
    const TYPE_PAGE = 'page';
    const TYPE_SEARCH = 'search';
    const TYPE_FEED = 'feed';
    const TYPE_COMMENT = 'comment';

    public function __construct(SwiftOtter_Blog_Model_Response $response)
    {
        $this->_response = $response;

        return $this;
    }

    abstract protected function _getCacheTagData();

    public function getCacheTags()
    {
        return array_merge(
            [ $this->_baseCacheTag ],
            $this->_getCacheTagData()
        );
    }

    public function getCacheData()
    {
        if ($this->_response->isXml()) {
            $body = $this->getBody();
        } else {
            $body = json_encode($this->getBody());
        }

        return ['url' => $this->getUrl(), 'content-type' => $this->getContentType(), 'body' => $body ];
    }

    public function getCacheKey()
    {
        $separator = SwiftOtter_Blog_Model_Cache::GROUP_SEPARATOR;
        $data = array_filter(array_merge(
            [ Mage::helper('SwiftOtter_Blog/Cache')->flattenUrl($this->getUrl()) ]
        ));

        return SwiftOtter_Blog_Model_Cache::CACHE_PREFIX . $separator . implode($separator, $data);
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->_response->getContentType();
    }

    /**
     * @return array
     */
    public function getBody()
    {
        if ($this->_response->isXml()) {
            return $this->_response->getBody();
        } else {
            return json_decode($this->_response->getBody(), true);
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_response->getUrl();
    }

    public function getPageNumber()
    {
        return $this->_pageNum;
    }

    public function getCurrentVisible()
    {
        if (isset($this->getBody()['count'])) {
            return $this->getBody()['count'];
        } else {
            return 1;
        }
    }

    public function getTotalPosts()
    {
        if (isset($this->getBody()['count_total'])) {
            return $this->getBody()['count_total'];
        } else {
            return 1;
        }
    }

    public function getTotalPages()
    {
        if (isset($this->getBody()['pages'])) {
            return $this->getBody()['pages'];
        } else {
            return 1;
        }
    }
}