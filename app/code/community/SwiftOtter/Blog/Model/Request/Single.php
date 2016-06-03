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

class SwiftOtter_Blog_Model_Request_Single extends SwiftOtter_Blog_Model_Request_Abstract implements SwiftOtter_Blog_Model_Request_Interface
{
    protected $_post;

    protected $_cachePrefix = 'SINGLE';
    protected $_includedKeys = [
        'id',
        'author',
        'comment_count'
    ];

    protected $_cacheTags = [
        'blog_post'
    ];

    protected $_type = self::TYPE_SINGLE;
    
    public function getPost()
    {
        if (!$this->_post) {
            $json = $this->getBody();
            $this->_post = Mage::getModel('SwiftOtter_Blog/Post');

            if (isset($json['post'])) {
                $this->_post->init($json['post']);
            } elseif (isset($json['page'])) {
                $this->_post->init($json['page']);
            }
        }

        return $this->_post;
    }
    
    protected function _getCacheTagData()
    {
        $post = $this->getPost();
        $separator = SwiftOtter_Blog_Model_Cache::GROUP_SEPARATOR;

        return array_filter(array_map(function($key) use ($post, $separator) {
            if ($value = $post->getData($key)) {
                return SwiftOtter_Blog_Model_Cache::CACHE_PREFIX . $separator . $this->_cachePrefix . $separator . strtoupper($key) . '_' . $value;
            } else {
                return '';
            }
        }, $this->_includedKeys));
    }
}