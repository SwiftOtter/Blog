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

class SwiftOtter_Blog_Model_Event
{
    /**
     * Run before the internal URL filtering. Use with caution.
     */
    const PRE_FILTER_CONTENT = "blog_pre_filter_content";
    
    /**
     * Ideal place to filter for short codes, for example.
     */
    const FILTER_POST_CONTENT = "blog_filter_post";

    /**
     * This event is fired first and is mostly for internal use.
     */
    const FILTER_WIDGET_URLS = "blog_filter_sidebar_urls";

    /**
     * This event is fired second and is the best way to manipulate sidebar data.
     */
    const FILTER_WIDGETS = "blog_filter_sidebar";
}