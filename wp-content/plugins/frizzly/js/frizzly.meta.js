(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

var _tabs = require('./tabs');

var _tabs2 = _interopRequireDefault(_tabs);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

(function ($) {
    var settings = window.frizzly_meta;
    var tabs = new _tabs2.default($, settings.i18n);
    tabs.init();
})(jQuery);

},{"./tabs":2}],2:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Tabs = function () {
    function Tabs($, i18n) {
        _classCallCheck(this, Tabs);

        this.$ = $;
        this.i18n = i18n;
    }

    _createClass(Tabs, [{
        key: 'init',
        value: function init() {
            var _this = this;

            this.$(document).ready(function () {
                return _this.ready();
            });
        }
    }, {
        key: 'ready',
        value: function ready() {
            var baseSelector = '.frizzly-tabs-container';
            var that = this;
            this.$(baseSelector + ' .frizzly-tabs a').click(function (e) {
                var target = that.$(this).attr('data-frizzly-id');
                that.$('.frizzly-tabs li').removeClass('frizzly-tab-active');
                that.$(this).parents('li').addClass('frizzly-tab-active');
                that.$('.frizzly-tabs-container .frizzly-tab-panel').hide();
                that.$('#' + target).show();
                e.preventDefault();
                e.stopPropagation();
                return false;
            });

            this.$(baseSelector + ' .frizzly-image-selector').click(function () {
                that.upload(that.$(this));
            });
        }
    }, {
        key: 'upload',
        value: function upload($btn) {
            var _this2 = this;

            this.fileFrame = this.fileFrame || null;
            var networkName = $btn.attr('data-frizzly-network');
            if (this.fileFrame) {
                this.fileFrame.open();
                return;
            }

            this.fileFrame = wp.media.frames.file_frame = wp.media({
                title: this.i18n.select_image.title,
                button: {
                    text: this.i18n.select_image.text
                },
                multiple: false
            }).on('select', function () {
                var image = _this2.fileFrame.state().get('selection').first().toJSON();
                _this2.$('#frizzly_' + networkName + '_image').val(image.url);
            });

            this.fileFrame.open();
        }
    }]);

    return Tabs;
}();

exports.default = Tabs;

},{}]},{},[1])
