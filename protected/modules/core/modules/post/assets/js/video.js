$(function(){
	
	$('#Video_video_url').bind('change', function(){

		if($(this).val() != ''){
			embed = parseYoutube($(this).val());
			$('#videoplayer').html('<iframe class="mt10" width="640" height="380" src="http://www.youtube.com/embed/' + embed + '" frameborder="0" allowfullscreen></iframe>').show();
		}
		else{
			$('#videoplayer').html('').hide();
		}
	});
});