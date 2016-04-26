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

class SwiftOtter_Blog_Block_Frontend_Comment_Form extends SwiftOtter_Base_Block_Template
{
    public function getSubmitUrl()
    {
        return Mage::getUrl('blog/comment/add');
    }

    public function getPostIdHash()
    {
        $post = $this->_getPost();
        $hash = '';

        if ($post->getId()) {
            $hash = base64_encode($post->getId());
        }

        return $hash;
    }

    protected function _getPost()
    {
        $post = Mage::registry('current_post');

        if (!$post) {
            $post = Mage::getModel('SwiftOtter_Blog/Post');
        }

        return $post;
    }
}