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

/** @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('SwiftOtter_Blog/Category')}`;
    CREATE TABLE `{$installer->getTable('SwiftOtter_Blog/Category')}` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `slug` VARCHAR(50) NOT NULL,
        `title` VARCHAR(50) NOT NULL,
        `description` VARCHAR(255) NOT NULL,
        `parent` INT UNSIGNED NOT NULL,
        `post_count` INT UNSIGNED NOT NULL,
        PRIMARY KEY(id)
    );
");

$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('SwiftOtter_Blog/DateIndex')}`;
    CREATE TABLE `{$installer->getTable('SwiftOtter_Blog/DateIndex')}` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `year` INT UNSIGNED NOT NULL,
        `month` INT UNSIGNED NOT NULL,
        `post_count` INT UNSIGNED NOT NULL,
        PRIMARY KEY(id)
    );
");

$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('SwiftOtter_Blog/Tag')}`;
    CREATE TABLE `{$installer->getTable('SwiftOtter_Blog/Tag')}` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `slug` VARCHAR(50) NOT NULL,
        `title` VARCHAR(50) NOT NULL,
        `description` VARCHAR(255) NOT NULL,
        `post_count` INT UNSIGNED NOT NULL,
        PRIMARY KEY(id)
    );
");

$installer->endSetup();