var Filters = {

	ajaxUpdate : false,

	//сброс полей
	resetField : function(event, element) {
		var target;
		var reset = false;

		if(event instanceof $.Event){
			event.preventDefault();
			event.stopPropagation();

			target = $(event.target);
			target.blur();
		}
		else{
			target = $(element);
		}

		var fieldtype = target[0].tagName.toLowerCase();

		switch(fieldtype){
			case 'input':
				switch(target.prop('type')){
					case 'checkbox':
						reset = true;
						Filters.resetCheckbox(target);
						break;
					case 'radio':
						reset = true;
						Filters.resetRadio(target);
						break;
					case 'text':
						reset = true;
						Filters.resetInput(target);
						break;
				}
				
				break;
			case 'select':
				reset = true;
				Filters.resetSelect(target);
				break;
		}	

	},

	//сброс input - ов
	resetInput : function(target) {
		// target.val(target.data('default-value') ? target.data('default-value') : '');
		target.val('');
		target.trigger('change');	
	},

	// сброс select - ов
	resetSelect : function(target) {
		// target.val( target.data('default-value') ? target.data('default-value') : '' );
		target.val( '' );
		target.selectStyler('refresh');
	},

	//сброс checkbox - ов
	resetCheckbox : function(target){
		// target.prop('checked', target.data('default-value') ? target.data('default-value') : false);
		target.prop('checked', false);
		target.trigger('change');
	},

	//сброс radio - ов
	resetRadio : function(target){	
		// target.prop('checked', target.data('default-value') ? target.data('default-value') : false);
		target.prop('checked', false);
		target.trigger('change');
	},

	//сброс полей всех фильтров или одного блока
	resetBlock : function(target, norefresh) {
		var resetAllBlocks = false;

		if(target == undefined) {
			var resetAllBlocks = true
			target = $('#top-filters .filter-blocks');
		}
		
		allCount = target.find('input, select').length;

		target.find('input, select').each(function(index, element){
			Filters.resetField(index, element);

			if(norefresh == undefined && index == allCount - 1) {//последний элемент
				if($('#top-filters').data('ajax-update') == '1')
					Filters.ajaxUpdate = true;

				// $('#top-filters').closest('form').submit(); 
			}
		});

		if(resetAllBlocks) $('#top-filters').closest('form').submit(); 
	},

	fillBlock : function(target){
		if(target == undefined) {
			target = $('#top-filters .filter-blocks');
		}	
		var filterBlock = target.closest('.filter-block');
		var exceptions = target.data('exceptions').split(',');
		var type = target.data('type');
		var checkboxes = filterBlock.find(':checkbox');

		$.each(checkboxes, function(index, checkbox){
			if($.inArray(checkbox.id, exceptions) == -1){
				if(type == 'check'){
					target.data('type', 'uncheck');
					$(checkbox).prop('checked', true);
				}else{
					target.data('type', 'check');
					$(checkbox).prop('checked', false);
				}
			}
		});
	},

	//скрытие блоков
	hideBlock : function(target) {
		target.addClass('hidden');
		var blockname = target.find('h4').text();
		var id = target.prop('id');

		var blockId = 'hidden-fb-'+id;
		$.cookie(blockId, blockId);

		var drop = $('<li><div class = "checkbox"><input type = "checkbox" id = "show_hfb_'+id+'"><label for= "show_hfb_'+id+'">'+blockname+'</label></div></li>'); 
		$('#top-filters .filter-tools .dropdown-menu').append(drop);
	},

	//показать блоки
	showBlock: function(target) {
		target.closest('li').remove();
		var id = target.prop('id').substr('show_hfb_'.length);

		var blockId = 'hidden-fb-'+id;
		$.cookie(blockId, null);

		$('#top-filters .filter-block').filter('#' + id).removeClass('hidden');
	},

	//показать / скрыть фильтры
	toggle : function(e){
		e.preventDefault();
		
		var topFilters = $('#top-filters');
		var btn = topFilters.find('.btn-toggle-filters');
		var options;
		
		if(topFilters.hasClass('compact')){
			btn.attr('data-original-title', '');
			$.cookie('hiddenTopFilters'+topFilters.data('model'), null);

		}
		else{
			btn.attr('data-original-title', 'Скрыть фильтры');
			$.cookie('hiddenTopFilters'+topFilters.data('model'), 'hidden');
			
		}

		topFilters.toggleClass('compact');
	},

	//submit формы поиска
	search : function(event){
		event.preventDefault();
		event.stopPropagation();

		var gridId = $(this).data('grid-id') ? $(this).data('grid-id') : false;
		
		if(!gridId || $('#' + gridId).length == 0){
			var url = location.origin + location.pathname;
		}
		else{
			var url = $('#' + gridId).yiiGridView('getUrl');
		}
		
		var data = $.deparam.querystring($.param.querystring(url, $(this).serialize()));
		
		delete data['YII_CSRF_TOKEN'];
		delete data['ajax'];

		removeEmpty(data);
		if(this.beforeUpdate != undefined) (this.beforeUpdate)();

		this.changed = true; //так как сабмит идет из формы, то заливать форму значениями не нужно

		url = decodeURIComponent($.param.querystring(url.substr(0, url.indexOf('?')), data));

		if($(this).hasClass('main')) {
			window.History.pushState(null, document.title, url);
		}

		if(!this.beforesearch || (this.beforesearch)($(this), data)){
			(this.onsearch)($(this), url, data); //по умолчанию вызывается updateGrid
		}
	},

	//вызывается перед обновлением результатов поиска
	beforeSearch : function(form, data){
		return true;
	},

	//вызывается при обновлении результатов поиска, если beforeSearch вернул true
	onSearch : function(form, url, data){
		var gridId = $(form).data('grid-id') ? $(form).data('grid-id') : findGrid();

		if(!gridId || $('#' + gridId).length == 0) {
			console.log('grid not found');
			return false;
		}

		//если не было изменений в фильтрах
		if(!$(form).hasClass('main')) {
			$('#' + gridId).yiiGridView('update', {data:data});
		}
	},

	//заливка формы значениями из GET
	fillForm : function(event){
		console.log('fill form');
		Filters.resetBlock(undefined, true);

		var href = window.location.href;
		if(href.indexOf('#') != -1) {
			href = href.substr(href.indexOf('#')+1);
		}

		var params = $.deparam.querystring($.param.querystring(href, $(this).serialize()));

		var form = $('#top-filters').closest('form');

		Filters.fill(form, params);
	},

	fill : function(form, params, prefix){

		$.each(params, function(key, value){

			if(typeof(value) === "object"){
				prefix = key + '_';
				Filters.fill(form, value, prefix); 
			}
			else{
				if(prefix == undefined) prefix = '';

				if(form.find('#' + prefix+key).length > 0){
					var element = form.find('#' + prefix+key).get(0);

					switch(element.type.toLowerCase()){
						case 'text':
							element.value = value;
							$(element).trigger('change');
							break;
						case 'select-one':
							element.value = value;
							$(element).trigger('change');
							break;

					}
				}
			}

		});
	}

};

