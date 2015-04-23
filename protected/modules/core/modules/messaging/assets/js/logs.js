(function($) {
	var methods = {
		resend : function(target, grid){
			Forms.disableBtn(target);

			var url = target.data('url');
			var model = target.data('model');
			var id = target.data('id');

			jPost(url, {id : id, model : model}, function(data){
				$.fn.yiiGridView.update(grid);
			});
		},

		showMessage : function(target){
			target.addClass('hidden');
			target.closest('div').find('.hidden-message').removeClass('hidden');
			target.closest('div').find('.hide-message').removeClass('hidden');
		},

		hideMessage : function(target){
			target.addClass('hidden');
			target.closest('div').find('.hidden-message').addClass('hidden');
			target.closest('div').find('.show-message').removeClass('hidden');	
		}
	} 

	$.fn.logs = function(method)
	{
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
		
	});

})( jQuery );

