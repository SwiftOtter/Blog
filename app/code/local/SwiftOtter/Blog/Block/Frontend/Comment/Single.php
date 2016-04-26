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
 * @copyright Swift Otter Studios, 5/19/15
 * @package default
 **/


class SwiftOtter_Blog_Block_Frontend_Comment_Single extends SwiftOtter_Base_Block_Template
{
    protected $_comment;

    /**
     * @return SwiftOtter_Blog_Model_Post_Comment
     */
    public function getComment()
    {
        return $this->_comment;
    }

    /**
     * Fail safe way to get the comment id if it exists
     * @return int
     */
    public function _getCommentId()
    {
        $id = 0;

        if ($this->getComment() instanceof SwiftOtter_Blog_Model_Post_Comment) {
            try {
                $id = (int) $this->getComment()->getData('id');
            } catch (Exception $e) {
                Mage::log("Comment error", 1, SwiftOtter_Blog_Helper_Data::LOG);
            }
        }

        return $id;
    }

    /**
     * @param SwiftOtter_Blog_Model_Post_Comment $comment
     * @return $this
     */
    public function setComment(SwiftOtter_Blog_Model_Post_Comment $comment)
    {
        $this->_comment = $comment;
        return $this;
    }

    public function getCacheKeyInfo()
    {
        $output = parent::getCacheKeyInfo();
        $output[] = $this->_getCommentId();

        return $output;
    }
}