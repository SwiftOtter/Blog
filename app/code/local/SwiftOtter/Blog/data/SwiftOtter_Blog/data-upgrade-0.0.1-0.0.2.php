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
 * @copyright Swift Otter Studios, 11/5/15
 * @package   default
 **/

$blockContent = <<<EOD
<p>Unfortunately, we couldn't find any results on the blog matching your search term. We recommend that you try performing the search in the box above in the header to search the entire site. Or, you can search the blog again below.</p>
EOD;

$blocks = array(
    array(
        'title' => 'Search Blog - No Results',
        'identifier' => 'blog_no_search_results',
        'is_active' => 1,
        'content' => $blockContent,
        'stores' => array(0)
    )
);

foreach ($blocks as $block) {
    Mage::getModel('cms/block')->setData($block)->save();
}