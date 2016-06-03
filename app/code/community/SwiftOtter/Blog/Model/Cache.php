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
 * @author Joseph Maxwell
 * @copyright SwiftOtter Studios, 5/30/16
 * @package default
 **/

class SwiftOtter_Blog_Model_Cache
{
    const CACHE_PREFIX = 'blog_cache';
    const CACHE_MODULE = 'SwiftOtter_Blog_Api_Data';
    const GROUP_SEPARATOR = '_';

    public function loadForUrl($url)
    {
        if (Mage::app()->useCache(self::CACHE_MODULE)) {
            $cache = $this->_getCache()->load(self::CACHE_PREFIX . self::GROUP_SEPARATOR . Mage::helper('SwiftOtter_Blog/Cache')->flattenUrl($url));
            return json_decode($cache, true);
        } else {
            return false;
        }
    }

    public function save(SwiftOtter_Blog_Model_Request_Interface $object)
    {
        if (method_exists($object, 'getCacheKey') && Mage::app()->useCache(self::CACHE_MODULE)) {
            $cacheKey = $object->getCacheKey();
            $this->_getCache()->save(json_encode($object->getCacheData()), $cacheKey, $object->getCacheTags());
        }
    }

    public function saveForUrl($url, $response)
    {
        $cache = $this->_getCache()->save(Mage::helper('SwiftOtter_Blog/Cache')->flattenUrl($url));
        return $cache;
    }

    protected function _getCache()
    {
        return Mage::app()->getCache();
    }

    
}