$(function(){
	//change titles
	$('.photos_list').delegate('textarea', 'change', function(){
		input = $(this);
		
		if(input.val != ''){
			$.post(input.closest('.photos_tabs').data('url'), 
				{id: input.data('id'), val: input.val()}, 
				function(result){
					if(result == 'false')
						input.val('');
					else {
						input.val(result);
					}
				});
		}
	});

	//change tab
	$('.style_chooser a').bind('click', function(e){
		e.preventDefault();

		var self = $(this);
		var chooser = self.closest('.style_chooser');
		
		chooser.find('a').removeClass('btn_orange');
		$(this).addClass('btn_orange');

		$('.photos_tabs').hide();
		$( '#' + self.data('item') + '_' + chooser.data('id')).show();

	}).first().trigger('click');

	//click on drop area
	$('.photos_tabs').on('click', '.qq-upload-drop-area', function(){
		$(this).next().find('input[type=\"file\"]').trigger('click');
	});
	
});

