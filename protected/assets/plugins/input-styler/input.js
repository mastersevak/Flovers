(function($) {
	$.fn.inputStyler = function(options){
		var inputs = 0;
		this.each(function(){
			var index = inputs++;

			//парамтры пользователя
			var params = {
				placeholder: false,
				focusplaceholder: false,
				prefix: '',
				suffix: '',
				inputtype: '',
			};

            $.extend(params, $(this).data());

            $.extend(params, options);
			
			var input = $(this);
			if(params.width) input.width(params.width);

			// var clear = 0;
			var prefix = params.prefix;
			var suffix = params.suffix;
			input.wrap('<div class="inputStyler-wrap"></div>');
			var wrapper = input.closest('.inputStyler-wrap');
			wrapper.addClass(input.attr('class'));
			input.attr('class', '');
			wrapper.attr('style', input.attr('style'));
			input.wrap('<div/>');
			var viewWrap = input.parent().addClass('inputStyler-viewWrap');
			var view = $('<span/>').addClass('inputStyler-view').prependTo(viewWrap);
			var resetInput = $('<div></div>').addClass('resetInput').prependTo(viewWrap);
			resetInput.hide();
			input.hide();
			
			if(params.focusplaceholder) input.attr('placeholder', params.focusplaceholder);
			
			if(!params.placeholder) {
				params.placeholder = input.prop('placeholder') ? input.prop('placeholder') : '';
			}
			
			if(input.val().length != 0) {
				view.removeClass('data-empty-attr');
				view.text(prefix+' '+input.val()+' '+suffix);
				resetInput.show();

			}else{
				view.addClass('data-empty-attr');
				view.text(params.placeholder);
			}
			//список
			if(input.data('items')){
				var list = $('<ul></ul>').appendTo(wrapper);
				list.wrap('<div/>');
				var listWrap = list.parent().addClass('inputStyler-listWrap');

				var arr = input.data('items').split(',');
				for(var p in arr){
					var li = $('<li data-val='+$.trim(arr[p])+'/>').text($.trim(arr[p])+' '+suffix).appendTo(list);
				}

				listWrap.hide();
				var listWidth = list.actual('outerWidth')+20;
	            if(input.actual('outerWidth') >= listWidth){
	                listWidth = view.actual('outerWidth');
	            }
	    
	            listWrap.width(listWidth);
	            list.width('100%');

	            //выбор из списка
				list.find('li').on('click', function(){
					resetInput.show();
					input.val($(this).data('val')).trigger('change');
				});
			}


			//если нужно вводить только цифры или текст
			input.on('keypress', function(event){
				if(event.keyCode == 13){
					input.trigger('change');
				}

				if(event.keyCode != 13 && params.inputtype=='text' && !isNaN(parseInt(String.fromCharCode(event.which)))){
					event.preventDefault();
				}

				if(event.keyCode != 13 && params.inputtype=='numeric' && isNaN(parseInt(String.fromCharCode(event.which)))){
					event.preventDefault();
				}
			});
			
			//при клике в input - e   только введенный текст, откыть список
			view.on('click', function(event){
				var target = $(event.target);
				view.hide();
				resetInput.hide();
				viewWrap.addClass('active');
				input.show();
				if(input.data('items')) {
					listWrap.show();

					var height;
	                var windowHeight = $(window).height();
	                var windowWidth = $(window).width();
	                var scrollTop = $(window).scrollTop();
	                var selectTop = input.offset().top;
	                var selectLeft = input.offset().left;
	                var footer = windowHeight + scrollTop - selectTop - input.actual('outerHeight');
	                var header = selectTop - scrollTop;
	                var bottom = windowHeight + scrollTop;

	                var liHeight = list.find('li').not(':hidden').actual('outerHeight');
	                var liCount = list.find('li').not(':hidden').length;
	                
	                height = liCount * liHeight;

	                if(footer >= header){
	                    if(footer <= (height + 20)){
	                        list.height(footer - input.actual('outerHeight') - 30);
	                    }else{

	                        list.height(height); 
	                    }
	                    listWrap.offset({top:(selectTop + input.actual('outerHeight')+3), left:selectLeft});
	                }

	                if(footer < header){
	                    if((height + 20) >= footer){
	                        if(height >= header){
	                            list.height(header-14);
	                            listWrap.offset({top:(scrollTop + 10), left:selectLeft});
	                        }else{
	                            list.height(height);
	                            listWrap.offset({top:(selectTop - height-5), left:selectLeft});
	                        }
	                    }else{
	                        list.height(height);
	                        listWrap.offset({top:(selectTop + input.actual('outerHeight')+3), left:selectLeft});
	                    }
	                }

	                if((selectLeft+listWrap.actual('outerWidth')+15)>(windowWidth+$(window).scrollLeft())){
	                    listWrap.offset({top:list.offset().top, left:((windowWidth+$(window).scrollLeft())-list.actual('outerWidth')-15)});
	                }

	                listWrap.height(list.actual('outerHeight')+1);

				}

				input.trigger('focus');

				//клик вне input - a скрывает список 
                $(document).on('click.hideList' + index, function(event){
                    event.stopPropagation();
                    
                    var target = $(event.target);
                    if(target.closest(wrapper).length == 0){
                        input.trigger('change');
                    }  
                });
			});

			// //когда указатель покидает область элемента 
			// wrapper.on('mouseleave', function(){
			// 	if(list.is(':visible')){
			// 		clear = setTimeout(function(){
			// 			input.trigger('change');
			// 		}, 500);
			// 	}

			// });
		

			// wrapper.on('mouseenter', function(){
			// 	clearTimeout(clear);
			// });


			


			//при изменении input - a добавляются суфикс и префикс
			input.on('change', function(event){
				var target = $(event.target);
				if(input.data('items')) listWrap.hide(); 

				viewWrap.removeClass('active');
				input.hide();
				view.show();

				if(target.val().length != 0) {
					view.removeClass('data-empty-attr');
					view.text(prefix+' '+target.val()+' '+suffix);
					resetInput.show();
				}else{
					view.addClass('data-empty-attr');
					view.text(params.placeholder);
					resetInput.hide();
				}

				$(document).unbind('click.hideList' + index);	
			});
			
			//сброс input - a
			resetInput.on('click', function(){
				input.val('').trigger('change');
				$(this).hide();
			});
			
		});
	};
})(jQuery);