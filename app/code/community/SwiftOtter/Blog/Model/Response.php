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
class SwiftOtter_Blog_Model_Response
{
    protected $_body;
    protected $_url;
    protected $_contentType;

    public function __construct($url, $contentType, $body)
    {
        $this->_url = $url;
        $this->_contentType = $contentType;
        $this->_body = $body;
    }

    public function isCached()
    {
        return Mage::getModel('SwiftOtter_Blog/Cache')->loadForUrl($this->getUrl());
    }

    public function isJson()
    {
        return strpos($this->_contentType, 'json') !== false;
    }

    public function isXml()
    {
        return strpos($this->_contentType, 'xml') !== false;
    }
    
    /**
     * @return string|array
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    public function hasQueryKey($key)
    {
        $queryString = $this->getQueryString();
        parse_str($queryString, $pieces);

        return array_key_exists($key, $pieces);
    }

    public function getQueryString()
    {
        $urlPieces = parse_url($this->getUrl());
        return $urlPieces['query'];
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->_contentType;
    }
}