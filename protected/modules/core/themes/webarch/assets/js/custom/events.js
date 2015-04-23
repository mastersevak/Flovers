$(function(){

	//form styler
	$('select:visible').not('.nostyler, .tbselect').selectStyler();
	
	//select picker, во всех, модальных окнах
	$('div[role=domodal-dialog]').filter('.modal').bind({
		
		"shown.bs.modal" : function (e) { //событие после показа модального окна
	        
			$(this).find('select:visible').not('.nostyler, .tbselect').selectStyler();
	    },
	});

	//search form
	$('div[role=domodal-dialog]').filter('.modal').has('.search-form').bind({
		
		"shown.bs.modal" : function (e) { //событие после показа модального окна
	        //подставить поля если они были сохранены до закрытия данной формы
	        
	        var form = $(this).find('form');
	        if(form.length > 0 && form.get(0).savedValues != undefined)
	        	form.deserialize(form.get(0).savedValues, true);
	        
	        $('select', form[0]).selectStyler('refresh');
	    },

	    "hidden.bs.modal" : function(e){
	    	var form = $(this).find('form');
	    	if(form.length > 0){
	    		form.get(0).reset();
	    	}
	    }
	});

	//вкладки
	$('.nav.nav-tabs').on('shown.bs.tab', 'a', function (e) {
		//e.target // activated tab
		//e.relatedTarget // previous tab
		
		var href = $(this).prop('href');
		var content = $('.tab-content .tab-pane'+href.substring( href.indexOf('#')) );

		content.find('select:visible:not(".tbselect")').not('.nostyler, .tbselect').selectStyler();
	});

	/**
	 * изменение полей с лимитами
	 */
	$('input:text[data-limit], textarea[data-limit]').on('keyup', function(){
		self = $(this);

		clearTimeout(this.timer);
		
		//создает окошко если его нет
		if($('#limit-window').length == 0){
			$('<div id="limit-window"/>').appendTo('body');
		}
		else{
			$('#limit-window:not(:animated)').stop().fadeIn('fast');
		}

		$('#limit-window').text(self.val().length);

		if(self.val().length > self.data('limit')){
			//вы превысили лимит
			self.siblings('.limit').addClass('error');
		}
		else{
			self.siblings('.limit').removeClass('error');
		}

		this.timer = setTimeout(function(){
			$('#limit-window').stop().fadeOut('fast');
		}, 2000);
	});

	$('input:text[data-limit], textarea[data-limit]').on('blur', function(){
		if($('#limit-window').length > 0)
			$('#limit-window').stop().fadeOut('fast', function(){
				$('#limit-window').val('');
			});
	});

	//manual slug
	$('.slug-checkbox input:checkbox').on('change', function(){
		$('.slug-text input').prop('readonly', !$(this).prop('checked'));
		
	});

	//auto slug
	$('input[data-slug-to]').on('change', function(){
       	var self = $(this);
		var val = $.trim(self.closest('form').find('#' + self.data('slug-to')).val());

        if(!$('.slug-checkbox input:checkbox').prop('checked')){

        	jPost(self.data('slugger') ? self.data('slugger') : 'ajaxslug', {
        		string: self.val(),
        		model: self.data('model') ? self.data('model') : self.prop('id').substr(0, self.prop('id').indexOf('_') )
        	}, function(data){
        		self.closest('form').find('#' + self.data('slug-to')).val( data );
        	});
        }       
	});

	//fix checkbox in accordeons bootstrap
	$(".panel-body input[type=checkbox], .panel-body label").on("click", function(event) {
	    event.stopPropagation();
	});

});

