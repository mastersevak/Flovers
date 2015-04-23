(function($){

	var methods = {
		init : function(options){
			this.each(function(){

				var params = {
	                backdrop: true, //задный фон modal - a
	                modalOutClick: false, //скрыть modal кликнув вне его контента
	            };

	            //парамтры пользователя
	            $.extend(params, $(this).data());

	            $.extend(params, options);

				var modal = $(this);

				//добавлаем параметры в dom
	            this.opt = params;

				modal.hide();

				//скрыть modal
				modal.find('button[data-dismiss="modal"]').on('click', function(){
					methods.hide(modal, params);
				});

			});
		},

		//показать modal
		show : function(position){
			var modal = $(this);

			if(modal.is(':hidden')){
				var index = modal.index();
				var params = $(this).get(0).opt;

				modal.show();

				if(params.backdrop) {
					var backdrop = $('<div/>').addClass('modal-backdrop').css({
					'background': 'rgba(0,0,0,0.5)', 
					'top' : '0',
					'left' : '0',
					'right' : '0',
					'bottom' : '0',
					'z-index' : '1030',
					'position': 'fixed'});

					$("body").css("overflow", "hidden");
					backdrop.prependTo('body');
				}	

				var windowHeight = $(window).height();
				var height = modal.find('.modal-content').actual('outerHeight');
				var windowWidth = $(window).width();
				var width = modal.find('.modal-content').actual('outerWidth')

				var topPos = 0;
				var leftPos = 0;
				var rightPos = 0;
				var bottomPos = 0;

				//позиция окна при открытии
				if($.isArray(position)){
					if(windowHeight > height) topPos = position[0];
					leftPos = position[1];
				}else{
					switch(position) {
						case 'center':
							if(windowHeight > height) topPos = (windowHeight - height)/2;
							leftPos = (windowWidth - width)/2;
							break;
						case 'center-top':
							leftPos = (windowWidth - width)/2;
							break;
						case 'center-bottom':
							if(windowHeight > height) topPos = windowHeight-height-20;
							leftPos = (windowWidth - width)/2;
							break;
						case 'left':
							if(windowHeight > height) topPos = (windowHeight - height)/2;
							break;
						case 'left-top':
							topPos = 0;
							break;
						case 'left-bottom':
							if(windowHeight > height) topPos = windowHeight-height-20;
							break;
						case 'right':
							if(windowHeight > height) topPos = (windowHeight - height)/2;
							leftPos = windowWidth-width;
							break;
						case 'right-top':
							topPos = 0;
							leftPos = windowWidth-width;
							break;
						case 'right-bottom':
							if(windowHeight > height) topPos = windowHeight-height-20;
							leftPos = windowWidth-width;
							break;
						default: //center
							if(windowHeight > height) topPos = (windowHeight - height)/2;
							leftPos = (windowWidth - width)/2;
							break;
					}
				}


				var _window = modal;
				if(params.backdrop) { 
					_window = modal.find('.modal-content');
				}
				else{
					_window.css({ 
						'overflow': 'visible',
						'overflow-y':'visible'
						});
				}

				_window.css({
					'top' : topPos+5,
					'left' : leftPos+5,
					'right' : rightPos+5,
					'bottom' : bottomPos+5
				});

				//клик вне нового select - a скрывает все списки 
				if(params.modalOutClick){	
		            $(document).on('click.hideModal' + index, function(event){
		                event.stopPropagation();
		                
		                var target = $(event.target);
		                if(target.closest('.modal-content').length == 0){
		                    if(event.target.tagName != 'BUTTON'){ //es uxaki testi hamar a, heto kpoxvi if-i meji payman@ kaxvac te inj attribut kunena modal@ bacox knopken
		                    	methods.hide(modal, params);
		                    	$(document).unbind('click.hideModal' + index);
		                    }
		                }  
		            });
				}

				//скрыть при нажатии на escape
	            $(document).on('keyup.uimodal', function(event){
	                if(event.which == 27){
	                    methods.hide(modal, params);
	                }
	            });

	            modal.trigger('shown.bs.modal');
			}
		},

		//скрыть modal
		hide : function(modal, params){
			if(!modal) var modal = $(this);
			if(!params) var params = this.get(0).opt;

			modal.hide();
			if(params.backdrop) {
				$('.modal-backdrop').remove();
			}
			
			$("body").removeAttr('style');

			$(document).unbind('keyup.uimodal');

			modal.trigger('hidden.bs.modal');
		},

	};

	$.fn.doModal = function(method){
      
	      if (methods[method]) {
	         return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
	      } 
	      else  if (typeof method === 'object' || !method) {
	               return methods.init.apply(this, arguments);
	            } 
	      else {
	         $.error('Метод ' + method + ' не существует');           
	         return false;
	      }
   	};

   	$(function(){

   		$(document).on('click.domodal.api', '[data-toggle=domodal]', function(event){
   			event.preventDefault();
   			event.stopPropagation();

   			var position = 'center';

   			if($(this).data('position')) position = $(this).data('position');

   			$($(this).data('target')).doModal('show', position);
   		});
   	});
})(jQuery);
