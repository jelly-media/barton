(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Debugger = function () {
    function Debugger(pluginName) {
        var _this = this;

        _classCallCheck(this, Debugger);

        this.flags = {};
        this.pluginName = pluginName;
        var print = typeof console !== 'undefined' && typeof console.log !== 'undefined';
        var stringifyExists = typeof JSON !== 'undefined' && typeof JSON.stringify === 'function';

        if (print) {
            this.logString = function (msg) {
                _this.log(msg);
            };
            this.logObject = stringifyExists ? function (obj) {
                _this.log(JSON.stringify(obj, null, 4));
            } : function (obj) {
                return _this.simplelogObject(obj);
            };
        } else {
            this.logString = function () {};
            this.logObject = function () {};
        }
    }

    _createClass(Debugger, [{
        key: 'getFlag',
        value: function getFlag(flagName) {
            return !!this.flags[flagName];
        }
    }, {
        key: 'log',
        value: function log(str) {
            if (!this.getFlag('print')) return;
            console.log(this.pluginName + ' debug: ' + str);
        }
    }, {
        key: 'setFlag',
        value: function setFlag(flagName) {
            this.flags[flagName] = true;
        }
    }, {
        key: 'simplelogObject',
        value: function simplelogObject(obj) {
            if (!this.getFlag('print')) return;
            var res = Object.keys(obj).filter(function (key) {
                return obj.hasOwnPrototype(key);
            }).map(function (key) {
                return key + ': ' + obj[key] + '\n';
            }).join();
            this.log(res);
        }
    }]);

    return Debugger;
}();

exports.default = Debugger;

},{}],2:[function(require,module,exports){
'use strict';

var _debug = require('./debug');

var _debug2 = _interopRequireDefault(_debug);

var _modal = require('./modal');

var _modal2 = _interopRequireDefault(_modal);

var _share = require('./share');

var _share2 = _interopRequireDefault(_share);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

(function ($) {
    var settings = window.frizzlySettings;
    var debug = new _debug2.default('frizzly');
    var shareModule = new _share2.default($, settings, debug);
    window.frizzlyDebugger = debug;
})(jQuery);

},{"./debug":1,"./modal":3,"./share":15}],3:[function(require,module,exports){
'use strict';


(function (factory) {
    factory(jQuery, window, document);
})(function ($, window, document, undefined) {

    var modals = [],
        getCurrent = function getCurrent() {
        return modals.length ? modals[modals.length - 1] : null;
    },
        selectCurrent = function selectCurrent() {
        var i,
            selected = false;
        for (i = modals.length - 1; i >= 0; i--) {
            if (modals[i].$blocker) {
                modals[i].$blocker.toggleClass('frizzly-current', !selected).toggleClass('frizzly-behind', selected);
                selected = true;
            }
        }
    };

    $.modal = function (el, options) {
        this.$body = $('body');
        this.options = $.extend({}, $.modal.defaults, options);
        this.options.doFade = !isNaN(parseInt(this.options.fadeDuration, 10));
        this.$blocker = null;
        if (this.options.closeExisting) while ($.modal.isActive()) {
            $.modal.close();
        } 
        modals.push(this);
        this.$elm = el;
        this.$body.append(this.$elm);
        this.open();
    };

    $.modal.prototype = {
        constructor: $.modal,

        open: function open() {
            var m = this;
            this.block();
            if (this.options.doFade) {
                setTimeout(function () {
                    m.show();
                }, this.options.fadeDuration * this.options.fadeDelay);
            } else {
                this.show();
            }
            $(document).off('keydown.modal').on('keydown.modal', function (event) {
                var current = getCurrent();
                if (event.which == 27 && current.options.escapeClose) current.close();
            });
            if (this.options.clickClose) this.$blocker.click(function (e) {
                if (e.target == this) $.modal.close();
            });
        },

        close: function close() {
            modals.pop();
            this.unblock();
            this.hide();
            if (!$.modal.isActive()) $(document).off('keydown.modal');
        },

        block: function block() {
            this.$elm.trigger($.modal.BEFORE_BLOCK, [this._ctx()]);
            this.$body.css('overflow', 'hidden');
            this.$blocker = $('<div class="frizzly-blocker frizzly-current"></div>').appendTo(this.$body);
            selectCurrent();
            if (this.options.doFade) {
                this.$blocker.css('opacity', 0).animate({ opacity: 1 }, this.options.fadeDuration);
            }
            this.$elm.trigger($.modal.BLOCK, [this._ctx()]);
        },

        unblock: function unblock(now) {
            if (!now && this.options.doFade) this.$blocker.fadeOut(this.options.fadeDuration, this.unblock.bind(this, true));else {
                this.$blocker.children().appendTo(this.$body);
                this.$blocker.remove();
                this.$blocker = null;
                selectCurrent();
                if (!$.modal.isActive()) this.$body.css('overflow', '');
            }
        },

        show: function show() {
            this.$elm.trigger($.modal.BEFORE_OPEN, [this._ctx()]);
            if (this.options.showClose) {
                this.closeButton = $('<a href="#close-modal" rel="modal:close" class="frizzly-close-modal ' + this.options.closeClass + '">' + this.options.closeText + '</a>');
                this.$elm.append(this.closeButton);
            }
            this.$elm.addClass(this.options.modalClass).appendTo(this.$blocker);
            if (this.options.doFade) {
                this.$elm.css('opacity', 0).show().animate({ opacity: 1 }, this.options.fadeDuration);
            } else {
                this.$elm.show();
            }
            this.$elm.trigger($.modal.OPEN, [this._ctx()]);
        },

        hide: function hide() {
            this.$elm.trigger($.modal.BEFORE_CLOSE, [this._ctx()]);
            if (this.closeButton) this.closeButton.remove();
            var _this = this;
            if (this.options.doFade) {
                this.$elm.fadeOut(this.options.fadeDuration, function () {
                    _this.$elm.trigger($.modal.AFTER_CLOSE, [_this._ctx()]);
                });
            } else {
                this.$elm.hide(0, function () {
                    _this.$elm.trigger($.modal.AFTER_CLOSE, [_this._ctx()]);
                });
            }
            this.$elm.trigger($.modal.CLOSE, [this._ctx()]);
        },

        showSpinner: function showSpinner() {
            if (!this.options.showSpinner) return;
            this.spinner = this.spinner || $('<div class="' + this.options.modalClass + '-spinner"></div>').append(this.options.spinnerHtml);
            this.$body.append(this.spinner);
            this.spinner.show();
        },

        hideSpinner: function hideSpinner() {
            if (this.spinner) this.spinner.remove();
        },

        _ctx: function _ctx() {
            return { elm: this.$elm, $blocker: this.$blocker, options: this.options };
        }
    };

    $.modal.close = function (event) {
        if (!$.modal.isActive()) return;
        if (event) event.preventDefault();
        var current = getCurrent();
        current.close();
        return current.$elm;
    };

    $.modal.isActive = function () {
        return modals.length > 0;
    };

    $.modal.getCurrent = getCurrent;

    $.modal.defaults = {
        closeExisting: true,
        escapeClose: true,
        clickClose: true,
        closeText: 'Close',
        closeClass: '',
        modalClass: "frizzly-modal",
        spinnerHtml: null,
        showSpinner: true,
        showClose: true,
        fadeDuration: null, 
        fadeDelay: 1.0 
    };

    $.modal.BEFORE_BLOCK = 'modal:before-block';
    $.modal.BLOCK = 'modal:block';
    $.modal.BEFORE_OPEN = 'modal:before-open';
    $.modal.OPEN = 'modal:open';
    $.modal.BEFORE_CLOSE = 'modal:before-close';
    $.modal.CLOSE = 'modal:close';
    $.modal.AFTER_CLOSE = 'modal:after-close';

    $.fn.modal = function (options) {
        if (this.length === 1) {
            new $.modal(this, options);
        }
        return this;
    };

    $(document).on('click.modal', 'a[rel~="modal:close"]', $.modal.close);
    $(document).on('click.modal', 'a[rel~="modal:open"]', function (event) {
        event.preventDefault();
        $(this).modal();
    });
});

},{}],4:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var EmailSharer = function () {
    function EmailSharer($, i18n) {
        _classCallCheck(this, EmailSharer);

        this.$ = $;
        this.i18n = i18n;
        this.responseClassPrefix = 'frizzly-share-email-response-';
        this.html = this.createForm();
    }

    _createClass(EmailSharer, [{
        key: 'cleanInputs',
        value: function cleanInputs() {
            this.html.sourceEmail.val('');
            this.html.targetEmail.val('');
            this.html.sourceName.val('');
        }
    }, {
        key: 'createForm',
        value: function createForm() {
            var _this = this;

            var response = this.$('<p />', {});

            var targetEmailLabel = this.$('<label />', {
                class: 'frizzly-share-email-header',
                for: 'frizzly-share-email-target-email',
                html: this.i18n.targetEmailLabel
            });

            var targetEmail = this.$('<input />', {
                class: 'frizzly-share-email-input',
                type: 'text',
                id: 'frizzly-share-email-target-email'
            });

            var sourceEmailLabel = this.$('<label />', {
                class: 'frizzly-share-email-input',
                type: 'text',
                for: 'frizzly-share-email-source-email',
                html: this.i18n.sourceEmailLabel
            });

            var sourceEmail = this.$('<input />', {
                class: 'frizzly-share-email-input',
                type: 'text',
                id: 'frizzly-share-email-source-email'
            });

            var sourceNameLabel = this.$('<label />', {
                class: 'frizzly-share-email-input',
                for: 'frizzly-share-email-source-name',
                html: this.i18n.sourceNameLabel
            });

            var sourceName = this.$('<input />', {
                class: 'frizzly-share-email-input',
                type: 'text',
                id: 'frizzly-share-email-source-name'
            });

            var btn = this.$('<div />', {
                class: 'frizzly-share-email-submit-container',
                html: this.$('<input />', {
                    class: 'frizzly-share-email-submit',
                    type: 'submit',
                    value: this.i18n.button
                })
            }).click(function () {
                var data = {
                    action: _this.i18n.ajax_action,
                    nonce: _this.i18n.ajax_nonce,
                    postId: _this.postId,
                    toEmail: _this.html.targetEmail.val(),
                    fromEmail: _this.html.sourceEmail.val(),
                    fromName: _this.html.sourceName.val()
                };
                _this.$.post(_this.i18n.ajax_url, data, function (_ref) {
                    var status = _ref.status;
                    var message = _ref.message;

                    _this.cleanInputs();
                    if (!status) {
                        _this.html.response.attr('class', _this.responseClassPrefix + 'error').html(_this.i18n.unknown_error);
                    } else {
                        _this.html.response.attr('class', '' + _this.responseClassPrefix + status).html(message);
                    }
                });
            });
            var form = this.$('<div />', { class: 'frizzly-share-email' }).append(response).append(targetEmailLabel).append(targetEmail).append(sourceEmailLabel).append(sourceEmail).append(sourceNameLabel).append(sourceName).append(btn);

            return {
                targetEmail: targetEmail,
                sourceEmail: sourceEmail,
                sourceName: sourceName,
                response: response,
                form: form
            };
        }
    }, {
        key: 'share',
        value: function share(postId) {
            this.html.response.html('');
            this.cleanInputs();
            this.postId = postId;
            this.$(this.html.form).modal();
            return false;
        }
    }]);

    return EmailSharer;
}();

