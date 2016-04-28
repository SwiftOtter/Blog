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

class SwiftOtter_Blog_Helper_Data extends SwiftOtter_Base_Helper_Data
{
    const SEARCH_PARAM = "s";
    const LOG = 'blog.log';

    protected $_group = 'blog';
    protected $_allowedQueryKeys = array("subscribe");
    protected $_postListTypes = array(
        'SwiftOtter_Blog/Frontend_List',
        'SwiftOtter_Blog/Frontend_Search'
    );

    public function __construct()
    {
        $this->_allowedQueryKeys[] = $this::SEARCH_PARAM;
    }

    public function getCurrentUrl()
    {
        return Mage::app()->getStore()->getUrl('blog/');
    }

    public function getEndpointUrl()
    {
        return $this->getStoreConfig('endpoint_url');
    }

    public function getGoogleApiUrl()
    {
        return $this->getStoreConfig('google_api_url');
    }

    public function getGoogleApiSecret()
    {
        return $this->getStoreConfig('google_api_secret');
    }

    public function getGoogleApiPublicKey()
    {
        return $this->getStoreConfig('google_api_public');
    }

    public function getJsonAppend()
    {
        return $this->getStoreConfig('json_append_parameter');
    }

    public function getFacebook()
    {
        return $this->getStoreConfig('enable_facebook');
    }

    public function getTwitter()
    {
        return $this->getStoreConfig('enable_twitter');
    }

    public function getPinterest()
    {
        return $this->getStoreConfig('enable_pinterest');
    }

    public function getSearchParam()
    {
        return $this::SEARCH_PARAM;
    }

    public function getSidebarWidgetUrl()
    {
        return $this->getEndpointUrl() . "api/widgets/get_sidebar/?sidebar_id=" . $this->getStoreConfig('sidebar_widget_id');
    }

    public function getFeedUrl()
    {
        return $this->getBlogUrl(true) . "feed/";
    }

    public function getBlogUrl($disableUrlParam = false)
    {
        $href = $this->getEndpointUrl() . $this->getBlogParams();

        if (!$disableUrlParam && !$this->_preventJsonAppend()) {
            $href = $this->addOrUpdateUrlParam($href, $this->getJsonAppend(), 1);
            $href = $this->addQueryParams($href);
        }

        return $href;
    }

    protected function _preventJsonAppend()
    {
        if (strpos($this->getBlogParams(), 'feed') !== false) {
            return true;
        }

        return false;
    }

    public function addQueryParams($href)
    {
        $queryString = $this->getBlogParamsArray();

        if (isset($queryString["_query"])) {
            foreach ($queryString["_query"] as $key => $value) {
                if ($value) {
                    $href = $this->addOrUpdateUrlParam($href, $key, $value);
                }
            }
        }

        return $href;
    }

    public function getBlogParams()
    {
        $frontName = SwiftOtter_Blog_Model_Observer_Router::FRONT_NAME;

        $href = Mage::app()->getRequest()->getPathInfo();
        $href = ltrim(str_replace(DS.$frontName, '', $href), '/');

        return $href;
    }

    public function getBlogParamsArray()
    {
        $params = $this->getBlogParams();
        $query = $this->_getQueryFromRequest();

        $list = explode(DS, $params);
        $output = array();

        for($i = 0; $i < count($list); $i++) {
            $key = $list[$i];

            if ($key) {
                $value = '';

                if (count($list) > $i + 1) {
                    $value = $list[$i + 1];
                    $i++;
                }

                $output[$key] = $value;
            }
        }

        if ($query) {
            $output['_query'] = $this->_sanitizeQueryString($query);
        }

        return $output;
    }

    protected function _getQueryFromRequest()
    {
        $request = Mage::app()->getRequest();
        $query = $request->getQuery();

        if (!$query) {
            $query = explode("=", $request->getParam('_query'));
        }

        return $query;
    }

    protected function _sanitizeQueryString($input)
    {
        $output = array();

        foreach ($input as $key => $value) {
            if (in_array($key, $this->_allowedQueryKeys)) {
                $output[$key] = $value;
            }
        }

        return $output;
    }

    public function assembleParamsIntoString($params)
    {
        $output = '';

        foreach ($params as $key => $value) {
            if ($key !== "_query") {
                $output .= $key . '/' . $value . '/';
            } else {
                $output .= '?';
                $items = array();
                foreach ($value as $queryKey => $queryValue) {
                    $items[] = urldecode(urlencode($queryKey)) . "=" . urldecode(urlencode($queryValue));
                }

                $output .= implode('&', $items);
            }
        }

        return $output;
    }

    public function filter($data)
    {
        $endpoint = str_replace('/', '\/', rtrim($this->getEndpointUrl(), '/'));
        $current = rtrim($this->getCurrentUrl(), '/');

        $output = preg_replace(sprintf('/%s(?!(\/)*+wp\-content)/', $endpoint), $current, $data);
        return $output;
    }

    /**
     * Just a stub to be extended
     * 
     * @return string
     */
    public function getPreviousUrl()
    {
        return $this->getCurrentUrl();
    }

    public function getBlogActionUrl($actionName)
    {
        $href = $this->getEndpointUrl();
        $href = $this->addOrUpdateUrlParam($href, $this->getJsonAppend(), $actionName);

        return $href;
    }

    public function getSearchTerm()
    {
        return $this->escapeHtml(Mage::registry('current_search_query'));
    }

    public function getListTypes()
    {
        return $this->_postListTypes;
    }

    /**
     * If query param exists that the user has subscribed with the sidebar widget return true.
     *
     * @return bool
     */
    public function userHasSubscribed()
    {
        if (Mage::app()->getRequest()->getParam('subscribe') === 'success') {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getUserSubscriptionStatusText()
    {
        $subscriptionStatus = 'unsubscribed';

        if ($this->userHasSubscribed()) {
            $subscriptionStatus = 'subscribed';
        }

        return $subscriptionStatus;
    }
}