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

class SwiftOtter_Blog_Model_DataPuller
{
    const ACTION_CATEGORY_INDEX = 'get_category_index';
    const ACTION_TAG_INDEX = 'get_tag_index';
    const ACTION_DATE_INDEX = 'get_date_index';

    protected $_client;

    public function mergeCategories()
    {
        $json = $this->_getJson(self::ACTION_CATEGORY_INDEX);

        if (isset($json['categories'])) {
            $this->_refreshData($json['categories'], 'SwiftOtter_Blog/Category');
        } else {
            // maybe truncate table here
        }

        return $this;
    }

    /**
     * Tags are no longer being rendered through Magento.
     *
     * @deprecated
     * @return $this
     * @throws \Exception
     */
    public function mergeTags()
    {
        $json = $this->_getJson(self::ACTION_TAG_INDEX);

        if (isset($json['tags'])) {
            $this->_refreshData($json['tags'], 'SwiftOtter_Blog/Tag');
        } else {
            // maybe truncate table here
        }

        return $this;
    }

    public function mergeDateIndex()
    {
        $json = $this->_getJson(self::ACTION_DATE_INDEX);

        if (isset($json['tree'])) {
            $json = $this->_formatDateIndex($json['tree']);
            $this->_refreshData($json, 'SwiftOtter_Blog/DateIndex', array($this, '_formatDateIndex'));
        } else {
            // maybe truncate table here
        }

        return $this;
    }

    protected function _getJson($action)
    {
        $url = Mage::helper('SwiftOtter_Blog')->getBlogActionUrl($action);
        $client = $this->_getClient();

        $response = $client->get($url);
        $json = $response->send()->json();

        return $json;
    }

    protected function _refreshData($json, $modelName)
    {
        /** @var SwiftOtter_Blog_Model_Resource_Category $resource */
        $resource = Mage::getResourceModel($modelName);
        $resource->beginTransaction();

        try {
            $collection = Mage::getResourceModel($modelName .'_Collection')->load();

            if (!$collection instanceof Traversable) {
                throw new Exception('Collection was not array.');
            }

            foreach ($collection as $item) {
                $item->delete();
            }

            foreach ($json as $data) {
                $item = Mage::getModel($modelName);
                $item->setData($data);
                $item->isObjectNew(true); // using this to force creation, as we already have an id.
                $item->save();
            };

            $resource->commit();
        } catch (Exception $ex) {
            $resource->rollback();
            Mage::log("Blog index refresh error: " .$ex->getMessage(), 0, SwiftOtter_Blog_Helper_Data::LOG);
            throw $ex;
        }
    }

    protected function _formatDateIndex($data)
    {
        $output = array();

        foreach ($data as $year => $months) {
            if (is_array($months)) {
                foreach ($months as $month => $postCount) {
                    $output[] = array(
                        'id' => $year . $month,
                        'year' => $year,
                        'month' => $month,
                        'post_count' => $postCount
                    );
                }
            }
        }

        return $output;
    }

    protected function _getClient()
    {
        if (!$this->_client) {
            $this->_client = new \Guzzle\Http\Client();
        }

        return $this->_client;
    }
}