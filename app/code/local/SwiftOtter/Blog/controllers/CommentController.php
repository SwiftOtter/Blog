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


class SwiftOtter_Blog_CommentController extends Mage_Core_Controller_Front_Action
{
    public function addAction()
    {
        $output = array();
        $comment = $this->_initComment();

        try {
            if ($this->_validateFormKey() && $comment->getPostId() && ctype_digit($comment->getPostId()) && $this->_validateRecaptcha()) {
                $this->_postCommentToWP($comment);
            } else {
                throw new Exception($this->__('Invalid form.'));
            }

            $output['html'] = $this->_getAddOutput($comment);
        } catch (Exception $ex) {
            $output['error'] = $ex->getMessage();
        }

        $this->getResponse()->clearAllHeaders()
            ->setHeader('content-type', 'application/json')
            ->setBody(json_encode($output));
    }

    protected function _initComment()
    {
        $request = $this->getRequest();
        $postId = base64_decode($request->getParam('post_hash'));

        $comment = Mage::getModel('SwiftOtter_Blog/Post_Comment');
        $comment->setData(array(
            'post_id' => $postId,
            'name' => strip_tags($request->get('name')),
            'email' => strip_tags($request->get('email')),
            'content' => strip_tags($request->get('content')),
            'date' => Mage::getModel('core/date')->date('Y-m-d H:i:s'),
            'status' => $comment::STATUS_AWAIT_MODERATION
        ));

        return $comment;
    }

    protected function _getAddOutput($comment)
    {
        $this->loadLayout();
        $block = $this->getLayout()->getBlock('comment')->setComment($comment);

        return $block->toHtml();
    }

    protected function _validateRecaptcha()
    {
        $request = $this->getRequest();

        $response = Mage::getModel('SwiftOtter_Blog/Api')->post(Mage::helper('SwiftOtter_Blog')->getGoogleApiUrl(), array(
            'secret' => Mage::helper('SwiftOtter_Blog')->getGoogleApiSecret(),
            'response' => $request->get('g-recaptcha-response')
        ));

        $json = $response->json();
        $success = $json['success'];

        if (!$success) {
            throw new Exception($this->__('The Recaptcha could not be validated. Please try again.'));
        }

        return $success;
    }

    protected function _postCommentToWP(SwiftOtter_Blog_Model_Post_Comment $comment)
    {
        /** @var \Guzzle\Http\Message\Response $response */
        $response = Mage::getModel('SwiftOtter_Blog/Api')->post(Mage::helper('SwiftOtter_Blog')->getEndpointUrl(), array_merge(
            $comment->getData(),
            array(
                'json' => 'submit_comment'
            )
        ));

        $output = $response->json();

        if (isset($output['status']) && $output['status'] == 'error') {
            throw new Exception($output['error']);
        }

        if ($response->getStatusCode() !== 200) {
            throw new Exception($this->__('Unfortunately, an error was experienced while submitting your comment.'));
        }

//        return $output;
    }
}