exports.default = EmailSharer;

},{}],5:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Functions = function () {
    function Functions() {
        _classCallCheck(this, Functions);
    }

    _createClass(Functions, null, [{
        key: 'openWindow',
        value: function openWindow(href, e, $) {
            var isHrefUrl = href.slice(-1) !== '#' && href.indexOf('http') === 0;
            if (isHrefUrl) {
                e.preventDefault();
                e.stopPropagation();
                window.open(href, 'mw' + e.timeStamp, 'left=20,top=20,width=800,height=500,toolbar=1,resizable=0');
            }
            return !isHrefUrl;
        }
    }]);

    return Functions;
}();

exports.default = Functions;

},{}],6:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _functions = require('./../common/functions');

var _functions2 = _interopRequireDefault(_functions);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var ContentModule = function () {
    function ContentModule($, settings, emailSharer, logger) {
        _classCallCheck(this, ContentModule);

        this.settings = settings;
        this.emailSharer = emailSharer;
        this.$ = $;
        this.logger = logger;
    }

    _createClass(ContentModule, [{
        key: 'init',
        value: function init() {
            this.config = {
                attributesToCopy: ['^alt$', '^title$', 'data-frizzly-content-share-pinterest'],
                cssPrefix: 'frizzly_pinmarklet',
                minImgSize: 100,
                thumbCellSize: 250
            };
            this.structure = null;
            this.saveScrollTop = 0;

            var that = this;
            this.$(document).on('click', '.frizzly-content a', function (e) {
                var $btn = that.$(this);
                if ($btn.attr('data-frizzly-pinmarklet')) {
                    var $images = that.$('img[data-frizzly-content-post-id="' + $btn.attr('data-frizzly-pinmarklet') + '"]');
                    if ($images.length) {
                        that.openPinmarklet($images);
                    } else {
                        alert(that.settings.i18n.pinmarklet.no_images);
                    }
                    return false;
                } else {
                    var href = $btn.attr('href');
                    if (href.indexOf('mailto') === 0) {
                        return that.emailSharer.share($btn.attr('data-frizzly-content-post-id'));
                    }
                    return _functions2.default.openWindow(href, e, that.$);
                }
            });
        }
    }, {
        key: 'close',
        value: function close() {
            if (this.structure) {
                this.structure.bg.remove();
                this.structure.bd.remove();
                this.structure = null;
            }
            window.scroll(0, this.saveScrollTop);
        }
    }, {
        key: 'createStructure',
        value: function createStructure() {
            var _this = this;

            var hd = this.$('<div />', { class: this.config.cssPrefix + '_hd' }).append(this.$('<span />', { class: this.config.cssPrefix + '_logo' })).append(this.$('<span />', {
                class: this.config.cssPrefix + '_title',
                html: this.settings.i18n.pinmarklet.choose
            })).append(this.$('<a />', {
                class: this.config.cssPrefix + '_x'
            }).click(function () {
                return _this.close();
            }));

            this.structure = {
                bd: this.$('<div />', { class: this.config.cssPrefix + '_bd' }).append(hd),
                bg: this.$('<div />', { class: this.config.cssPrefix + '_bg' }).click(function () {
                    return _this.close();
                })
            };

            this.$(document.body).append(this.structure.bg).append(this.structure.bd);
            window.scroll(0, 0);
        }
    }, {
        key: 'createThumb',
        value: function createThumb($img, realHeight, realWidth) {
            var _this2 = this;

            var image = this.$('<img />', {
                css: { visibility: 'hidden' },
                src: $img.prop('src')
            });

            [].filter.call($img[0].attributes, function (att) {
                return _this2.config.attributesToCopy.some(function (regex) {
                    return new RegExp(regex).test(att.name);
                });
            }).map(function (att) {
                return att.name;
            }).forEach(function (attName) {
                image.attr(attName, $img.attr(attName));
            });

            image.load(function () {
                return _this2.resizeImage(image);
            });

            var link = this.$('<a />', {
                href: image.attr('data-frizzly-content-share-pinterest')
            }).append(image).append(this.$('<span />', {
                html: realHeight + ' x ' + realWidth,
                class: this.config.cssPrefix + '_resolution'
            }));

            var button = this.$('<span />', {
                class: this.config.cssPrefix + '_button',
                html: this.$('<div />', {
                    class: 'frizzly-button-container frizzly-button-size-' + this.settings.button_size + ' frizzly-theme-' + this.settings.button_shape,
                    css: { 'align-self': 'center' },
                    html: this.$('<a />', {
                        class: 'frizzly-button frizzly-pinterest',
                        html: this.$('<i />', {
                            class: 'fa fa-fw fa-pinterest'
                        })
                    })
                })
            });

            var container = this.$('<span />', { class: this.config.cssPrefix + '_pincontainer' }).append(link).append(button).click(function (e) {
                _functions2.default.openWindow(link.attr('href'), e);
                setTimeout(function () {
                    return _this2.close();
                }, 10);
            }).hover(function () {
                return button.css('display', 'flex');
            }, function () {
                return button.hide();
            });

            return container;
        }
    }, {
        key: 'loadParamImage',
        value: function loadParamImage(img) {
            var that = this;
            this.$('<img />', {
                src: img.src
            }).load(function () {
                if (this.height > that.config.minImgSize && this.width > that.config.minImgSize) {
                    var container = that.createThumb(that.$(img), this.height, this.width);
                    that.structure.bd.append(container);
                }
            });
        }
    }, {
        key: 'resizeImage',
        value: function resizeImage(img) {
            var width = img.width(),
                height = img.height();
            if (width >= height) {
                height = height * Math.min(this.config.thumbCellSize / width, 1);
                width = Math.min(width, this.config.thumbCellSize);
            } else {
                width = width * Math.min(this.config.thumbCellSize / height, 1);
                height = Math.min(height, this.config.thumbCellSize);
            }
            this.$(img).css({
                height: height,
                width: width,
                visibility: '',
                marginTop: -Math.min(height, this.config.thumbCellSize) / 2 + 'px',
                marginLeft: -Math.min(width, this.config.thumbCellSize) / 2 + 'px'
            });
        }
    }, {
        key: 'openPinmarklet',
        value: function openPinmarklet($images) {
            var _this3 = this;

            if (this.structure) return;
            this.saveScrollTop = window.pageYOffset;
            this.createStructure();
            $images.each(function (id, img) {
                return _this3.loadParamImage(img);
            });
        }
    }]);

    return ContentModule;
}();

