$(function(){

	$(window).on('hashchange', function(){
		var hash = location.hash.substring(1);
		changeSettingsPage(hash);
	});

	var hash = location.hash.substring(1);

	if(hash.length == 0 || $('.right-menu ul').find('li[data-id='+hash+']').length == 0){
		hash = $('.right-menu ul li:first').data('id');
	}
	
	setTimeout(function(){changeSettingsPage(hash)}, 10);
});

function changeSettingsPage(hash){

	$.fn.yiiGridView.update('settings-grid', {data: {category: hash}});

	$('.right-menu ul').find('li')
			.removeClass('active')
			.filter('[data-id='+hash+']')
			.addClass('active');
}