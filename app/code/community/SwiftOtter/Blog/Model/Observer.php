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
 * @author Joseph Maxwell
 * @copyright SwiftOtter Studios, 3/1/16
 * @package default
 **/

class SwiftOtter_Blog_Model_Observer
{
    public function controllerActionLayoutRenderBeforeBlogIndexView()
    {
        Mage::app()->getLayout()->getBlock('root')->setCachePage(false);
    }

    public function filterWidgetUrls($event)
    {
        $data = $event->getWidgetGroup();
        $widgets = $data->getWidgets();
        $helper = Mage::helper('SwiftOtter_Blog');
        
        if (!$widgets) {
            return false;
        }
        
        foreach ($widgets as $key => $widget) {
            if (isset($widget['widget'])) {
                $widgets[$key]['widget'] = $helper->filter($widget['widget']);
            }
        }

        $data->setWidgets($widgets);
    }
}