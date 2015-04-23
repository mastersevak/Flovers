var Gridview = {

	//событие перед обновлением gridview
	beforeAjaxUpdate : function(id, params){
		var method = $('#'+id).data('update-method') != undefined ? $('#'+id).data('update-method') : 'GET';

		if(method.toLowerCase() == 'post'){
			
			if(params.data == undefined) params.data = {};
			params.data[getCsrf().name] = getCsrf().value;
		}
	},

	//событие после обновления gridview
	afterAjaxUpdate : function(id, data){
		var settings = $('#'+id).yiiGridView.settings[id];

		if($('.search-form').length > 0){
			$('.search-form').get(0).savedValues = $(settings.filterSelector).serialize();
		}

		$('#'+id).find('select').not('.nostyler').selectStyler();
		$('#'+id).find('[rel=tooltip]').tooltip();
	},

	//изменение количества страниц
	onPageSizeChange : function(select){
		$.cookie($(select).data('update'), $(select).val(),
			{path: '/'});

		$.fn.yiiGridView.update($(select).data('grid-id'));
	},

	changeTableWidth : function(table){ //временно не используется
		var table = $(table).find('table.table');

		var ths = table.find('thead th');

		var minTableWidth = table.outerWidth();
		var sum = 0;
		
		ths.each(function(index, th){
			if($(th).attr('width') != undefined){
				sum += parseInt($(th).attr('width')) + 2 * parseInt($(th).css('padding'));
			}
			else if($(th).width() != undefined){
				sum += parseInt($(th).width()) + 2 * parseInt($(th).css('padding'));
			}
		});

		if(sum > minTableWidth) table.outerWidth(sum);	
		else table.outerWidth('100%');
	},

	//обновление одной ячейки через таблицу
	ajaxUpdateCell : function(options, callback){

		jPost(options.updateUrl, {
			'id' : options.id,
			'val' : options.val,
			'attribute' : options.attribute,
			'model' : options.model != undefined ? options.model : null,
			'ajaxedit' : true
		}, function(data){

			if(callback != undefined && 
				Object.prototype.toString.call(callback) === "[object Function]")
				(callback)(data); //callback call
			
		}, 'json');
	},

	hideColumn : function(table, columnIndex){
		var th = table.find('th:eq('+columnIndex+')').addClass('hidden');

		table.find('tbody tr, thead tr.filters').find('td:eq('+columnIndex+')').addClass('hidden');

		var colId = 'hidden-column-'+th.prop('id');
		$.cookie(colId, colId);
		
		var drop = $('<option value = "opencol_'+(columnIndex+1)+'">'+th.text()+'</option>'); 
		var select = table.closest('.grid-view').find('select.show-table-columns');
		select.append(drop);	
		select.selectStyler('update');
	},

	showColumn : function(table, columnIndex){
		table.find('th:eq('+columnIndex+')').removeClass('hidden');
		
		table.find('tbody tr, thead tr.filters').find('td:eq('+columnIndex+')').removeClass('hidden');

		var colId = 'hidden-column-'+table.find('th:eq('+ columnIndex +')').prop('id');
		$.cookie(colId, null);

		var select = table.closest('.grid-view').find('select.show-table-columns');
		select.find('option[value="opencol_'+(columnIndex+1)+'"]').remove();
		select.selectStyler('update');

		table.find('select:visible').selectStyler();
	},

	block : function(grid){
		if(! (grid instanceof jQuery)){
			grid = $('#'+grid);
		}

		grid.addClass('grid-view-loading');
	},

	unblock : function(grid){
		if(! (grid instanceof jQuery)){
			grid = $('#'+grid);
		}

		grid.removeClass('grid-view-loading');
	},

	setPageHeaderWidth : function(grid){
		if(! (grid instanceof jQuery)){
			grid = $('#'+grid);
		}

		var width = grid.find('table').outerWidth(true) + 2*26; 

		if($('.page-content .page-header').length && width > $(document).outerWidth(true)) {
			$('.page-content .page-header').outerWidth(width);
		}
	}
};