exports.default = ContentModule;

},{"./../common/functions":5}],7:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _showStrategy = require('./show-strategy');

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var ImageModule = function () {
    function ImageModule($, settings, emailSharer, logger) {
        _classCallCheck(this, ImageModule);

        this.settings = settings;
        this.$ = $;
        this.emailSharer = emailSharer;
        this.logger = logger;
    }

    _createClass(ImageModule, [{
        key: 'init',
        value: function init() {
            this.$('input.frizzly').closest('div').addClass('frizzly_container');
            this.showStrategy = this.getStrategy();
            this.showStrategy.start();
        }
    }, {
        key: 'getStrategy',
        value: function getStrategy() {
            var F = this.settings.show === 'always' ? _showStrategy.ShowAlwaysStrategy : _showStrategy.ShowOnHoverStrategy;
            return new F(this.$, this.settings, this.emailSharer, this.logger);
        }
    }]);

    return ImageModule;
}();

exports.default = ImageModule;

},{"./show-strategy":10}],8:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _functions = require('./../../common/functions');

var _functions2 = _interopRequireDefault(_functions);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var ButtonGenerator = function () {
    function ButtonGenerator($, settings, emailSharer) {
        _classCallCheck(this, ButtonGenerator);

        this.$ = $;
        this.settings = settings;
        this.emailSharer = emailSharer;
        this.$element = this.$('<div />', {
            class: 'frizzly-button-container frizzly-media frizzly-button-size-' + this.settings.button_size + ' frizzly-theme-' + this.settings.button_shape
        });

        this.size = this.computeSize();
    }

    _createClass(ButtonGenerator, [{
        key: 'generateButtons',
        value: function generateButtons($image) {
            var _this = this;

            return this.settings.networks.map(function (networkName) {
                var link = _this.getNetworkLink($image, networkName);
                if (!link) return null;
                return _this.$('<a />', {
                    class: 'frizzly-button frizzly-' + networkName,
                    target: _this.getTargetAttribute(networkName),
                    href: link,
                    html: '<i class="fa fa-fw ' + _this.getNetworkClass(networkName) + '"></i>'
                }).click(function (e) {
                    var href = e.currentTarget.href;
                    if (href.indexOf('mailto') === 0) {
                        return _this.emailSharer.share($image.attr('data-frizzly-image-post-id'));
                    }
                    return _functions2.default.openWindow(e.currentTarget.href, e, _this.$);
                });
            }).filter(function (e) {
                return !!e;
            });
        }
    }, {
        key: 'getNetworkClass',
        value: function getNetworkClass(networkName) {
            switch (networkName) {
                case 'email':
                    return 'fa-envelope-o';
                case 'googleplus':
                    return 'fa-google-plus';
                default:
                    return 'fa-' + networkName;
            }
        }
    }, {
        key: 'getNetworkLink',
        value: function getNetworkLink($img, networkName) {
            return $img.attr('data-frizzly-image-share-' + networkName) || '';
        }
    }, {
        key: 'getTargetAttribute',
        value: function getTargetAttribute(networkName) {
            switch (networkName) {
                case 'email':
                    return '_top';
                default:
                    return '_blank';
            }
        }
    }, {
        key: 'computeSize',
        value: function computeSize() {
            var numberOfButtons = this.settings.networks.length;
            var baseHeight = 48;
            var baseWidth = 100;
            var size = this.settings.button_size;
            var theme = this.settings.button_shape;

            var sizes = {
                xsmall: 0.5,
                small: 0.8,
                normal: 1,
                large: 1.5,
                xlarge: 2
            };

            var themes = {
                square: baseHeight,
                rounded: baseHeight,
                round: baseHeight,
                'rounded-rectangle': baseWidth,
                rectangle: baseWidth
            };
            return {
                height: Math.round(baseHeight * sizes[size]),
                width: Math.round(numberOfButtons * themes[theme] * sizes[size])
            };
        }
    }, {
        key: 'createButtons',
        value: function createButtons($image) {
            var $elem = this.$element.clone(false);
            $elem.html(this.generateButtons($image));

            return {
                $element: $elem,
                size: this.size
            };
        }
    }]);

    return ButtonGenerator;
}();

