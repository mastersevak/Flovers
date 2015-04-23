var UIButtons = {

	//показать / скрыть картинки
	showImages : function(element){

		//установка или удаление cookie
		var show = $.cookie('show_images');
		show = show != null ? null : 1;
		$.cookie('show_images', show, {path: '/'});

		var text = show ? $(element).data('hide-title') : $(element).data('show-title');

		//update grid
		if(gridId = findGrid(element)){
			$.fn.yiiGridView.update(gridId);
			$(element).val(text).html(text);
		}
		else{
			location.reload();
		}

		return false;
	},

	//удалить элемент
	delete : function(element){

		jConfirm(Yii.t('admin', 'Вы уверены что хотите удалить данный элемент?'),
			Yii.t('admin', 'Удалить элемент'), function(r) {
			if(r){
				window.location = $(element).data('url');
			}
		});
	},

	//удалить выбранные элемент
	deleteSelected : function(element){

		Forms.disableBtn($(element));

		gridId = findGrid(element);

		selected = $.fn.yiiGridView.getChecked(gridId, 'checked_rows');

		if( selected == '' ){
			showErrorMessage(Yii.t('admin', 'Вы должны выбрать элементы перед удалением!'));
			Forms.enableBtn($(element));
		}
		else {

			jConfirm(Yii.t('admin', 'Вы уверены что хотите удалить выбранные элементы?'),
				Yii.t('admin', 'Удаление элементов'), function(r) {
				if(r){
					var items  = selected.join(',');

					jPost($(element).data('url'), {items: items},
						function(){
							Forms.enableBtn($(element));
							$.fn.yiiGridView.update(gridId);
						});
				}
				else
					Forms.enableBtn($(element));

			});
		}

		return false;
	},

	//очистить фильтры
	clearFilters : function(element){
		$.fn.yiiGridView.update(findGrid(element), {data:{clearFilters:1}});
	},

	//сохранить
	save : function(element){
		var form;

		if($(element).closest('form').length > 0){
			form = $(element).closest('form');
		}
		else if($(element).data('form')){
			form = $('#' + $(element).data('form'));
		}
		else {
			return false;
		}

		form[0].submitbtn = $(element);

		var formsettings = $.fn.yiiactiveform.getSettings('#'+form.prop('id'));
		//if not ajaxvalidate
		if(formsettings != undefined &&
			formsettings.beforeValidate == undefined){
			Forms.disableFormSubmit(form);
		}

		form.submit();
	},

	//сохранить и закрыть
	saveAndClose : function(element){

		var form;

		if($(element).closest('form').length > 0){
			form = $(element).closest('form');
		}
		else if($(element).data('form')){
			form = $('#' + $(element).data('form'));
		}
		else {
			return false;
		}

		form[0].submitbtn = $(element);

		var action = form.prop('action');

		if(action.search(/close=true/) == -1){
			if(action.search(/\?/) != -1)
				action += '&close=true';
			else
				action += '?close=true';

			form.prop('action', action);

			if($(element).data('form')){
				form.submit();
			}
		}
	},

	//закрытие формы
	close : function(element){
		window.location = $(element).data('url');
	},

	file : function(element){
		$(element).closest('div').find('input[type=file]').trigger('click');
	},

	gotoUrl : function(element, target){
		target = typeof target !== 'undefined' ? target : '_self';
		window.open($(element).data('url'), target);
	}
};


var UIMenu = {

	buttonClick : function(element){
		window.location = $(element).data('url')
	}
};
