/**
 * jQuery Editable Select
 * by Indri Muska <indrimuska@gmail.com>
 *
 * Source on GitHub @ https://github.com/indrimuska/jquery-editable-select
 *
 * File: jquery.editable-select.js
 */

(function ($) {
	$.extend($.expr[':'], {
		nic: function (elem, i, match, array) {
			return !((elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0);
		}
	});
	$.fn.editableSelect = function (options) {
		var defaults = { filter: true, effect: 'default', duration: 'fast', onCreate: null, onShow: null, onHide: null, onSelect: null, onChange : null };
		var select = this, input = $('<input type="text">'), list = $('<ul class="es-list">');
		options = $.extend({}, defaults, options);
		switch (options.effects) {
			case 'default': case 'fade': case 'slide': break;
			default: options.effects = 'default';
		}
		if (isNaN(options.duration) && options.duration == 'fast' && options.duration == 'slow') options.duration = 'fast';
      
      this.after(input);
      
      
//		this.replaceWith(input);
		var EditableSelect = {
			init: function () {
				var es = this;
				es.copyAttributes(select, input);
            input.prop('name', input.prop('name')+'_es').prop('id', input.prop('id')+'_es');
            // hide select
            select.css({ height: 0, width : 0, padding : 0, margin : 0, lineHeight : 0, opacity : 0, position : 'absolute', top:0, right:0});
            
				input.addClass('es-input');
				$(document.body).append(list);
				select.find('option').each(function () {
					var li = $('<li>');
					li.html($(this).text());
					es.copyAttributes(this, li);
					list.append(li);
					if ($(this).attr('selected')) input.val($(this).text());
				});
				input.on('focus input click', es.show);
				$(document).click(function (event) {
					if (!$(event.target).is(input) && !$(event.target).is(list)) es.hide();
				});
				es.initializeList();
				es.initializeEvents();
				if (options.onCreate) options.onCreate.call(this, input);
			},
			initializeList: function () {
				var es = this;
				list.find('li').each(function () {
					$(this).on('mousemove', function () {
						list.find('.selected').removeClass('selected');
						$(this).addClass('selected');
					});
					$(this).click(function () { 
                  es.setField.call(this, es); 
               });
				});
				list.mouseenter(function () {
					list.find('li.selected').removeClass('selected');
				});
			},
			initializeEvents: function () {
				var es = this;
            select.on('change', function(){
               options.onChange.call(this, select, input);
            });
				input.on('input keydown', function (event) {
					switch (event.keyCode) {
						case 40: // Down
							es.show();
							var visibles = list.find('li:visible'), selected = visibles.filter('li.selected');
							list.find('.selected').removeClass('selected');
							selected = visibles.eq(selected.size() > 0 ? visibles.index(selected) + 1 : 0);
							selected = (selected.size() > 0 ? selected : list.find('li:visible:first')).addClass('selected');
							es.scroll(selected, true);
							break;
						case 38: // Up
							es.show();
							var visibles = list.find('li:visible'), selected = visibles.filter('li.selected');
							list.find('li.selected').removeClass('selected');
							selected = visibles.eq(selected.size() > 0 ? visibles.index(selected) - 1 : -1);
							(selected.size() > 0 ? selected : list.find('li:visible:last')).addClass('selected');
							es.scroll(selected, false);
							break;
						case 13: // Enter
							if (list.is(':visible')) {
								es.setField.call(list.find('li.selected'), es);
								event.preventDefault();
							}
						case 9:  // Tab
						case 27: // Esc
							es.hide();
							break;
						default:
							es.show();
							break;
					}
				}).on('change', function(){
               es.setSelectValue($(this).val());
            });
			},
			show: function () {
				list.find('li').show();
				list.css({ top: input.offset().top + input.outerHeight() - 1, left: input.offset().left, width: input.innerWidth() });
				var hidden = options.filter ? list.find('li:nic(' + input.val() + ')').hide().size() : 0;
				if (hidden == list.find('li').size()) list.hide();
				else
					switch (options.effects) {
						case 'fade':   list.fadeIn(options.duration); break;
						case 'slide':  list.slideDown(options.duration); break;
						default:       list.show(options.duration); break;
					}
				if (options.onShow) options.onShow.call(this, input);
			},
			hide: function () {
				switch (options.effects) {
					case 'fade':   list.fadeOut(options.duration); break;
					case 'slide':  list.slideUp(options.duration); break;
					default:       list.hide(options.duration); break;
				}
				if (options.onHide) options.onHide.call(this, input, select);
			},
			scroll: function (selected, up) {
				var height = 0, index = list.find('li:visible').index(selected);
				list.find('li:visible').each(function (i, element) { if (i < index) height += $(element).outerHeight(); });
				if (height + selected.outerHeight() >= list.scrollTop() + list.outerHeight() || height <= list.scrollTop()) {
					if (up) list.scrollTop(height + selected.outerHeight() - list.outerHeight());
					else list.scrollTop(height);
				}
			},
			copyAttributes: function (from, to) {
				var attrs = $(from)[0].attributes;
				for (var i in attrs) $(to).attr(attrs[i].nodeName, attrs[i].nodeValue);
			},
         setSelectValue : function(){
//            if(input.val() === ""){
//               return;
//            }
            var opts = $('option', select).prop('selected', false);
            var selectedOpt = opts.filter(function() {
               return $(this).text() === input.val(); 
            });
            if(selectedOpt.length > 0){
               selectedOpt.prop('selected', true);
            } else {
               this.addSelectValue(input.val());
            }
            select.change();
         },
         addSelectValue : function(value){
            var newOpt = $('<option class="new"></option>').text(value);
            select.append(newOpt);
            newOpt.prop('selected', true);
         },
			setField: function (es) {
				if (!$(this).is('li:visible')) return false;
				input.val($(this).text()).change();
				es.hide();
				if (options.onSelect) options.onSelect.call(input, $(this));
			}
         
		};
		EditableSelect.init();
		return input;
	}
}) (jQuery);