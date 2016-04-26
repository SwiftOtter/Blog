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

class SwiftOtter_Blog_Model_Post extends Mage_Core_Model_Abstract
{
    protected $_comments;
    protected $_author;
    protected $_tags;
    protected $_categories;

    public function init(array $json)
    {
        $this->setData($json);

        $this->_filter();

        return $this;
    }

    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * @return array
     */
    public function getComments()
    {
        if (!$this->_comments && is_array($this->getData('comments'))) {
            $comments = array();

            foreach ($this->getData('comments') as $commentData) {
                $comments[] = Mage::getModel('SwiftOtter_Blog/Post_Comment')->setData($commentData);
            }

            $this->_comments = $comments;
        }
        return $this->_comments;
    }

    public function getContent()
    {
        $data = $this->getData('content');

        if (strpos($data, '{{') !== false) {
            $data = Mage::helper('cms')->getBlockTemplateProcessor()->filter($data);
        }

        return $data;
    }


    public function getNumberOfComments()
    {
        return count($this->getComments());
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        $authorData = $this->getData('author');

        if (!$this->_author && is_array($this->getData('author'))) {
            $this->_author = Mage::getModel('SwiftOtter_Blog/Author')->setData($authorData);
        }
        return $this->_author;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        if (!$this->_tags && is_array($this->getData('tags'))) {
            $tags = array();

            foreach ($this->getData('tags') as $tagData) {
                $tags[] = Mage::getModel('SwiftOtter_Blog/Tag')->setData($tagData);
            }

            $this->_tags = $tags;
        }

        return $this->_tags;
    }

    public function getNumberofTags()
    {
        return count($this->getTags());
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        if (!$this->_categories && is_array($this->getData('categories'))) {
            $categories = array();

            foreach ($this->getData('categories') as $categoryData) {
                $categories[] = Mage::getModel('SwiftOtter_Blog/Category')->setData($categoryData);
            }

            $this->_categories = $categories;
        }

        return $this->_categories;
    }

    public function getNumberOfCategories()
    {
        return count($this->getCategories());
    }

    public function getUrl()
    {
        return $this->getData('url');
    }

    public function getCommentUrl()
    {
        return $this->getData('url') . "#comments";
    }

    protected function _filter()
    {
        $helper = Mage::helper('SwiftOtter_Blog');
        $searches = array(
            'url',
            'content',
            'excerpt'
        );

        foreach ($searches as $key) {
            $this->setData($key, $helper->filter($this->getData($key)));
        }
    }
}