exports.default = ButtonGenerator;

},{"./../../common/functions":5}],9:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var ImageFilter = function () {
    function ImageFilter(settings) {
        _classCallCheck(this, ImageFilter);

        this.minWidth = settings.desktop_min_width;
        this.minHeight = settings.desktop_min_height;
        this.classes = settings.image_classes ? this.settings.image_classes.split(',') : [];
        this.positive = settings.image_classes_positive;
    }

    _createClass(ImageFilter, [{
        key: 'checkClass',
        value: function checkClass($img) {
            var _this = this;

            return this.classes.length === 0 || this.classes.reduce(function (res, name) {
                return res && $img.hasClass(name) === _this.positive;
            }, true);
        }
    }, {
        key: 'imageEligible',
        value: function imageEligible($img) {
            return this.checkClass($img) && $img[0].clientWidth >= this.minWidth && $img[0].clientHeight >= this.minWidth;
        }
    }]);

    return ImageFilter;
}();

exports.default = ImageFilter;

},{}],10:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.ShowOnHoverStrategy = exports.ShowAlwaysStrategy = undefined;

var _showAlwaysStrategy = require('./show-always-strategy');

var _showAlwaysStrategy2 = _interopRequireDefault(_showAlwaysStrategy);

var _showOnHoverStrategy = require('./show-on-hover-strategy');

