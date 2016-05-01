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
 * Copyright: 2016 (c) SwiftOtter Studios
 *
 * @author    Jesse Maxwell
 * @copyright Swift Otter Studios, 4/28/16
 * @package   default
 **/

class SwiftOtter_Blog_Block_Frontend_Product_Tag extends SwiftOtter_Base_Block_Template
{
    protected $_products = [];
    protected $_rawTags = [];

    /**
     * @var $_hydratedProductTags: Tags with product data
     */
    protected $_hydratedProductTags = [];


    public function setProductTags($rawTags)
    {
        $this->_rawTags = $rawTags;
        return $this;
    }

    public function getTagsAsJsObjects()
    {
        $objects = [];

        if ($tags = $this->getProductTags()) {
            foreach ($tags as $tag) {
                $objects[] = json_encode($this->_createTagObject($tag));
            }
        }

        return $objects;
    }

    protected function _createTagObject($tag)
    {
        $output = [];

        /** @var $product Mage_Catalog_Model_Product */
        $product = $tag->getProduct();

        $output['top'] = $tag->getData('top');
        $output['left'] = $tag->getData('left');
        $output['id'] = $tag->getData('id');
        $output['image'] = (string) Mage::helper('catalog/image')->init($product, 'thumbnail')->keepFrame(false)->resize(90);
        $output['url'] = $product->getProductUrl();
        $output['name'] = $product->getName();

        return $output;
    }

    public function getProductTags()
    {
        if (!$this->_hydratedProductTags) {
            $this->_loadProductData()->_associateProducts();
        }

        return $this->_hydratedProductTags;
    }

    protected function _loadProductData()
    {
        $this->_products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('sku', ['in' => $this->_getAllProductSkus()])
            ->addAttributeToFilter('status', ['eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED])
            ->addAttributeToSelect(['name', 'thumbnail', 'url']);

        return $this;
    }

    protected function _associateProducts()
    {
        if (!$this->_products) {
            return false;
        }

        foreach ($this->_rawTags as $tag) {
            $this->_hydratedProductTags[] = $this->_addProductDataToTag($tag);
        }

        return $this;
    }

    protected function _addProductDataToTag($tag)
    {
        foreach ($this->_products as $product) {
            if ($product instanceof Mage_Catalog_Model_Product && $tag->getSku() == $product->getSku()) {
                $tag->setData('product', $product);
                return $tag;
            }
        }
    }

    protected function _getAllProductSkus()
    {
        $tags = $this->_getRawProductTags();
        $skus = [];

        if ($tags and is_array($tags)) {
            foreach ($tags as $tag) {
                $skus[] = $tag->getSku();
            }
        }

        return array_unique($skus);
    }

    protected function _getRawProductTags()
    {
        return $this->_rawTags;
    }
}