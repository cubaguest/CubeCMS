/**
 * Vytvoření pomocných funkcí pro Cube CMS systém
 *
 * Msg - zprávy
 * Dom - dom objekty
 * Tools - různé nástroje
 * Loader - loadery a ajax
 * Toolbox - toolbox a nástroje
 * Toolbox.Tool - toolbox a nástroje ???????????
 *
 **/
var CubeCMS = CubeCMS || {};

CubeCMS = {
   // base object
   contentSelector: '.main-content',
   lang: null,
   primaryLang: null,
   domain: null,
   toolboxTopOffset: 30,

   baseOpt: {},
   // caled in index header
   init: function (opt) {
      $.extend(this, opt);

      document.domain = this.domain;
      document.lang = this.lang;
      document.primaryLang = this.primaryLang;
      CubeCMS.ToolBox.toolboxTop = this.toolboxTopOffset;
   },
   setup: function () {
      // init messages
      this.Msg.init();
      this.Loader.init();
      this.ToolBox.init();
      this.Form.init();
      this.Modal.init();
      this.Popup.init();
   }
};

// msg class
CubeCMS.Msg = {
   infoBox: null,
   errBox: null,

   init: function () {
      this.infoBox = $('#infoMessages');
      this.errBox = $('#errMessages');
   },

   /**
    *  Zobrazí info hlášku
    */
   info: function (msg, clear) {
      if (msg == null)
         return this;
      if (typeof (clear) == 'undefined')
         clear = true;
      if (clear == true) {
         this.clear('info');
      }
      this.infoBox.prepend("<p>" + msg + "</p>").slideDown();
      $("body").scrollTop(this.infoBox.offset().top - 50);
      return this;
   },
   err: function (msg, clear) {
      if (msg == null)
         return this;
      if (typeof (clear) == 'undefined')
         clear = true;
      if (clear == true) {
         this.clear('err');
      }
      this.errBox.prepend("<p>" + msg + "</p>").slideDown();
      $("body").scrollTop(this.errBox.offset().top - 50);
      return this;
   },
   /**
    * Metoda zobrazí zprávy aplikace
    */
   show: function (dataObj) {
      this.clear('info');
      this.clear('error');
      if (typeof (dataObj.infomsg) == 'object') {
         for (var i = 0; i < dataObj.infomsg.length; i++) {
            this.info(dataObj.infomsg[i], false);
         }
      } else if (typeof (dataObj.infomsg) == 'string') {
         this.info(dataObj.infomsg);
      }
      if (typeof (dataObj.errmsg) == 'object') {
         for (var y = 0; y < dataObj.errmsg.length; y++) {
            this.err(dataObj.errmsg[y], false);
         }
      } else if (typeof (dataObj.infomsg) == 'string') {
         this.err(dataObj.errmsg);
      }
   },
   /**
    * Vymaže info zprávy
    */
   clear: function (type) {
      if (type == 'info') {
         this.infoBox.html(null).hide();
      } else if (type == "err" || type == "error") {
         this.errBox.html(null).hide();
      }

      return this;
   }
};

