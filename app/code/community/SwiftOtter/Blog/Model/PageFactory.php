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

class SwiftOtter_Blog_Model_PageFactory
{
    public function getPageFrom(\Guzzle\Http\Message\Response $response)
    {
        if (strpos($response->getHeader('content-type'), 'xml') !== false) {
            return Mage::getModel('SwiftOtter_Blog/Request_Feed')->init($response->getBody());
        } else {
            $json = $response->json();
            if (isset($json['error']) && $json['error'] == 'Not found') {
                throw new SwiftOtter_Blog_FileNotFoundException();
            }

            if (isset($response->getInfo()["query"][Mage::helper("SwiftOtter_Blog")->getSearchParam()])) {
                return Mage::getModel('SwiftOtter_Blog/Request_Page_Search')->init($json);
            } else if (isset($json['posts'])) {
                return Mage::getModel('SwiftOtter_Blog/Request_Page')->init($json);
            } else {
                return Mage::getModel('SwiftOtter_Blog/Request_Single')->init($json);
            }
        }
    }
}