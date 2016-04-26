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
 * @copyright Swift Otter Studios, 3/3/16
 * @package   default
 **/

$pageContent = <<<EOD
<h3>We're sorry.</h3>
<dl><dt>The blog page you requested either was not found, or there was an error.</dt><dd>
<ul class="disc">
<li>If you typed the URL directly, please make sure the spelling is correct.</li>
<li>If you clicked on a link to get here, the link is outdated.</li>
</ul>
</dd></dl><dl><dt>What can you do?</dt><dd>Here are some ways that you can get back on track.</dd><dd>
<ul class="disc">
<li><a href="/blog/">Try going to the blog main page.</a></li>
<li><a onclick="history.go(-1); return false;" href="#">Go back</a> to the previous page.</li>
</ul>
</dd></dl>

<a href="/blog/" class="link-as-button">Pleasant Hill Grain Blog</a>
EOD;

$pages = [
    [
        'title'         => '404 Blog Error',
        'root_template' => 'two_columns_right',
        'identifier'    => 'blog-no-route',
        'stores'        => array(0),
        'content_heading' => '404 Blog Page Not Found',
        'content'       => $pageContent,
        'parent_id'     => 2,
        'level'         => 2,
    ]
];

foreach ($pages as $page) {
    Mage::getModel('cms/page')->setData($page)->save();
}