CubeCMS.Tools = {
   /**
    * Metoda převede řetězec na url adresu a odstraní nepovolené znaky
    */
   str2url: function (str, ucfirst) {
      str = str.toUpperCase();
      str = str.toLowerCase();

      str = str.replace(/[\u0105\u0104\u00E0\u00E1\u00E2\u00E3\u00E4\u00E5]/g, 'a');
      str = str.replace(/[\u00E7\u010D\u0107\u0106]/g, 'c');
      str = str.replace(/[\u010F]/g, 'd');
      str = str.replace(/[\u00E8\u00E9\u00EA\u00EB\u011B\u0119\u0118]/g, 'e');
      str = str.replace(/[\u00EC\u00ED\u00EE\u00EF]/g, 'i');
      str = str.replace(/[\u0142\u0141]/g, 'l');
      str = str.replace(/[\u00F1\u0148]/g, 'n');
      str = str.replace(/[\u00F2\u00F3\u00F4\u00F5\u00F6\u00F8\u00D3]/g, 'o');
      str = str.replace(/[\u0159]/g, 'r');
      str = str.replace(/[\u015B\u015A\u0161]/g, 's');
      str = str.replace(/[\u00DF]/g, 'ss');
      str = str.replace(/[\u0165]/g, 't');
      str = str.replace(/[\u00F9\u00FA\u00FB\u00FC\u016F]/g, 'u');
      str = str.replace(/[\u00FD\u00FF]/g, 'y');
      str = str.replace(/[\u017C\u017A\u017B\u0179\u017E]/g, 'z');
      str = str.replace(/[\u00E6]/g, 'ae');
      str = str.replace(/[\u0153]/g, 'oe');
      str = str.replace(/[\u013E\u013A]/g, 'l');
      str = str.replace(/[\u0155]/g, 'r');

      str = str.replace(/[^a-z0-9\s\'\:\/\[\]-]/g, '');
      str = str.replace(/[\s\'\:\/\[\]-]+/g, ' ');
      str = str.replace(/[ ]/g, '-');
      str = str.replace(/[\/]/g, '-');
      if (ucfirst == 1) {
         var c = str.charAt(0);
         str = c.toUpperCase() + str.slice(1);
      }
      return str;
   },

   /**
    * Metoda odstraní html tagy
    */
   stripHtml: function (html) {
      var tmp = document.createElement("DIV");
      tmp.innerHTML = html;
      return tmp.textContent || tmp.innerText;
   },
   /**
    * metoda provede kontrolu url klíče nad zadanou url adresou
    */
   checkUrlKey: function (checkerUrl, elem, callback) {
      var $e = $(element);
      $.ajax({
         type: "POST", url: checkerUrl, cache: false,
         data: {key: $e.val(), lang: $e.attr("lang")},
         success: function (data) {
            if (typeof data.urlkey != "undefined"
                    && $.isFunction(callback))
               callback(data.urlkey);
         }
      });
   },
   /**
    * Metoda detekuje jestli je barva tmavá nebo světlá. Podle toho vrací bílou nebo černou barvu.
    */
   contrastColor: function (color) {
      // Counting the perceptive luminance - human eye favors green color... 
      var a = 1 - (
              0.299 * parseInt((color).substring(0, 2), 16)
              + 0.587 * parseInt((color).substring(2, 4), 16)
              + 0.114 * parseInt((color).substring(4, 6), 16)) / 255;
      if (a < 0.5) {
         return "#000000"; // bright colors - black font
      } else {
         return "#ffffff"; // dark colors - white font
      }
   },
   /**
    * Načítání obsahu stránky v daném jazyce
    * @param string url
    * @param string lang
    */
   loadPageLang: function (url, lang) {
      showLoadBox(CubeCMS.contentSelector);
      $(CubeCMS.contentSelector).load(url, function (cnt) {
         var lang = url.match(/(l=[a-z]{2})/g);
         if (lang !== null) {
            $(CubeCMS.contentSelector).html(cnt.replace(lang[0], ""));
         }
         hideLoadBox();
         $(document).scroll();
      });
   },
   uniqID: {
      counter: 0,
      get: function (prefix) {
         if (!prefix) {
            prefix = "uniqid";
         }
         var id = prefix + "-" + CubeCMS.Tools.uniqID.counter++;
         if ($("#" + id).length === 0) {
            return id;
         } else {
            return CubeCMS.Tools.get(prefix);
         }
      }
   },
   formatFileSize: function (fileSizeInBytes) {
      var i = -1;
      var byteUnits = [' kB', ' MB', ' GB', ' TB', 'PB', 'EB', 'ZB', 'YB'];
      do {
         fileSizeInBytes = fileSizeInBytes / 1024;
         i++;
      } while (fileSizeInBytes > 1024);

      return Math.max(fileSizeInBytes, 0.1).toFixed(1) + byteUnits[i];
   },

   /**
    * Vrátí veliksot scrollbaru
    * @return int
    */
   getScrollBarWidth: function () {
      // Create the measurement node
      var scrollDiv = document.createElement("div");
      scrollDiv.className = "scrollbar-measure";
      document.body.appendChild(scrollDiv);
      // Get the scrollbar width
      var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;
      document.body.removeChild(scrollDiv);
      return scrollbarWidth;
   },
   rememberTabSelection: function (tabPaneSelector, useHash) {
      var key = 'selectedTabFor' + tabPaneSelector;
      if (get(key))
         $(tabPaneSelector).find('a[href="' + get(key) + '"]').tab('show');

      $(tabPaneSelector).on("click", 'a[data-toggle]', function (event) {
         set(key, this.getAttribute('href'));
      });

      function get(key) {
         return useHash ? location.hash : localStorage.getItem(key);
      }

      function set(key, value) {
         if (useHash)
            location.hash = value;
         else
            localStorage.setItem(key, value);
      }
   },
   /**
    * Spuští zadanou funkci včetně parametrů
    * @param string functionName - název funkce včeně namespace
    * @param Object context - kontext, ve kterém se funkce volá (např window)
    * @returns {unresolved}
    */
   callFunction : function(functionName, context /*, args */ ){
      var args = Array.prototype.slice.call(arguments, 2);
      var namespaces = functionName.split(".");
      var func = namespaces.pop();
      for(var i = 0; i < namespaces.length; i++) {
        context = context[namespaces[i]];
      }
      return context[func].apply(context, args);
   },
   /**
    * Spuští zadanou funkci včetně parametrů
    * @param string functionName - název funkce včeně namespace
    * @param Object context - kontext, ve kterém se funkce volá (např window)
    * @returns {unresolved}
    */
   functionExist : function(functionName, context){
      if(typeof context === undefined){
         context = window;
      }
      var args = Array.prototype.slice.call(arguments, 2);
      var namespaces = functionName.split(".");
      var func = namespaces.pop();
      for(var i = 0; i < namespaces.length; i++) {
        context = context[namespaces[i]];
      }
      
      if (typeof context === "undefined") { 
         return false;
      }
      if (typeof context[func] === "function") { 
         return true;
      }
      return false;
   }
};

CubeCMS.ToolBox = {
   enablePageTracking: true,
   toolboxTop: 30,
   init: function () {
      var that = this;
      // pokud je ajax, reinicializovat znovu toolboxy, které nejsou
      $(document).ajaxComplete(function (event, xhr, settings) {
         that.initHtml();
         that.initEvents();
         that.initLangLoader();
         that.initFixedBoxs();
      });


      this.initHtml();
   },
   initHtml: function () {
      // rodiče musí mít relativní pozici kvůli posunu
      $("div.toolbox").parent('div').css({position: 'relative'});

      // přesun toolboxu přímo do body kvůli overflow
      $('.toolbox').each(function () {
         if ($(this).data('_ccms_loaded')) {
            return;
         }
         $(this).data('_ccms_loaded', true);
         var id = CubeCMS.Tools.uniqID.get('toolbox');
         // move to body
         $(this).prop('id', id);
         $('.toolbox-button', this).prop('id', id + '-button');
         $(this).children('.toolbox-tools').prop('id', id + '-tools');
      });
      $('.toolbox>.toolbox-tools').appendTo('body');

      this.initEvents();
      this.initLangLoader();
      this.initFixedBoxs();
   },
   initEvents: function () {
      var _this = this;
      /* default events*/
      $("body").on('mouseenter', 'div.toolbox', function (event) {
         // show toolbox
         var idbase = '#' + $(this).prop('id');
         $(idbase).parent().addClass('toolbox-active-content');
         console.log(idbase);
         var $toolbox = $(idbase + '-tools');
         $toolbox.css({
            top: $(this).offset().top - 2,
            left: $(this).offset().left - $toolbox.outerWidth() + $(idbase + '-button').outerWidth() + 2,
            right: 'auto'
         }).fadeIn(100);
         // add hide touch
         $('body').on('touchstart', function () {
            $toolbox.trigger('mouseleave');
            // $('body').off('touchstart');
         });

      });
      $("body").on('mouseleave', 'div.toolbox-tools', function (e) {
         if ($(this).parent('.toolbox-tool').length === 0) {
            if ((typeof e.fromElement !== 'undefined' && !e.fromElement.length) ||
                    (typeof e.fromElement === 'undefined' && e.target.tagName !== 'SELECT')) {
               $(this).fadeOut(100);
            }
         }
         $('#' + $(this).prop('id').toString().replace('-tools', "")).parent().removeClass('toolbox-active-content');
      });
      /* mobile event */
      $("body").on('touchstart', '.toolbox-button', function (event) {
         $(this).parent().trigger('mouseenter');
         return false;
      });
      $("body").on('touchstart', '.toolbox-tools *', function (event) {
         event.stopPropagation();
      });

      //   $('.toolbox').parent().mousedown(function(e){ 
      //      if( e.button === 2 ) { 
      //         console.log('show toolbox'); 
      //         return false; 
      //      } 
      //      return true; 
      //   });

      $(document).on('scroll', function () {
         var top = $(this).scrollTop();
         var toolbox = $('.toolbox');
         toolbox.each(function () {
            var $container = $(this).parent();

            if ($container.is(':visible') // musí být viditelný
                    && $container.offset().top < (top + _this.toolboxTop) // box musí začínat výše než je minimální odsazení (většinou admin menu)
                    && top < ($container.offset().top + $container.height() - _this.toolboxTop - $('.toolbox-button', this).outerHeight())) // ještě není odscrolováno
            {
               $(this).css({
                  position: "fixed",
                  top: _this.toolboxTop,
                  left: $(this).offset().left,
                  right: 'auto'
               });
            } else {
               $(this).css({
                  position: "absolute",
                  top: 3,
                  right: 3, left: "auto"
               });
            }
         });
      });
   },
   initLangLoader: function () {
      $('body').on('click', 'a.toolbox-changelang-button', function (e) {
         e.preventDefault();
         CubeCMS.Loader.showLoadBox('.main-content');
         var cntUrl = this.href;
         $('.main-content').load(cntUrl, function (cnt) {
            var lang = cntUrl.match(/(l=[a-z]{2})/g);
            if (lang != null) {
               $('.main-content').html(cnt.replace(lang[0], ""));
            }
            CubeCMS.Loader.hideLoadBox();
            $(document).scroll();
         });
         return false;
      });
   },
   initFixedBoxs: function () {
      $('.fixed-actions-box').each(function () {
         var $element = $(this);
         if (!$element.data('container')) {
            return;
         }
         var $container = $($element.data('container'));
         var baseOffsetTop = $element.offset().top;
         var maxVPos = $element.offset().top + $container.outerHeight();
         $(window).on('scroll', function () {
            if ($(window).scrollTop() + 30 > baseOffsetTop && $(window).scrollTop() < maxVPos) {
               if (!$element.hasClass('fixed-actions-box-helper')) {
                  $element.after($('<div class="fixed-actions-box-palceholder hidden"></div>').height($element.height()).width($element.width()));
                  $element.outerWidth($element.outerWidth()).addClass('fixed-actions-box-helper').css({left: $element.offset().left});
               }
            } else {
               $element.removeClass('fixed-actions-box-helper').css({left: "auto", width: 'auto'});
               $element.next('.fixed-actions-box-palceholder').remove();
            }
         });
      });
   }
};

CubeCMS.ToolBox.Tool = {
   init: function () {

   }
};

CubeCMS.Images = {
   slideShowFade: function (selector, delay) {
      $(selector + ' .image:gt(0)').hide();
      setInterval(function () {
         $(selector + ' .image:first').fadeOut().next('.image').fadeIn().end().appendTo(selector);
      }, delay);
   },
   loadImage: function (src, callback) {
      var cacheImage = document.createElement('img');
      //set the onload method before the src is called otherwise will fail to be called in IE
      cacheImage.onload = function () {
         if ($.isFunction(callback)) {
            callback.call(cacheImage);
         }
      };
      cacheImage.src = src;
   }
};

CubeCMS.Loader = {
   timeout: 10000,
   loaderCnt: '<div id="loadingBox" class="loadingBox"><div class="loader-w"><span class="loader-text">{MSG}</span></div>',
   loadBoxClass: "loadingBox",

   init: function () {

   },

   loadUrl: function (box, url) {

   },

   showLoadBox: function (box, msg) {
      if (typeof (msg) == "undefined") {
         msg = "loading...";
      }
      var rId = 'loadingBox-' + (Math.floor(Math.random(0) * 10000));
      var parentBox = $(box).parent('div,p');
      var overBox = $(this.loaderCnt.replace('{MSG}', msg))
              .addClass(this.loadBoxClass)
              .attr('id', rId)
              .css({
                 position: 'absolute',
                 top: 0,
                 left: 0,
                 opacity: 0,
                 width: parentBox.width() + parseInt(parentBox.css('padding-left')) + parseInt(parentBox.css('padding-right'))
                         + parseInt(parentBox.css('margin-left')) + parseInt(parentBox.css('margin-right')),
                 height: parentBox.height() + parseInt(parentBox.css('padding-top')) + parseInt(parentBox.css('padding-bottom'))
                         + parseInt(parentBox.css('margin-top')) + parseInt(parentBox.css('margin-bottom'))
              });
      if (parentBox.css('position') != "absolute" && parentBox.css('position') != "relative") {
         parentBox.css('position', 'relative');
      }
      parentBox.prepend(overBox.hide());
      overBox.fadeTo(500, 1);
      var _loader = this;
      setTimeout(function () {
         _loader.hideLoadBox(rId);
      }, this.timeout); // zavření
      return rId;
   },
   hideLoadBox: function (id) {
      if (typeof (id) == "undefined") {
         $('.' + this.loadBoxClass).remove();
      } else if (id instanceof jQuery) {
         id.parent('div,p').find('.' + this.loadBoxClass).remove();
      } else {
         $('#' + id).remove();
      }
   }
};

CubeCMS.Form = {
   init: function () {
      this.initLangSelector();
      this.initUrlCheckers();
      this.initAdvancedFormElements();
      this.initAjaxForms();
   },
   addRow: function (button) {
      var $original = $(button).closest('.form-input-multiple');
      var $new = $original.clone(true);
      var $input = $new.find('input');
      $input.attr('id', $input.attr('id') + Math.floor((Math.random() * 10) + 1))
              .attr('name', $input.attr('name').replace(/\[.*\]/, '[]'))
              .val("");
      $original.removeClass('form-input-multiple-last').find('a.button-add-multiple-line').hide();

      $original.after($new.hide());
      $new.show().css('display', 'table');

      if ($original.closest('.form-controls').find('.form-input-multiple').length > 1) {
         $original.closest('.form-controls').find('a.button-remove-multiple-line').show();
      }
      return false;
   },
   removeRow: function (button) {
      var $row = $(button).closest('.form-input-multiple');
      var $container = $row.closest('.form-controls');
      $row.fadeOut(100, function () {
         if ($row.hasClass('form-input-multiple-last')) {
            // add prev class
            $row.prev().addClass('form-input-multiple-last').find('a.button-add-multiple-line').show();
            // show prev add button
            $row.prev().addClass('form-input-multiple-last');
         }
         $(this).remove();
         if ($container.find('.form-input-multiple').length == 1) {
            $container.find('a.button-remove-multiple-line').hide();
         }
      });
      return false;
   },
   addButton: function ($element, options) {
      options = $.extend({
         icon: 'refresh',
         href: window.location + '#',
         text: null,
         elementClass: null,
         id: null
      }, options);

      if (!$element.parent().hasClass('input-group')) {
         $element.wrap($('<div></div>').addClass('input-group'));
      }

      var $button = $('<a></a>')
              .addClass('input-group-btn')
              .addClass(options.elementClass)
              .prop({
                 href: options.href,
                 id: options.id
              });
      if (options.icon !== null) {
         $button.append($('<span></span>').addClass('icon').addClass('icon-' + options.icon));
      }
      if (options.text !== null) {
         $button.html(options.text);
      }
      $element.parent().append($button);
   },
   initLangSelector: function () {
      $('body').on('click', 'form .lang-container a.link-lang', function (event, focus) {
         if (typeof focus === 'undefined')
            focus = true;
         var lang = this.lang;
         $(this).parent().find('a').removeClass('link-lang-sel');
         $(this).addClass('link-lang-sel');
         // vybereme prvek, který obsahuje inputy
         var $container = $(this).closest('*:has(div[lang])');
         $('div[lang]', $container).hide();
         var p = $('div[lang="' + lang + '"]', $container).show();
         if (focus) {
            p.find('input,textarea,select').focus();
         }
         // zobrazení popisku k elementu
         $container = $(this).closest('*:has(label[lang])')[0];
         $('label[lang]', $container).hide();
         $('label[lang="' + lang + '"]', $container).show();
         return false;
      });
      $('body').on('click', 'form .lang-container a.link-lang-duplicator', function (e) {
         e.preventDefault();
         var curLang = $(this).parent().find('.link-lang-sel').prop('lang');
         var $container = $(this).closest('*:has(div[lang])');
         if ($container.find('div[lang="' + curLang + '"] textarea').length > 0) {
            if ($container.find('div[lang="' + curLang + '"] textarea + span.mceEditor')) {
               var val = tinyMCE.get($container.find('div[lang="' + curLang + '"] textarea').prop('id')).getContent();
               $container.find('textarea').each(function () {
                  tinyMCE.get($(this).prop('id')).setContent(val);
               });
            } else {
               $container.find('div[lang] textarea').val($container.find('div[lang="' + curLang + '"] textarea').val());
            }
         } else {
            $container.find('div[lang] input,select').val($container.find('div[lang="' + curLang + '"] input,select').val());
         }
      });

      if (typeof CubeCMS.primaryLang !== "undefined") {
         $('.lang-container a[lang="' + CubeCMS.primaryLang + '"]').trigger('click', [false]);
      } else {
         $('.lang-container a[lang="' + CubeCMS.lang + '"]').trigger('click', [false]);
      }
   },
   initUrlCheckers: function () {
      var _that = this;
      $('.input-urlkey-autoupdate').on('change', function () {
         _that.checkUrlKey($(this), $(this).data('checkurl'));
      });
      $('.button_update_urlkey').on('click', function (e) {
         e.preventDefault();
         var $elem = $($(this).data('element'));
         _that.checkUrlKey($elem, $elem.data('checkurl'));
         return false;
      });
//      $('.input-urlkey').on('change');

   },
   checkUrlKey: function ($elem, url, params) {
      var postParams = {
         key: $elem.val(),
         lang: $elem.attr("lang")
      };
      $.extend(postParams, $elem.data());
      if (typeof params !== "undefined") {
         $.extend(postParams, params);
      }
      $elem.trigger("beforeCheck", [postParams, url]);
      $.ajax({
         type: "POST",
         url: url,
         data: postParams,
         cache: false,
         success: function (data) {
            if (typeof data.urlkey !== "undefined") {
               $elem.val(data.urlkey);
            }
            $elem.trigger("checkComplete");
         }
      });
   },
   initAdvancedFormElements: function () {
      // první všechny adv schovat
      $('form').each(function () {
         var $advGrgoups = $('.form-group-advanced', this).hide();
         var _$form = $(this);
         if ($advGrgoups.length > 0) {
            $(this).addClass('form-advanced');

            // create expand button
//            var $buttonExpand = $('<a href="#"><span class="icon icon-eye"></span> pokročilé možnosti</a>');
            var $buttonExpand = $('<button></button>').prop('type', 'button')
                    .html('<span class="icon icon-eye"></span> pokročilé možnosti')
                    .data('expanded', false)
                    .on('click', function () {
                       if ($(this).data('expanded')) {
                          // otevřené
                          $('.icon', this).removeClass('icon-eye-slash').addClass('icon-eye');
                          $(this).data('expanded', false);
                          $('.form-group-advanced,.form-fieldset-advanced', _$form).slideUp(300);
                       } else {
                          // zavřené
                          $('.icon', this).removeClass('icon-eye').addClass('icon-eye-slash');
                          $(this).data('expanded', true);
                          $('.form-fieldset-advanced .form-group-advanced', _$form).show();
                          $('.form-group-advanced,.form-fieldset-advanced', _$form).slideDown(300);
                       }
                    });
            $(this).prepend($('<div class="form-advance-buttons"></div>').append($buttonExpand));
         }
         // pokud fildsed nemá žádný viditelný prvek typu div.form-group, tak jej označ a schovej
         $('form fieldset').each(function () {
            if ($(this).find('div.form-group:visible').length === 0 && $(this).find('table.form-table').length === 0 && !$(this).hasClass('not-hidden')) {
               $(this).addClass('form-fieldset-advanced').hide();
            }
         });


      });


   },
   setMessages: function ($form, cmsXhrRespond) {
      $form.find('.form-errors,.form-success').html(null);
      if (cmsXhrRespond.errmsg.length > 0) {
         var $box = $form.find('.form-errors');
         $box.html(null);
         $.each(cmsXhrRespond.errmsg, function (index, data) {
            if(typeof data === 'string'){
               $box.append('<div>' + data + '</div>');
            }
         });
         $box.slideDown(400);
      } 
      if($form.find('.form-errors div').length === 0){
         $form.find('.form-errors').hide();
      }
      if (cmsXhrRespond.infomsg.length > 0) {
         var $box = $form.find('.form-success');
         $box.html(null);
         $.each(cmsXhrRespond.infomsg, function (index, data) {
            $box.append('<div>' + data + '</div>');
         });
         $box.slideDown(400);
      } else {
         $form.find('.form-success').hide();
      }
      if (cmsXhrRespond.infomsg.length == 0 && cmsXhrRespond.errmsg.length == 0) {
         this.hideMessages($form);
      }
   },
   hideMessages: function ($form) {
      $form.find('.form-success,.form-errors').slideUp(400);
   },
   initAjaxForms: function () {
      $('form.cubecms-ajax-form').each(function () {
         // data-callback formu může místo defaultního calbacku mít vlastní
         $(this).on('submit', function (e) {
            e.preventDefault();
            var $form = $(this);
            CubeCMS.Form.hideMessages($form);
            var ajaxObj = {
               method : 'POST',
               url: $form.prop('action'),
               data : $form.serialize()+'&_cubecms_respond_class=', // bez hodnoty je výchozí třída
               success: function (response) {
                  if(typeof response === 'object'){
                     var callbackFunction = $form.data('callback');
                     if (typeof callbackFunction !== "undefined" && CubeCMS.Tools.functionExist(callbackFunction)) {
                        CubeCMS.Tools.callFunction($form.data('callback'), window, $form, response);
                     } else {
                        CubeCMS.Form.ajaxFormSuccess($form, response);
                     }
                  } else {
                     console.log('ERR: neimplmentovaná chyba aplikace');
                  }
               }
            };
            $.ajax(ajaxObj);
            return false;
         });
      });
   },
   ajaxFormSuccess: function ($form, response) {
      // zobraz zprávy
      // pokud má redirect proměnou, tak přesun na tut adresu
      if(typeof response.redirect !== "undefined"){
         window.location.replace(response.redirect);
      }
      CubeCMS.Form.setMessages($form, response);
   }
};

CubeCMS.Modal = {
   init: function () {
      $('body').on('click', '.cubecms-modal .close-modal', function () {
         CubeCMS.Modal.close($(this).closest('.cubecms-modal'));
         return false;
      }).on('click', '.modal-open-button', function () {
         CubeCMS.Modal.open($($(this).data('modal')));
         return false;
      });
      // assign modal size id defined
      $('.cubecms-modal').each(function () {

      });
   },
   open: function ($target) {
      $target = $($target);
      if ($target.length > 0) {
         $("body").addClass('cubecms-modal-open').css({paddingRight: CubeCMS.Tools.getScrollBarWidth()});
         $target.fadeIn(200);
      }
   },
   close: function ($target) {
      $target = $($target);
      if ($target.length > 0) {
         $target.fadeOut(200, function () {
            $("body").removeClass('cubecms-modal-open').css({paddingRight: 0});
         });
      }
   }
};

CubeCMS.Popup = {
   init: function () {
      var that = this;
      $('.cubecms-link-popup').each(function () {
         $(this).on('click', function (e) {
            if ($(this).prop('href') === undefined || $(this).prop('href') === "") {
               return;
            }
            e.preventDefault();
            var url = $(this).prop('href');
            url = (url.indexOf("?") === -1 ? url + '?popupwindow=1' : url + '&popupwindow=1');
            url = url + ($(this).data('popup-callback') === undefined ? '' : '&callback=' + $(this).data('popup-callback'));
            that.openPopup(url, $(this).prop('title'), $(this).data('popup-width'), $(this).data('popup-height'));
            return false;
         });
      });
      $('body.popup .button-cancel').click(function () {
         window.close();
      });
   },
   openPopup: function (url, title, w, h) {
      // Fixes dual-screen position                         Most browsers      Firefox
      var dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : screen.left;
      var dualScreenTop = window.screenTop !== undefined ? window.screenTop : screen.top;

      width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
      height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

      var left = ((width / 2) - (w / 2)) + dualScreenLeft;
      var top = ((height / 2) - (h / 2)) + dualScreenTop;
      var newWindow = window.open(url, title, 'scrollbars=yes, width='
              + w + ', height=' + h + ', top=' + top + ', left=' + left);

      // Puts focus on the newWindow
      if (window.focus) {
         newWindow.focus();
      }
   }
}

/* DEPRECATED */
function vveShowMessages(dataObj) {
   CubeCMS.Msg.show(dataObj);
}
function infoMsg(msg, clear) {
   CubeCMS.Msg.info(msg, clear);
}
function clearInfoMsg() {
   CubeCMS.Msg.clear('info');
}
function clearErrMsg() {
   CubeCMS.Msg.clear('error');
}
function errMsg(msg, clear) {
   CubeCMS.Msg.err(msg, clear);
}
function showLoadBox(box, timeout) {
   CubeCMS.Loader.showLoadBox(box);
}
function hideLoadBox() {
   CubeCMS.Loader.hideLoadBox();
}
function vveLoadImage(src, callback) {
   CubeCMS.Images.loadImage(src, callback);
}
function str2url(str, encoding, ucfirst) {
   return CubeCMS.Tools.str2url(str, ucfirst);
}

function vveCheckUrlKey(checkerUrl, element, callback, params) {
   var $e = $(element);
   var postParams = {key: $e.val(), lang: $e.attr("lang")};
   if (typeof params != "undefined") {
      $.extend(postParams, params);
   }
   $.ajax({
      type: "POST",
      url: checkerUrl,
      data: postParams,
      cache: false,
      success: function (data) {
         if ((callback || callback != '') && (typeof callback !== 'undefined')) {
            if (typeof data.urlkey !== "undefined") {
               callback(data.urlkey);
            } else {
               callback(null);
            }
         }
      }
   });
}

$(document).ready(function () {
   CubeCMS.setup(); // init base class when document ready
   // open external link in new window
   $("body").on('click', '.link-external', function () {
      window.open(this.href);
      return false;
   });
});
