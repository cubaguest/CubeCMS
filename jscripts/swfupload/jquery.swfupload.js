/*
 * SWFUpload jQuery Plugin v1.1.0
 * @depend SWFUpload v 2.5
 *
 * Copyright (c) 2009 Adam Royle
 * Edited by Jakub Matas 2010
 * Licensed under the MIT license.
 *
 * Changes:
 * - Add upload_resize_start_handler handler.
 * - Add support for startResizeUpload with autodetection of image type (png, jpg)
 *   Params for resize in custom_settings {r_width: 100, r_height: 100, r_quality: 100}
 */

(function($){
	
	var defaultHandlers = ['swfupload_loaded_handler','file_queued_handler','file_queue_error_handler','file_dialog_start_handler','file_dialog_complete_handler',
      'upload_start_handler','upload_progress_handler','upload_error_handler','upload_success_handler','upload_complete_handler','queue_complete_handler',
      'upload_resize_start_handler'];
	var additionalHandlers = [];
	
	$.fn.swfupload = function(){
		var args = $.makeArray(arguments);
		return this.each(function(){
			var swfu;
			if (args.length == 1 && typeof(args[0]) == 'object') {
				swfu = $(this).data('__swfu');
				if (!swfu) {
					var settings = args[0];
					var $magicUploadControl = $(this);
					var handlers = [];
					$.merge(handlers, defaultHandlers);
					$.merge(handlers, additionalHandlers);
					$.each(handlers, function(i, v){
						var eventName = v.replace(/_handler$/, '').replace(/_([a-z])/g, function(){return arguments[1].toUpperCase();});
						settings[v] = function() {
							var event = $.Event(eventName);
							$magicUploadControl.trigger(event, $.makeArray(arguments));
							return !event.isDefaultPrevented();
						};
					});
					$(this).data('__swfu', new SWFUpload(settings));
				}
         } else if (args.length > 0 && typeof(args[0]) == 'string') {
				var methodName = args.shift();
            swfu = $(this).data('__swfu');
            switch (methodName) {
               case "startResizedUpload": // metoda rsize má další parametry
                  var file = swfu.getFile();
                  var type = SWFUpload.RESIZE_ENCODING.JPEG;
                  if(file.type.toLowerCase() == '.png'){
                     type = SWFUpload.RESIZE_ENCODING.PNG;
                  }
                  swfu.startResizedUpload(file.id, swfu.settings.custom_settings.r_width,
                  swfu.settings.custom_settings.r_height, type, swfu.settings.custom_settings.r_quality);
                  break;
               default: // výchozí metody
      				if (swfu && swfu[methodName]) {
         				swfu[methodName].apply(swfu, args);
            		}
                  break;
            }
			}
		});
	};
	
	$.swfupload = {
		additionalHandlers: function() {
			if (arguments.length === 0) {
				return additionalHandlers.slice();
			} else {
				$(arguments).each(function(i, v){
					$.merge(additionalHandlers, $.makeArray(v));
				});
			}
		},
		defaultHandlers: function() {
			return defaultHandlers.slice();
		},
		getInstance: function(el) {
			return $(el).data('__swfu');
		}
	};
	
})(jQuery);