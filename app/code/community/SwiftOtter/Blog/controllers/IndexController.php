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

class SwiftOtter_Blog_IndexController extends Mage_Core_Controller_Front_Action
{
    const NO_ROUTE_CONFIG = 'swiftotter_config/blog/blog_no_route';

    public function viewAction()
    {
        try {
            $response = Mage::getModel('SwiftOtter_Blog/Api')->get();

            /** @var SwiftOtter_Blog_Model_Request_Abstract $request */
            $request = Mage::getModel('SwiftOtter_Blog/PageFactory')->getPageFrom($response);

            switch ($request->getType()) {
                case $request::TYPE_FEED:
                    Mage::registry('current_feed_request', $request);
                    $this->_forward('feed', null, null, array_merge(
                        $this->getRequest()->getParams(),
                        array('request' => $request)
                    ));
                    break;
                case $request::TYPE_PAGE:
                    Mage::register('current_page_request', $request);
                    $this->_forward('page', null, null, array_merge(
                        $this->getRequest()->getParams(),
                        array('request' => $request)
                    ));
                    break;
                case $request::TYPE_SEARCH:
                    Mage::register('current_page_search_request', $request);
                    $this->_forward('search', null, null, array_merge(
                        $this->getRequest()->getParams(),
                        array('request' => $request)
                    ));
                    break;
                case $request::TYPE_SINGLE:
                    Mage::register('current_post_request', $request);
                    $this->_forward('post', null, null, array_merge(
                        $this->getRequest()->getParams(),
                        array('request' => $request)
                    ));
                    break;
            }

        } catch (SwiftOtter_Blog_FileNotFoundException $fileNotFound) {
            // 404 page
            Mage::log("SwiftOtter FileNotFound Exception thrown. Forwarding to 404.", 0, SwiftOtter_Blog_Helper_Data::LOG);
            $this->_noRouteAction();
        } catch (Exception $e) {
            // 404 page
            Mage::log("Generic Exception thrown. Forwarding to 404.", 0, SwiftOtter_Blog_Helper_Data::LOG);
            Mage::log($e->getMessage(), 0, SwiftOtter_Blog_Helper_Data::LOG);
            Mage::log($e->getFile(), 0, SwiftOtter_Blog_Helper_Data::LOG);
            $this->_noRouteAction();
        }
    }

    public function feedAction()
    {
        /** @var SwiftOtter_Blog_Model_Request_Feed $feedRequest */
        if ($feedRequest = $this->getRequest()->getParam('request')) {
            $feed = $feedRequest->getXml();

            $this->getResponse()->clearAllHeaders()
                ->setHeader('content-type', 'text/xml')
                ->setBody($feed);
        } else {
            Mage::log("Feed wasn't found. Forwarding to 404.", SwiftOtter_Blog_Helper_Data::LOG);
            $this->_noRouteAction();
        }
    }

    public function subscribeAction()
    {
        $response = Mage::getModel('SwiftOtter_Blog/Api')->post(Mage::helper('SwiftOtter_Blog')->getEndpointUrl(), $this->getRequest()->getPost());

        if ($response->getParams()->get('redirect.count') >= 1) {
            // On certain pages, this was stripping part of the route out.
            // For example /blog/2015/12/gift-guide became /12/gift-guide
            // $this->_redirect("*", array('_query' => parse_url($response->getInfo('url'))["query"]));

            $this->_redirectUrl($this->getRequest()->getParam('path') . "?" . parse_url($response->getInfo('url'))["query"]);
        } else {
            $this->_forward("view");
        }
    }

    public function postAction()
    {
        /** @var SwiftOtter_Blog_Model_Request_Single $postRequest */
        if ($postRequest = $this->getRequest()->getParam('request')) {
            $post = $postRequest->getPost();

            $this->_title($post->getTitle());

            Mage::register('current_post', $post);
            $this->loadLayout();
            $this->renderLayout();
        } else {
            Mage::log("Blog: Post wasn't found correctly. Forwarding to 404.", 4, SwiftOtter_Blog_Helper_Data::LOG);
            $this->_noRouteAction();
        }
    }

    public function pageAction()
    {
        /** @var SwiftOtter_Blog_Model_Request_Page $pageRequest */
        if ($pageRequest = $this->getRequest()->getParam('request')) {
            $this->_title($this->__('Blog'));

            $posts = $pageRequest->getPosts();
            Mage::register('post_list', $posts);

            $this->loadLayout();
            $this->renderLayout();
        }
    }

    public function searchAction()
    {
        /** @var SwiftOtter_Blog_Model_Request_Page $pageRequest */
        if ($pageRequest = $this->getRequest()->getParam('request')) {
            $this->_title($this->__('Search Blog'));
            Mage::register('current_search_query', $this->getRequest()->getParam(Mage::helper("SwiftOtter_Blog")->getSearchParam()));

            $posts = $pageRequest->getPosts();
            Mage::register('post_list', $posts);

            $this->loadLayout();
            $this->renderLayout();
        }
    }


    public function refreshIndexAction()
    {
        try {
            Mage::getModel('SwiftOtter_Blog/Cron')->refreshStructure();
            $this->_clearCache();
            echo "Blog indices have been updated.";
        } catch (Exception $e) {
            Mage::log('Failed to clear indices when triggered by blog.', 2, SwiftOtter_Blog_Helper_Data::LOG);
        }
    }

    public function refreshCacheAction()
    {
        try {
            $this->_clearCache();
            echo "Success";
        } catch (Exception $e) {
            Mage::log('Failed to clear cache when triggered by blog.', 2, SwiftOtter_Blog_Helper_Data::LOG);
        }
    }

    protected function _clearCache()
    {
        $cache = Mage::app()->getCache();

        foreach ($cache->getTags() as $tag) {
            if (stripos($tag, 'blog')) {
                $ids = $cache->getIdsMatchingAnyTags(array($tag));
                foreach($ids as $id){
                    $cache->remove($id);
                }
            }
        }

        if (Mage::helper('core')->isModuleEnabled('PleasantHill_Adminhtml')) {
            Mage::helper('PleasantHill_Adminhtml/Cache_Key')->clean([ '*blog*', '*BLOG*' ]);
        }
    }

    protected function _noRouteAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        $this->getResponse()->setHeader('Status','404 File not found');

        $pageId = Mage::getStoreConfig(self::NO_ROUTE_CONFIG);
        Mage::log('Rendering blog 404', 5, SwiftOtter_Blog_Helper_Data::LOG);
        if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
            $this->_forward('defaultNoRoute');
        }
    }
}