var _showOnHoverStrategy2 = _interopRequireDefault(_showOnHoverStrategy);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.ShowAlwaysStrategy = _showAlwaysStrategy2.default;
exports.ShowOnHoverStrategy = _showOnHoverStrategy2.default;

},{"./show-always-strategy":12,"./show-on-hover-strategy":13}],11:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var topPosition = function topPosition(topLeft, bottomRight, containerSize, margins) {
    return topLeft.top + margins.top;
};

var leftPosition = function leftPosition(topLeft, bottomRight, containerSize, margins) {
    return topLeft.left + margins.left;
};

var bottomPosition = function bottomPosition(topLeft, bottomRight, containerSize, margins) {
    return bottomRight.top - containerSize.height - margins.bottom;
};

var rightPosition = function rightPosition(topLeft, bottomRight, containerSize, margins) {
    return bottomRight.left - margins.right - containerSize.width;
};

var topCenterPosition = function topCenterPosition(topLeft, bottomRight, containerSize) {
    return topLeft.top + ((bottomRight.top - topLeft.top) / 2 - containerSize.height / 2);
};

var leftCenterPosition = function leftCenterPosition(topLeft, bottomRight, containerSize) {
    return topLeft.left + ((bottomRight.left - topLeft.left) / 2 - containerSize.width / 2);
};

