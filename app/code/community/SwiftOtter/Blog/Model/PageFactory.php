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
    public function getPageFrom(SwiftOtter_Blog_Model_Response $response)
    {
        if ($response->isXml()) {
            $subType = 'Feed';
        } else {
            $json = json_decode($response->getBody(), true);
            if (isset($json['error']) && $json['error'] == 'Not found') {
                throw new SwiftOtter_Blog_FileNotFoundException();
            }

            if ($response->hasQueryKey(Mage::helper("SwiftOtter_Blog")->getSearchParam())) {
                $subType = 'Page_Search';
            } else if (isset($json['posts'])) {
                $subType = 'Page';
            } else {
                $subType = 'Single';
            }
        }

        $type = "SwiftOtter_Blog_Model_Request_$subType";
        return new $type($response);
    }
}