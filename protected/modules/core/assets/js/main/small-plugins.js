(function($) {
	
	/**
	 * Открывает модальное окно, заполняя поля формы, 
	 * данными для редактирования
	 */
	$.fn.openModal = function(data, additional) {
		var target = additional.target; // id modal with '#'
		var model  = additional.model; 	// model name for form
		var title  = additional.title; 	// modal title
		var action = additional.action;	// action for form

		var form = $(target).find('form');

		if(target == undefined || model == undefined)
			return false;

		if(title.indexOf(' ') != -1){
			var pos = title.indexOf(' ');
			var title1 = title.substring(0, pos); 	// first part of title (till first space)
			var title2 = title.substr(pos);			// another part
			$(target).find('.modal-title').html(title1 + "<span class='semi-bold'>" + title2 + "</span>")
		}
		else
			$(target).find('.modal-title').html(title);
		
		if(action == undefined) action = form.attr('action');
		else form.attr('action', action);

		$.fn.yiiactiveform.getSettings('#'+form.attr('id')).validationUrl = action;

		//reset fields values
		var modalBody = form.find('.modal-body');
		modalBody.find('input:text, input[type="hidden"], select, textarea').val('');
		modalBody.find('select').selectStyler('update');
		modalBody.find('input:checkbox').prop('checked', false);
		modalBody.find('img').attr('src', '');
		//reset elrte if exists in modal
		var textareas = form.find('.el-rte textarea');
		if(textareas.length > 0){
			textareas.each(function(index, textarea){
				$(textarea).elrte('val', ' ');
			});
		}

		if(additional.beforeOpen != undefined){
			additional.beforeOpen(data);
		}
		
		if(data != undefined){

			//set data to fields
			$.each(data, function(key, value){
				var field = $(target).find("[name='" + model + "[" + key + "]']"); // ex. Shift[work_begin]

				if($.type(value) !== 'object'){ //для случаев с текстами
					if(field.length > 0){
						$.each(field, function(ind, _field){
							_field = $(_field);
							if(_field.is('input:text') || _field.is('textarea')){
								_field.val(value);
							}
							else if(_field.is('input[type="hidden"]')){
								//если есть одноименный checkbox то для hidden ставим значение 0
								if(field.length > 1) _field.val(0);
								else _field.val(value);
							}
							else if(_field.is('select')){
								_field.val(value).selectStyler('update');
							}
							else if(_field.is(':checkbox')){
								_field.prop('checked', parseInt(value));
							}
							else if(_field.is('img')){
								_field.attr('src', value);
							}	
						});
						
					}
				}
			});
		}

		//update elrte if exists in modal
		var textareas = form.find('.el-rte textarea');
		if(textareas.length > 0){
			textareas.each(function(index, textarea){
				$(textarea).elrte('val', $(textarea).val());
			});
		}

		$(target).doModal('show');
	}

	/**
	 * Пока не используем
	 * альтернатива для jqueryui draggable
	 */
	$.fn.drags = function(opt) {

		opt = $.extend({handle:"",cursor:"move"}, opt);

		if(opt.handle === "") {
			var $el = this;
		} else {
			var $el = this.find(opt.handle);
		}

		return $el.css('cursor', opt.cursor).on("mousedown", function(e) {
			if(opt.handle === "") {
				var $drag = $(this).addClass('draggable');
			} else {
				var $drag = $(this).addClass('active-handle').parent().addClass('draggable');
			}
			var z_idx = $drag.css('z-index'),
				drg_h = $drag.outerHeight(),
				drg_w = $drag.outerWidth(),
				pos_y = $drag.offset().top + drg_h - e.pageY,
				pos_x = $drag.offset().left + drg_w - e.pageX;
			$drag.css('z-index', 1000).parents().on("mousemove", function(e) {
				$('.draggable').offset({
					top:e.pageY + pos_y - drg_h,
					left:e.pageX + pos_x - drg_w
				}).on("mouseup", function() {
					$(this).removeClass('draggable').css('z-index', z_idx);
				});
			});
			e.preventDefault(); // disable selection
		}).on("mouseup", function() {
			if(opt.handle === "") {
				$(this).removeClass('draggable');
			} else {
				$(this).removeClass('active-handle').parent().removeClass('draggable');
			}
		});

	}

	/**
	 * показывает или скрывает элемент, 
	 * либо делает активным или неактивным его, или элементы внутри него, если таковые есть
	 */
	$.fn.toggleElement = function(action) {
		
		return this.each(function() {

			switch(action){

				case 'visibility':
					$(this).toggle();
					break;

				case 'activation':
					var $this = $(this);
					
					var toggleActivation = function (element){	
						if (element.attr('disabled')) element.removeAttr('disabled');
						else element.attr('disabled', 'disabled');
					};

					if(['SELECT', 'INPUT', 'TEXTAREA', 'BUTTON'].indexOf(this.tagName) == -1){
						$(this).find('select, input, textarea, button').each(function(index, element){
							toggleActivation($(element));
						});
					}
					else 
						toggleActivation($(this));

					break;
			}
		});
	};

	/**
	 * делает математические действия и считает сумму, 
	 * работает на инпутах по классом "mataction" 
	 */
	$.fn.math = function(){
		//
		var keyCodes = [
			40, // (
			41, // )
			42, // *
			43, // +
			45, // -
			46, // .
			47, // /
			48, // 0
			49, // 1
			50, // 2
			51, // 3
			52, // 4
			53, // 5
			54, // 6
			55, // 7
			56, // 8
			57, // 9
		];

		this.each(function(){

			var element = $(this);
			var target = element.data('target') ? $('#' + element.data('target')) : element;
			var overlay = $('<div></div>');
			
			element.on('keyup', function(){
				
				var val = $(this).val();
				if(val){
					overlay.text(eval(val)).appendTo('body');						
				}
				else{
					overlay.remove();
				}
			});

			// keypress -i jamank argelum e tarer grel inputum, 
			element.on('keypress', function (event){
				var self = $(this)
				var val = self.val();
				var k = event.which;

				// ev enter kam = sexmeluc patasxan@ granci inputi mej
				(k == 13 || k == 61) ? target.val(eval(val)) : '';

				return ($.inArray(k, keyCodes) !== -1) ? true : false;
			});

			// focusi jamanak haytnvum e div@
			element.on('focus', function(){
							
				overlay.css({
					'background-color': '#F2F4F6',
					'font-size': '28px',
					'position': 'absolute',
					'top' 	  : element.offset().top - 48,
					'left' 	  : element.offset().left + element.outerWidth() + 2,
					'display' : 'inline-block',
					'padding' : '0 10px',
					'margin'  : '0',
					'z-index' : '99',
					'border'  : '3px solid rgba(167, 180, 204, 0.36)',
					'border-radius' : '8px'
				});
			});

			element.on('blur', function() {
				var val = $(this).val();
				// focusic durs galuc patasxan@ grancum e inputi mej
				(val) ? target.val(eval(val)) : target.val('');

				// apply change
				$(this).trigger('change');

				// focusic haneluc jnjvum e div@.
				overlay.remove();
			});
		});
	};

	/**
	 * Плагин для работы с открытием и закрытием деревьев
	 * использовали в проекте маркета
	 */
	$.fn.Collapsable = {
		//события при открытии закрытии уровней
        toggleLevelsEvents : function(){

        	if($.fn.Collapsable.applied == undefined || !$.fn.Collapsable.applied){
	        	$.fn.Collapsable.applied = true;

	        	//событие при свертывании 
	        	$(document).on('hidden.bs.collapse', '.panel-group', function (event) {
	        		var panel = $(event.target).closest('.panel').find('.panel-heading');
				  	// при закрытии закрывать все вложенные
					panel.find("a:not(.link).opened").each(function(index, value){
						$.fn.Collapsable.toggle($(this).attr('href'), 'close');
					});

					location.hash = $.fn.Collapsable.getHash();
				});

				$(document)
					//при нажатии на открытии любого уровня
					.on('click', ".panel-heading a:not(.link)", function(){
						$(this).toggleClass('opened'); 

						if($(this).hasClass('opened')) location.hash = $.fn.Collapsable.getHash();
					});

				this.openLevels(location.hash.substr(1));	
        	}
        	
        },

        //открытие нужных уровней при загрузке страницы
        openLevels : function(hash){
        	var params = $.parseJSON(hash);

        	if(params == null || params.opened == undefined) return false;
        	
        	ids = params.opened;
        	$.each(ids, function(index, value){
				$('.panel-heading h4 a[href="#'+value+'"]').trigger('click');
        	});
        	
        },

        //показ / скрытие конкретного звена
        toggle : function(href, action){

        	if(action == 'open'){
				$("a[href="+href+"]").removeClass('collapsed').addClass('opened');
				$(href).removeClass('collapse').addClass('in').attr('style', 'height: auto');
			}

			if(action == 'close'){
				$("a[href="+href+"]").addClass('collapsed').removeClass('opened');
				$(href).removeClass('in').addClass('collapse').attr('style', 'height: 0px');
			}	
		},

		getHash : function(){
			var ids = Array();
			$('.panel-heading h4 a:not(.link).opened').each(function(index, value){
				ids.push($(this).attr('href').substr(1));
			});
			return "#" + JSON.stringify({opened: ids});
		}
	};
	
	//events
	$(function(){
		/**
		 * Зависимые объекты
		 * то есть можно делать зависимость одного объекта к другому, 
		 * чтобы тот мог либо показывать или скрывать его, либо делать активным или неактивным
		 *
		 * Пример использования
		 * <input type="checkbox" data-related="test" data-action="visibility">
		 * <input type="text" id="test">
		 *
		 * либо 
		 * <input type="checkbox" data-related="test2" data-action="activation">
		 * <input type="text" id="test2">
		 */
		$('select:has(option[data-related]), input[data-related]').on('change', function(e){

			// find last and if it has data-related run
			if(this.lastselected != undefined && 
					this.lastselected.data('related') != undefined){
				var lastid = this.lastselected.data('related');
				$('#' + lastid).toggleElement(this.lastselected.data('action'));
			}
			
			// if current has data-related run
			var element = this.tagName == 'SELECT' ? $(this).find('option:selected') : $(this);
			if(element.data('related') != undefined){
				$('#'+element.data('related')).toggleElement(element.data('action'));
			}
			
			//set lastselected
			if(this.tagName == 'SELECT'){
				this.lastselected = $(this).find('option:selected');
			}
		});

		$('.math').math();

	});

	
})(jQuery);

