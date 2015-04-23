(function($) {

	var payment_id, template;

	var popup = {};
	var list = {};
	var textarea = {};

	var methods = {
		init : function(options) {
			var id = options.id;
			popup[id] = $('#' + id);

			list[id] = popup[id].find('.grid-body .scroller');
			textarea[id] = popup[id].find('textarea');

			template = '<div class="comment">' + 
							'<div class="user-info-small clearfix">' + 
								'<div  class="user-profile-pic-normal pull-left mr15">' +
									'{{{imgsrc}}}' +
								'</div>' +
								'<p class="username dark-text pull-left">{{name}}<span>{{role}}</span></p>' +
								'<p class="date">{{date}}<span>{{ago}}</span></p>' +
							'</div>' +
							'<p class="body">{{{comment}}}</p>' +
						'</div>';

			//EVENTS
			textarea[id].on('keydown', function(e){
				if(e.keyCode == 13 && e.ctrlKey){ //нажали на enter
					e.preventDefault();
					$(this).closest('form').submit();
				}
			});
		},

		add : function(id, data, callback){

			jPost(popup[id].data('add-url'),
				data,
				function(result){
					if(result.success){
						var comment = $(Mustache.to_html(template, result.data));

						if(list[id].find('> span.empty').length > 0) list[id].html(comment.hide(id));
						else list[id].append(comment.hide(id));
						
						$(comment).fadeIn('fast');

						//scroll down list
						list[id].scrollTop(list[id][0].scrollHeight);

						// add class has-comment to the same payment
						var icon = $(".grid-view input[type=checkbox][value=" + data.idOwner + "]").closest('tr').find('td a.fa-comments');
						if(icon.length == 0) icon = $('td a.fa-comments[data-id-owner="' + data.idOwner + '"]');
						if(icon.length == 0) icon = $('button.comment-button[data-id-owner=' + data.idOwner + ']');
						
						if(!icon.hasClass('has-comment')) icon.addClass('has-comment');
						if(icon.hasClass('comment-hovered')) icon.removeClass('comment-hovered');        

						methods.reset(id);

						callback(result);
					}
					else{

					}
				},
				'json');
		},

		get : function(id){
			jPost(popup[id].data('get-url'), {
				ownerModelName: popup[id].data('owner-modelname'),
				idOwner: popup[id].data('id-owner'),
				commentsRelation: popup[id].data('comments-relation'),
				additionalFields: popup[id].attr('data-additional-fields')
			}, function(data){
				if(data.length > 0){
					$.each(data, function(index, comment){
						comment = Mustache.to_html(template, comment);
						list[id].append($(comment));
					});
				}else{
					list[id].html('<span class="empty">Нет комментариев</span>');
				}

				list[id].scrollTop(list[id][0].scrollHeight);
			}, "json");
		},

		open : function (id, elem){
			//set payment_id
			var idOwner = $(elem).closest('tr').find('td.checkbox-column :checkbox').val();
			if(!idOwner)
				idOwner = $(elem).data('id-owner');

			// backdrop
			if($(elem).data('backdrop')){
				var backdrop = $('<div/>').addClass('modal-backdrop').css({
				'background': 'rgba(0,0,0,0.5)', 
				'top' : '0',
				'left' : '0',
				'right' : '0',
				'bottom' : '0',
				'z-index' : '1000',
				'position': 'fixed'});

				$("body").css("overflow", "hidden");
				backdrop.prependTo('body');
			}

			popup[id].data('id-owner', idOwner);
			popup[id].attr('data-additional-fields', $(elem).attr('data-additional-fields'));
			popup[id].find('.grid-title h4 span').text('[' + idOwner + ']');

			methods.get(id);
			list[id].html('');
			popup[id].slideDown('fast');
			methods.reset(id);
			textarea[id].focus();

			//скрыть при нажатии на escape
            $(document).on('keyup.hideComment', function(event){
                event.stopPropagation();

                if(event.which == 27){
                    methods.hide(id);
                    $(document).unbind('keyup.hideComment');
                }
            });
		},

		hide : function(id){
			methods.reset(id);
			popup[id].hide();

			if($('.modal-backdrop')) {
				$('.modal-backdrop').remove();
			}
		},

		//Получает комментарии для показа при наведении
		getOnHover : function(element, placement){
			element.addClass('comment-hovered');
			jPost(element.data('hover-url'), {
				ownerModelName: element.data('owner-modelname'),
				idOwner: element.data('id-owner'),
				commentsRelation: element.data('comments-relation'),
				title: element.data('hover-title')
			}, function(data){
				if(data.success && data.resultComments){
					var commentsClass = $(data.resultComments).find('.comment-no-result').length == 0 ? 'alert alert-success' : 'alert alert-error';
					placement.removeClass('grid-view-loading').addClass(commentsClass).append(data.resultComments);
				}
				else
					showErrorMessage(data.error);
			});
		},

		// emptylist : function(){
		//     list.html(''); //empty list
		//     //show preloader
		// },

		reset : function(id){
			popup[id].find('form').get(0).reset();
		} 
	};

	$.fn.comments = function(method)
	{
		var commentsTimeout;

		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Метод ' + method + ' не существует');
			return false;
		}
	};

	$(function(){
		$(document).on('mouseenter', '.fa-comments.has-comment', function(event){
			var self = $(this);
			if(self.data('show-on-hover')){
				if(!self.siblings('div').hasClass('comments-hover-block')){
					var commentBlock = $('<div class="comments-hover-block posabs hidden" style="width:565px;"></div>');
					self.after(commentBlock);
				}else
					var commentBlock = self.siblings('.comments-hover-block');

				if(!self.hasClass('comment-hovered')){
					$.fn.comments.commentsTimeout = window.setTimeout(function(){
						commentBlock.empty();
						commentBlock.addClass('grid-view-loading');
						commentBlock.removeClass('hidden');
						methods.getOnHover(self, commentBlock);
					}, 500);
				}else
					commentBlock.removeClass('hidden');
			}
		})
		//Когда отводим курсор от названия товара скрываем блок с комментариями
		.on('mouseout', '.fa-comments.has-comment', function(){
			if($.fn.comments.commentsTimeout !== undefined) clearTimeout($.fn.comments.commentsTimeout);
			$(this).siblings('.comments-hover-block').addClass('hidden');
		})
	});
})( jQuery );