//переопределяем одну функцию
(function ($) {

	$.fn.yiiGridView.getChecked = function (id, column_id) {
		var checked = [];

		if (column_id.substring(column_id.length - 2) !== '[]') {
			column_id = column_id + '[]';
		}

		$('#' + id).find('table').children('tbody').children('tr').children('td').find('input[name="' + column_id + '"]').each(function (i) {
			if (this.checked) {
				checked.push(this.value);
			}
		});
		return checked;
	}

	$.fn.disableSelection = function() {
        return this
                 // .attr('unselectable', 'on')
                 .css({
                 	'user-select':'none',
                 	'-webkit-user-select':'none',
                 	'-ms-user-select':'none',
                 	'-moz-user-select':'none'
                 });
                 // .on('selectstart', false);
    };

    $.fn.enableSelection = function() {
        return this
                 // .attr('unselectable', 'off')
                  .css({
                 	'user-select':'text',
                 	'-webkit-user-select':'text',
                 	'-ms-user-select':'text',
                 	'-moz-user-select':'text'
                 });
    };

})(jQuery);

//document ready
$(function(){

	//popover
	$(document).on('mouseover', "a[data-toggle='popover']", function(){
		$(this).popover({
			content: $(this).data('content')
		});
	}).on('mouseleave', "a[data-toggle='popover']", function(){
		$(this).popover('hide');
	});


	/**
	 * фикс при изменении значений в фильтрах
	 * после изменения фильтров, если до этого была нажата кнопка очистки фильтров, 
	 * фильтры не работали из за того что параметр clearFilters=1, сохранялся
	 */
	$(document).on('change.yiiGridView keydown.yiiGridView', 
			".grid-view .filters input, .grid-view .filters select", function (event) {
				
		var id = $(this).closest('.grid-view').attr('id');
		var url = $('#'+id).yiiGridView('getUrl');

		$('#'+id).yiiGridView.settings[id].url = url.replace(/clearFilters=\d+/, '');
	});

	/**
	 * При отметке чекбохов если нажата кнопка SHIFT, 
	 * отмечать все строки между первым нажатием и вторым
	 */
	$(document).on('mousedown', ".grid-view .checkbox-column label", function(e){
		var grid = $(this).closest('.grid-view').get(0); //js object
		var currentRowIndex = $(this).closest('tr').index();
		
		$(this).closest('table').disableSelection();
		
		if(grid.lastSelectedRow != undefined && 
			grid.lastSelectedRow < currentRowIndex && e.shiftKey == true){

			//отметить все строки между ними
			for(var row = grid.lastSelectedRow + 2; row <= currentRowIndex; row ++){
				$(grid).find('table.table tr:eq('+ (row + 1) +')').find('.checkbox-column :checkbox').trigger('click');
			}
		}

		grid.lastSelectedRow = currentRowIndex;
		
	});

	$(document).on('mouseup', ".grid-view .checkbox-column label", function(e){
		$(this).closest('table').enableSelection();
	});


	//contenteditable
	$(document).on('dblclick', ".grid-view td.contenteditable", function(e){
		e.stopPropagation();

		var td = $(this);
		var input = td.hasClass('date') ? $('#datecol_init_datepicker') : $('<input type="text">');
		var initVal = $(this).text();
		var endVal = initVal;
		input.val(initVal);

		//add autocomplete if needed
		if(td.hasClass('autocomplete')) {

			//find filter autocomplete
			var index = td.index();
			var options = td.closest('table').find('tr.filters td:eq('+index+')').find('input').autocomplete('option');
			input.autocomplete({source : options.source});
		}

		if($(this).attr('align') != undefined) 
			input.css('text-align', $(this).attr('align'));
		
		// input.outerWidth($(this).outerWidth());
		input.outerHeight($(this).outerHeight());
		input.data('attribute', $(this).data('attribute'));


		function changeValue(){
			var sendval = td.hasClass('autocomplete') ? input.autocomplete('option').value : input.val();
			if(sendval == undefined) {
				hideBlock();
				return;
			}

			var params = {
				'id' : td.closest('tr').find('td.checkbox-column :checkbox').val(),
				'val' : sendval,
				'attribute' : td.data('attribute'),
				'updateUrl' : $.fn.yiiGridView.settings[td.closest('.grid-view').prop('id')].url
			};

			Gridview.ajaxUpdateCell(params, function(data){
				if(data.success){
					endVal = input.val();

					//change user name, if column exists
					if(data.user && td.closest('tr').find('td.user').length > 0){
						td.closest('tr').find('td.user').data('user-id', data.user.id);
						td.closest('tr').find('td.user a')
							.text(data.user.name).addClass('changed')
							.data('original-title', 'Обновлен: ' + data.user.date_changed);
					}
				}
				else{
					//show error
					$.each(data.message, function(index, value){
						showErrorMessage(value);
						playNotificationSound();
					});
				}

				//убрать текстовый блок
				hideBlock();
			});
		}

		function hideBlock(){
			td.removeClass('editing');
			if(td.hasClass('date')) input.appendTo($('body'));
			td.text(endVal);
		}

		var ajaxSend = false;
		//add input event
		input.on('blur keydown change', function(e){
			e.stopPropagation();
			if(ajaxSend) return;

			var self = $(this);
			
			//во время потери фокуса или нажатии на enter
			if(e.type == 'keydown' && e.keyCode == 13 || e.type == 'blur' || e.type == 'change'){ //enter
				//post
				e.preventDefault();
				if(initVal != input.val()){ //значение изменилось
					if(td.data('ajax-update') != false){
						changeValue();
						ajaxSend = true;
					}
				}
				else{ //значение не изменялось
					hideBlock();
				}
					
			}


		}).on('dblclick', function(e){
			e.stopPropagation();
		});	

		
		$(this).html(input);
		$(this).addClass('editing');
		input.select(); //выделить текст
	});

	//при изменении boolean column checkbox
	$(document).on('change', ".grid-view td.boolean-column.editable input:checkbox", function(){
		var self = $(this);
		var td = self.closest('td');
		var initVal = self.is(':checked') ? 1 : 0;
		var endVal = initVal;

		var params = {
			'id' : td.closest('tr').find('td.checkbox-column :checkbox').val(),
			'val' : self.is(':checked') ? 1 : 0,
			'attribute' : td.data('attribute'),
			'updateUrl' : td.data('url') != undefined ? td.data('url') : $.fn.yiiGridView.settings[td.closest('.grid-view').prop('id')].url
		};

		Gridview.ajaxUpdateCell(params, function(data){
			if(data.success){
				endVal = self.is(':checked') ? 1 : 0;

				//change user name, if column exists
					if(data.user && td.closest('tr').find('td.user').length > 0){
						td.closest('tr').find('td.user').data('user-id', data.user.id);
						td.closest('tr').find('td.user a')
							.text(data.user.name).addClass('changed')
							.data('original-title', 'Обновлен: ' + data.user.date_changed);
					}
			}
			else{
				//show error
				$.each(data.message, function(index, value){
					showErrorMessage(value);
					playNotificationSound();
				});
			}
			
		});

	});

	//выборка autocomplete в таблице
	$(document).on( "autocompleteselect", ".grid-view", function( event, ui ) {
		event.stopPropagation();
		event.target.value = ui.item.value;

		$(event.target).autocomplete('option', {'value':ui.item.id});
		$(event.target).change();
	});


	//при нажатии на кнопку добавления строк в конец таблицы
	$(document).on( "click", "button[data-autoincrement], a[data-autoincrement]", function( event ) {
		event.preventDefault();

		var id = $(this).data('autoincrement');
		var table = $('#' + id).find('table');
		var tbody = table.find('tbody');

		if(table[0].insertedRows == undefined)
			table[0].insertedRows = 0;
		
		var tr = $('<tr class="autoincrement"></tr>');
		
		$(table).find('thead th').each(function(index, cell){
			var content = ''; 
			if($(cell).data('autoincrement') != undefined){
				content = $($(cell).data('autoincrement'))[0];
				content.name = content.name.replace(/(\[.+\])/, '[' + table[0].insertedRows + ']' + "$1");
				content.id = content.id + '_' + table[0].insertedRows;
			}
			
			var td = $('<td></td>');
			td.html(content);
			tr.append(td);
		});
		
		if(tbody.find('tr:has(td.empty)').length > 0){
			tbody.html(tr);
		}
		else tbody.append(tr);

		table[0].insertedRows ++;
	});


	//скрыть колонки в таблице
	$(document).on('click', '.grid-view .table .hide-column', function(event){
		event.preventDefault();
		var index = $(this).closest('th').index();
		var table = $(this).closest('table');

		Gridview.hideColumn(table, index);
	});

	//показать колонки в таблице
	$(document).on('change', '.show-table-columns', function(event){
		event.stopPropagation(); 
		var target = $(event.target);
		var index = target.find('option:selected').val().substr('opencol_'.length);
		var table = target.closest('.grid-view').find('table');

		Gridview.showColumn(table, parseInt(index) - 1);
	});

});
