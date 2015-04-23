var Listview = {

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
		var settings = $('#'+id).yiiListView.settings[id];

		$('#'+id).find('select').not('.nostyler').selectStyler();
		
		if($.fn.tooltip != undefined)
			$('#'+id).find('[rel=tooltip]').tooltip();
	},

	//изменение количества страниц
	onPageSizeChange : function(select){
		$.cookie($(select).data('update'), $(select).val(),
			{path: '/'});

		$.fn.yiiListView.update($(select).data('list-id'));
	},

	/*block : function(listview){
		if(! (listview instanceof jQuery)){
			listview = $('#'+listview);
		}

		listview.addClass('listview-view-loading');
	},

	unblock : function(listview){
		if(! (listview instanceof jQuery)){
			listview = $('#'+listview);
		}

		listview.removeClass('listview-view-loading');
	}*/
};