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
 * @author Jesse Maxwell
 * @copyright Swift Otter Studios, 4/29/16
 */

var TagManager = function(tags, tagPrototypeId, viewerPrototypeId) {
    this.tags = tags;
    this.tagPrototype = document.getElementById(tagPrototypeId);
    this.viewerPrototype = document.getElementById(viewerPrototypeId);

    if (this.tags.length > 0) {
        window.addEventListener('load', this.initializeTags.bind(this));
    }
};

TagManager.prototype = {
    containers: {},
    initializeTags: function() {
        if (typeof jQuery !== "undefined") {
            this.$ = jQuery;
            this.tagImage();
        }
    },

    tagImage: function() {
        this.createTags()
            .createTagViewer()
            .addEventHandlers();
    },

    createTagViewer: function() {
        var property;

        for (property in this.containers) {
            if (this.containers.hasOwnProperty(property)) {
                this.createViewerElement(this.containers[property], property);
            }
        }

        return this;
    },
    
    createTags: function() {
        for (var i = 0; i < this.tags.length; i++) {
            this.getContainer(this.tags[i].id).append(this.getTagElement(this.tags[i], i));
        }

        return this;
    },

    addEventHandlers: function() {

        return this;
    },

    createViewerElement: function(container, id) {
        var $newViewer = this.$(this.viewerPrototype).clone();

        $newViewer.attr('id', 'tag_viewer_' + id);

        container.append($newViewer);
    },

    getTagElement: function(tag, index) {
        var newTag = this.tagPrototype.cloneNode(true);
        newTag.setAttribute('id', 'product_tag_' + tag.id + "_" + index);
        newTag.setAttribute('data-index', index);
        newTag.setAttribute('data-image-id', tag.id);
        newTag.setAttribute('data-name', tag.name);
        newTag.setAttribute('data-image', tag.image);
        newTag.setAttribute('href', tag.url);
        newTag.setAttribute('style', this.getTagStyle(tag));

        return newTag;
    },

    getTagStyle: function(tag) {
        var style = "top: " + (tag.top * 100) + "%; ";
            style += "left: " + (tag.left * 100) + "%";

        return style;
    },

    getContainer: function(id) {
        if (!this.containers[id]) {
            var $image = this.$('.wp-image-' + id).wrap('<div class="product-tag-image-container"></div>');
            this.containers[id] = $image.parent().addClass(this.getContainerClasses($image));

            this.containers[id].on('click touchstart', '.product-tag', this.handleClick.bind(this, id));
            this.containers[id].on('mouseenter', '.product-tag', this.openViewer.bind(this, id));
            this.containers[id].on('mouseleave', this.closeViewer.bind(this, id));
        }

        return this.containers[id];
    },

    getContainerClasses: function($image) {
        var containerClass = "",
            classes = $image.attr('class').split(/\s/);

        for (var i = 0, len = classes.length; i < len; i++) {
            if (/^align|^size/.test(classes[i])) {
                containerClass += " " + classes[i];
            }
        }

        return containerClass;
    },

    openViewer: function(containerId, e) {
        this.containers[containerId].opened = true;
        this.updateViewer(this.$(e.currentTarget), this.$(e.delegateTarget), containerId);
    },

    closeViewer: function(containerId, e) {
        this.containers[containerId].opened = false;
        this.$(e.delegateTarget).removeClass('viewer-open').find('.tag-data').addClass('off');
    },

    handleClick: function(containerId, e) {
        if (!this.containers[containerId].opened || this.containers[containerId].currentTag !== parseInt(this.$(e.currentTarget).data('index'))) {
            e.preventDefault();
            this.openViewer(containerId, e);
        }
    },

    updateViewer: function($tagElement, $containerElement, containerId) {
        var $viewer = $containerElement.find('.tag-data');

        $viewer.attr('href', $tagElement.attr('href'));
        $viewer.find('.tag-data-name').text($tagElement.data('name'));
        this.updateImage($viewer, $tagElement.data('image'), $tagElement.data('name'));
        $viewer.removeClass('off');
        $containerElement.addClass('viewer-open');
        this.containers[containerId].currentTag = parseInt($tagElement.data('index'));
    },

    updateImage: function($viewer, src, alt) {
        var $image = $viewer.find('img');

        if (!$image.length) {
            $image = this.$('<img />');
            $viewer.prepend($image);
        }

        $image.attr('src', src).attr('alt', name);

        return $image;
    }
};