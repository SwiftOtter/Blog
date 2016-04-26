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
 * @copyright Swift Otter Studios, 5/7/15
 * @package default
 **/

class SwiftOtter_Blog_Model_Request_Feed extends SwiftOtter_Blog_Model_Request_Abstract implements SwiftOtter_Blog_Model_Request_Interface
{
    protected $_type = self::TYPE_FEED;
    protected $_xml;

    public function init($value)
    {
        $this->setXml($value);
        return $this;
    }

    public function getXml()
    {
        return $this->_xml;
    }

    public function setXml($xml)
    {
        $this->_xml = (string)$xml;

        $this->_xml = Mage::helper('SwiftOtter_Blog')->filter($this->_xml);
        return $this;
    }

    protected function _filterItem($endpoint, $replacement, $value)
    {
        $value = str_replace($endpoint, $replacement, $value);
        return $value;
    }
}