var PositionCalculator = function () {
    function PositionCalculator(topFunc, leftFunc) {
        _classCallCheck(this, PositionCalculator);

        this.topF = topFunc;
        this.leftF = leftFunc;
    }

    _createClass(PositionCalculator, [{
        key: 'calculate',
        value: function calculate(topLeft, bottomRight, containerSize, margins) {
            return {
                top: this.topF(topLeft, bottomRight, containerSize, margins),
                left: this.leftF(topLeft, bottomRight, containerSize, margins)
            };
        }
    }]);

    return PositionCalculator;
}();

var Positioner = function () {
    function Positioner(position, margins) {
        _classCallCheck(this, Positioner);

        this.margins = margins;
        this.positionCalculator = this.getPositionCalculator(position);
    }

    _createClass(Positioner, [{
        key: 'getPositionCalculator',
        value: function getPositionCalculator(position) {
            switch (position) {
                case 'top-left':
                    return new PositionCalculator(topPosition, leftPosition);
                case 'top-right':
                    return new PositionCalculator(topPosition, rightPosition);
                case 'bottom-left':
                    return new PositionCalculator(bottomPosition, leftPosition);
                case 'bottom-right':
                    return new PositionCalculator(bottomPosition, rightPosition);
                case 'center':
                default:
                    return new PositionCalculator(topCenterPosition, leftCenterPosition);
            }
        }
    }, {
        key: 'calculatePosition',
        value: function calculatePosition(topLeft, bottomRight, containerSize) {
            return this.positionCalculator.calculate(topLeft, bottomRight, containerSize, this.margins);
        }
    }]);

    return Positioner;
}();