/**
 * Плагин для работы с кнопками форм, 
 * для активации, и деактивации кнопок
 */
var Forms = {

	disableBtn : function(button){
		if(! (button instanceof jQuery))
			button = $(button);

		var waitText = button.data('wait') ? button.data('wait') : Yii.t('admin', 'Подождите');
		
		var _button = button.get(0);
		
		_button.successText = button.val();
		if(button.val().length == 0) 
			_button.successText = button.html();

		var matches = _button.successText.match(/(<i.+\/i>)*.+/);
		prefix = matches[1]!=undefined ? matches[1] : '';

    	button.addClass('disable').attr('disabled', 'disabled');
    	
    	if(!button.hasClass('without-value')) 
    		button.val(prefix + waitText).html(prefix + waitText);
	},

	enableBtn : function(button){
		var _button = button.get(0);
		var successText = button.data('success');

		if(!successText) successText = _button.successText;

    	button.removeClass('disable').removeAttr("disabled");
    	
    	if(!button.hasClass('without-value')) 
    		button.val(successText).html(successText);   

	},


	disableFormSubmit : function(form){
		var btn = form.find("button[type=submit], input[type=submit]");

		if(btn.length == 1) 
			this.disableBtn(btn);
		else {
			btn = $("[data-form='"+form.prop('id')+"']");
			if(btn.length == 1) this.disableBtn(btn);
		}


	},

	enableFormSubmit : function(form){
		var btn = form.find("button[type=submit], input[type=submit]");

		if(btn.length == 1) 
			this.enableBtn(btn);
		else {
			btn = $("[data-form='"+form.prop('id')+"']");
			if(btn.length == 1) this.enableBtn(btn);
		}
	},

	uploadSettings : function(btn){
		return {
            xhr: function() {  // Custom XMLHttpRequest
                var myXhr = $.ajaxSettings.xhr();
                if(myXhr.upload){ // Check if upload property exists
                    //myXhr.upload.addEventListener('progress',progressHandlingFunction, false); // For handling the progress of the upload
                }
                return myXhr;
            },
            //Ajax events
            beforeSend: function(){
                if (btn != undefined) Forms.disableBtn(btn);
            },
            //Options to tell jQuery not to process data or worry about content-type.
            cache: false,
            contentType: false,
            processData: false
        };
	}

} 