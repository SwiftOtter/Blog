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

class SwiftOtter_Blog_Model_Api
{
    const STATUS_GOOD = 200;
    const STATUS_NOT_FOUND = 404;

    protected $_client;
    protected $_lastUrl;

    /**
     * @param string $url
     * @return SwiftOtter_Blog_Model_Response
     * @throws Exception
     * @throws SwiftOtter_Blog_FileNotFoundException
     */
    public function get($url = '', $cache = true)
    {
        $url = $this->_formatUrl($url);
        $this->_lastUrl = $url;

        if ($cache && ($cached = $this->_getCacheFor($url)) && is_array($cached)) {
            return new SwiftOtter_Blog_Model_Response($url, $cached['content-type'], $cached['body']);
        } else {
            $response = $this->_loadResponse($url);
            return new SwiftOtter_Blog_Model_Response($url, $response->getContentType(), $response->getBody(true));
        }
    }

    /**
     * @param $url
     * @return \Guzzle\Http\Message\Response
     * @throws Exception
     * @throws SwiftOtter_Blog_FileNotFoundException
     */
    protected function _loadResponse($url)
    {
        Mage::log("GET: Sending request to " . $url, 7, SwiftOtter_Blog_Helper_Data::LOG);

        $client = $this->_getClient($url);
        $response = $client->get(null, null, array('cookies' => array('frontend' => $this->_getClientCookies())));
        $queryParams = $response->getQuery();
        $requestTime = round(microtime(true) * 1000);
        $response = $response->send();
        $this->logRequest($requestTime);
        $response = $this->_addQueryToResponse($response, $queryParams);

        if ($response->getStatusCode() == self::STATUS_NOT_FOUND) {
            Mage::log('GET: Status code from blog is 404. Throwing 404.', 7, SwiftOtter_Blog_Helper_Data::LOG);
            throw new SwiftOtter_Blog_FileNotFoundException();
        } else if ($response->getStatusCode() != self::STATUS_GOOD) {
            Mage::log('GET: status code from blog is: '. $response->getStatusCode() .'. Throwing 404.', 3, SwiftOtter_Blog_Helper_Data::LOG);
            throw new Exception();
        }

        return $response;
    }

    protected function _formatUrl($url)
    {
        if (!$url) {
            $url = Mage::helper('SwiftOtter_Blog')->getBlogUrl();
        } else {
            $url = Mage::helper('SwiftOtter_Blog')->addQueryParams($url);
        }

        return $url;
    }

    protected function _getCacheFor($url)
    {
        return Mage::getModel('SwiftOtter_Blog/Cache')->loadForUrl($url);
    }

    /**
     * @param string $url
     * @return \Guzzle\Http\Message\Response
     * @throws Exception
     * @throws SwiftOtter_Blog_FileNotFoundException
     */
    public function post($url = '', $params = array())
    {
        $client = $this->_getClient();
        $requestTime = round(microtime(true)) * 1000;
        $response = $client->post($url, array(), $params, array('cookies' => array('frontend' => $this->_getClientCookies())))->send();
        $this->logRequest($requestTime, "POST");

        if ($response->getStatusCode() == self::STATUS_NOT_FOUND) {
            Mage::log('POST: status code from blog is 404. Throwing 404.', 7, SwiftOtter_Blog_Helper_Data::LOG);
            throw new SwiftOtter_Blog_FileNotFoundException();
        } else if ($response->getStatusCode() != self::STATUS_GOOD) {
            Mage::log('POST: status code from blog is: '. $response->getStatusCode() .'. Throwing 404.', 7, SwiftOtter_Blog_Helper_Data::LOG);
            throw new Exception();
        }

        return $response;
    }

    public function getLastUrl()
    {
        return $this->_lastUrl;
    }

    protected function logRequest($initial, $type = "GET")
    {
        Mage::log($type . ": Request Time " . (round(microtime(true) * 1000) - $initial) . "ms", 7, SwiftOtter_Blog_Helper_Data::LOG);
    }

    protected function _addQueryToResponse(\Guzzle\Http\Message\Response $response, $queryParams)
    {
        $info = $response->getInfo();
        $info["query"] = $queryParams;
        $response->setInfo($info);

        return $response;
    }

    protected function _filterUrls($response)
    {
        return $response;
    }

    protected function _getClient($baseUrl = '')
    {
        if (!$this->_client) {
            $client = new \Guzzle\Http\Client($baseUrl, $this->_getOptions());
            $this->_client = $client;
        }

        return $this->_client;
    }

    /**
     * @return string Only returning the frontend cookie if it exists
     */
    protected function _getClientCookies()
    {
        $cookies = Mage::getModel('core/cookie');
        $frontend = '';

        if ($cookies) {
            $frontend = $cookies->get('frontend');
        }

        return $frontend;
    }

    protected function _getOptions()
    {
        /* @link http://guzzle3.readthedocs.org/http-client/client.html */
        return [
            'request.options' => [
                'cookies'           => ['frontend' => $this->_getClientCookies()],
                'timeout'           => 20,
                'connect_timeout'   => 20
            ]
        ];
    }
}
