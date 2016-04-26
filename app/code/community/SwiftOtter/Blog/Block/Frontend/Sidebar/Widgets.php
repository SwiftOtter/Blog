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
 * Copyright: 2015 (c) SwiftOtter Studios
 *
 * @author    Jesse Maxwell
 * @copyright Swift Otter Studios, 12/16/15
 * @package   default
 **/

class SwiftOtter_Blog_Block_Frontend_Sidebar_Widgets extends SwiftOtter_Base_Block_Template
{
    public function getWidgets()
    {
        $widgetCollection = $this->getSidebarWidgets();
        $subscriptionStatus = Mage::helper('SwiftOtter_Blog')->userHasSubscribed();

        if (!$widgetCollection) {
            try {
                Mage::log("Blog: getting sidebar widgets.", 7, SwiftOtter_Blog_Helper_Data::LOG);
                $widgetCollection = Mage::getModel('SwiftOtter_Blog/Api')->get(
                    Mage::helper('SwiftOtter_Blog')->getSidebarWidgetUrl()
                );
            } catch (Exception $e) {
                Mage::log("Blog: error when getting sidebar.", 0, SwiftOtter_Blog_Helper_Data::LOG);
                Mage::log($e->getMessage(), 0, SwiftOtter_Blog_Helper_Data::LOG);
            }

            if ($subscriptionStatus) {
                $this->setSidebarWidgetsSubscribed($widgetCollection);
            } else {
                $this->setSidebarWidgets($widgetCollection);
            }
        }

        if ($subscriptionStatus) {
            $widgetCollection = $this->getSidebarWidgetsSubscribed();
        }
        if ($widgetCollection) {
            $response = $widgetCollection->json();
        }

        if (isset($response["widgets"])) {
            return $response["widgets"];
        }
    }

    /**
     * Determine if the widget is a search block so it can be replaced.
     *
     * @param $widget
     *
     * @return bool
     */
    public function getIsSearch($widget)
    {
        return (isset($widget['id']) && strpos($widget['id'], "search") !== false);
    }

    public function getCacheKeyInfo()
    {
        $output = parent::getCacheKeyInfo();
        $output[] = Mage::helper('SwiftOtter_Blog')->getUserSubscriptionStatusText();

        return $output;
    }
}