exports.default = Positioner;

},{}],12:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var ShowAlwaysStrategy = function ShowAlwaysStrategy(settings) {
    _classCallCheck(this, ShowAlwaysStrategy);

    this.settings = settings;
};

exports.default = ShowAlwaysStrategy;

},{}],13:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _showStrategy = require('./show-strategy');

var _showStrategy2 = _interopRequireDefault(_showStrategy);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var ShowOnHoverStrategy = function (_ShowStrategy) {
    _inherits(ShowOnHoverStrategy, _ShowStrategy);

    function ShowOnHoverStrategy() {
        _classCallCheck(this, ShowOnHoverStrategy);

        return _possibleConstructorReturn(this, Object.getPrototypeOf(ShowOnHoverStrategy).apply(this, arguments));
    }

    _createClass(ShowOnHoverStrategy, [{
        key: 'start',
        value: function start() {
            var that = this;
            var indexerAttr = 'data-frizzly-indexer';
            var timeoutAttr = 'data-frizzly-timeout';
            var indexer = 0;

            var containerSelector = function containerSelector(idx) {
                return that.$('.frizzly-button-container[' + indexerAttr + '="' + idx + '"]');
            };
            that.$(document).delegate(this.settings.image_selector, 'mouseenter', function handleHover() {
                var $image = that.$(this);

                if (!that.imageFilter.imageEligible($image)) return;
                var currentIndexer = $image.attr(indexerAttr);
                if (!currentIndexer) {
                    currentIndexer = indexer++;
                    $image.attr(indexerAttr, currentIndexer);
                }

                var $container = containerSelector(currentIndexer);
                if ($container.length === 0) {
                    (function () {
                        var _that$buttonGenerator = that.buttonGenerator.createButtons($image);

                        var $element = _that$buttonGenerator.$element;
                        var size = _that$buttonGenerator.size;

                        var topLeft = $image.offset();
                        var bottomRight = {
                            top: topLeft.top + $image[0].clientHeight,
                            left: topLeft.left + $image[0].clientWidth
                        };
                        var offset = that.positioner.calculatePosition(topLeft, bottomRight, size);

                        $image.after($element);
                        $element.attr(indexerAttr, currentIndexer).css('visibility', 'hidden').show().offset(offset).css('visibility', 'visible').hover(function () {
                            return clearTimeout($element.attr(timeoutAttr));
                        }, function () {
                            return $element.attr(timeoutAttr, setTimeout(function () {
                                $element.remove();
                            }, 100));
                        });
                    })();
                } else {
                    clearTimeout($container.attr(timeoutAttr));
                }
            });

            that.$(document).delegate(this.settings.image_selector, 'mouseleave', function handleLeave() {
                if (that.logger.getFlag('image_prevent_hide')) {
                    return;
                }
                var $image = that.$(this);
                var currentIndexer = $image.attr(indexerAttr);
                if (!currentIndexer) return;

                var $container = containerSelector(currentIndexer);
                $container.attr(timeoutAttr, setTimeout(function () {
                    $container.remove();
                }, 100));
            });
        }
    }]);

    return ShowOnHoverStrategy;
}(_showStrategy2.default);

