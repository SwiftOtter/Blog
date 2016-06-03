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

class SwiftOtter_Blog_Model_Request_Page extends SwiftOtter_Blog_Model_Request_Abstract implements SwiftOtter_Blog_Model_Request_Interface
{
    protected $_posts = array();
    /** @var  int $_visibleCount */
    protected $_visibleCount;
    /** @var  int $_totalCount */
    protected $_totalCount;

    protected $_cachePrefix = 'PAGE';
    protected $_includedKeys = ['id'];
    protected $_cacheTags = [
        'blog_post',
        'blog_page'
    ];


    protected $_type = self::TYPE_PAGE;

    protected $_loaded = false;

    const PAGE_TYPE_TAG = 'tag';
    const PAGE_TYPE_CATEGORY = 'category';
    const PAGE_TYPE_DATEINDEX = 'date_index';
    const PAGE_TYPE_UNKNOWN = 'unknown';
    const PAGE_TYPE_INDEX = 'index';

    public function getPosts()
    {
        if (!$this->_loaded) {
            $this->_posts = $this->_formulatePosts();
        }

        return $this->_posts;
    }

    protected function _getCacheTagData()
    {
        $posts = $this->getPosts();
        $separator = SwiftOtter_Blog_Model_Cache::GROUP_SEPARATOR;

        return array_map(function(SwiftOtter_Blog_Model_Post $post) use ($separator) {
            return strtolower(SwiftOtter_Blog_Model_Cache::CACHE_PREFIX . $separator . $this->_cachePrefix . $separator . 'id' . $separator . $post->getId());
        }, $posts);
    }

    public function getPageNumber()
    {
        if ($this->_pageNum === false) {
            $parts = explode(DS, trim(Mage::helper('SwiftOtter_Blog')->getBlogParams(), '/'));
            $this->_pageNum = 1;

            for ($i = 0; $i < count($parts); $i++) {
                if ($parts[$i] == 'page' && count($parts) > $i+1) {
                    $this->_pageNum = $parts[$i+1];
                    break;
                }
            }
        }

        return $this->_pageNum;
    }

    /**
     * @return string
     */
    public function getPageType()
    {
        $parts = explode(DS, trim(Mage::helper('SwiftOtter_Blog')->getBlogParams(), '/'));

        if (count($parts) == 2 && ctype_digit($parts[0]) && ctype_digit($parts[1])) {
            return self::PAGE_TYPE_DATEINDEX;
        }

        if (count($parts) > 1) {
            if ($parts[0] == self::PAGE_TYPE_CATEGORY) {
                return self::PAGE_TYPE_CATEGORY;
            }

            if ($parts[0] == self::PAGE_TYPE_TAG) {
                return self::PAGE_TYPE_TAG;
            }
        } else if ($parts[0] == "") {
            return self::PAGE_TYPE_INDEX;
        }

        return self::PAGE_TYPE_UNKNOWN;
    }

    protected function _formulatePosts()
    {
        $body = $this->getBody();
        $posts = array();

        if (isset($body['posts']) && is_array($body['posts'])) {
            foreach ($body['posts'] as $postData) {
                $post = Mage::getModel('SwiftOtter_Blog/Post')->init($postData);

                $posts[] = $post;
            }
        }

        return $posts;
    }
}