$(function(){

	var topFilters = $('#top-filters');
	var tools = topFilters.find('.filter-tools');

	//при сабмите поисковой формы
	$('.search-form').on('submit', Filters.search);

	Filters.ajaxUpdate = topFilters.data('ajax-update') == '1';
	
	//click on toggle filters
	tools.find('.btn-toggle-filters').on('click', Filters.toggle);

	//сброс полей всех фильтров
	tools.find('.btn-clear-filters').on('click', function(event){ 
		event.preventDefault();

		Filters.ajaxUpdate = false;
		Filters.resetBlock();
	});

	//сброс полей одного блока фильтров
	topFilters.find('.tools .reload').on('click', function(event){
		event.preventDefault();

		Filters.ajaxUpdate = false;
		var target = $(event.target).closest('.filter-block');
		Filters.resetBlock(target);
	});

	//скрытие блоков
	topFilters.find('.tools .remove').on('click', function(event){
		event.preventDefault();

		var target = $(event.target).closest('.filter-block');
		Filters.hideBlock(target);
	});

	//показать скрытые блоки
	tools.find('.dropdown-menu').on('change', 'input', function(event){
		event.preventDefault();

		var target = $(event.target);
		Filters.showBlock(target);
	});

	//сбросить поля правым щелчком
	topFilters.on('contextmenu', 'input:text', Filters.resetField);

	$('body').on('contextmenu', '.grid-view tr.filters input:text, .grid-view tr.filters .grid-view tr.filters', Filters.resetField);

	//сброс и выделение bootstrap-select - ов
	topFilters.on('contextmenu', '.selectStyler-view', function(event){
		event.preventDefault();
		var select = $(this).closest('.selectStyler-wrap').find('select');
		
		if(select.prop('multiple')){

			if(select.val() != null){
				Filters.resetSelect(select);
			}
			else{
				select.find('option').prop('selected', true).end().trigger('change');
			}
		}
		else{
			Filters.resetSelect(select);
		}
		
	});
	
	//сброс подблоков
	topFilters.on('click', '[data-reset]', function(event){
		event.preventDefault();

		Filters.resetBlock($(this).closest('div, fieldset'));
	});

	//trigger submit, on change input or select if ajax update is active	
	topFilters.on('change', 'input, select', function(e){
		//submit if ajaxupdate
		if(Filters.ajaxUpdate) {
			$(this).closest('form').submit();
		}
	});

	topFilters.on('keydown', 'input:text', function(e){

		if(e.keyCode == 13/*ENTER*/){
			$(this).closest('form').submit();
		}
	});

	//нажатие на кнопке поиска
	topFilters.on('click', '.btn-submit', function(e){
		e.preventDefault();

		$(this).closest('form').submit();
	});

	topFilters.on('click', '.fill-block', function(e){
		e.preventDefault();
		Filters.fillBlock($(this));
	});

	/**
	 * @todo выяснить что это такое
	 */
	$('#supplier-info .btn-toggle-filters').on('click', function(event){
		event.preventDefault();
		var supplier_info = $('#supplier-info');
		var btn = supplier_info.find('.btn-toggle-filters');
		var options;
		
		supplier_info.toggleClass('compact');
		
		if(supplier_info.hasClass('compact')){
			btn.attr('data-original-title', '');
		}
		else{
			btn.attr('data-original-title', 'Скрыть фильтры');
		}

	});

	//после изменения history.state 
	$(window).on('popstate', function(){
		//если изменения не были результатом submit-a формы
		if(location.hash.length < 2 && location.hash != ''){
			if($('#top-filters').closest('form').length > 0){

				if(!$('#top-filters').closest('form').get(0).changed){
					Filters.fillForm();
				}
			}	
			topFilters.closest('form').get(0).changed = false;
		}
		
	});
});  