exports.default = ShowOnHoverStrategy;

},{"./show-strategy":14}],14:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _imageFilter = require('./image-filter');

var _imageFilter2 = _interopRequireDefault(_imageFilter);

var _buttonGenerator = require('./button-generator');

var _buttonGenerator2 = _interopRequireDefault(_buttonGenerator);

var _positioner = require('./positioner');

var _positioner2 = _interopRequireDefault(_positioner);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var ShowStrategy = function ShowStrategy($, settings, emailSharer, logger) {
    _classCallCheck(this, ShowStrategy);

    this.$ = $;
    this.settings = settings;
    this.emailSharer = emailSharer;
    this.logger = logger;
    this.imageFilter = new _imageFilter2.default(settings);
    this.buttonGenerator = new _buttonGenerator2.default($, settings, emailSharer);
    var margins = {
        left: settings.button_margin_left,
        top: settings.button_margin_top,
        right: settings.button_margin_right,
        bottom: settings.button_margin_bottom
    };
    this.positioner = new _positioner2.default(settings.button_position, margins);
};

exports.default = ShowStrategy;

},{"./button-generator":8,"./image-filter":9,"./positioner":11}],15:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _image = require('./image');

var _image2 = _interopRequireDefault(_image);

var _content = require('./content');

var _content2 = _interopRequireDefault(_content);

var _emailSharer = require('./common/email-sharer');

var _emailSharer2 = _interopRequireDefault(_emailSharer);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var ShareModule = function () {
    function ShareModule($, settings, logger) {
        _classCallCheck(this, ShareModule);

        this.$ = $;
        this.settings = settings;
        this.logger = logger;
        this.emailSharer = new _emailSharer2.default($, this.settings.general['i18n']['email_sharer']);

        this.init();
    }

    _createClass(ShareModule, [{
        key: 'init',
        value: function init() {
            this.imageModule = new _image2.default(this.$, this.settings.image, this.emailSharer, this.logger);
            this.imageModule.init();

            this.contentModule = new _content2.default(this.$, this.settings.content, this.emailSharer, this.logger);
            this.contentModule.init();
        }
    }]);

    return ShareModule;
}();

exports.default = ShareModule;

},{"./common/email-sharer":4,"./content":6,"./image":7}]},{},[2])
