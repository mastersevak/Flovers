$(document).ready(function() {		
	// setHeaderWidth();

	$(".remove-widget").click(function() {		
		$(this).parent().parent().parent().addClass('animated fadeOut');
		$(this).parent().parent().parent().attr('id', 'id_a');

		//$(this).parent().parent().parent().hide();
		 setTimeout( function(){			
			$('#id_a').remove();	
		 },400);	
		return false;
	});

	$("[data-toggle='tooltip'], [rel='tooltip']").tooltip();
	$("[data-toggle='popover'], [rel='popover']").popover();

	$(".inside").children('input').blur(function(){
		$(this).parent().children('.add-on').removeClass('input-focus');		
	});
	
	$(".inside").children('input').focus(function(){
		$(this).parent().children('.add-on').addClass('input-focus');		
	});	
	
	$(".input-group.transparent").children('input').blur(function(){
		$(this).parent().children('.input-group-addon').removeClass('input-focus');		
	});
	
	$(".input-group.transparent").children('input').focus(function(){
		$(this).parent().children('.input-group-addon').addClass('input-focus');		
	});	
	
	$(".bootstrap-tagsinput input").blur(function(){
		$(this).parent().removeClass('input-focus');
	});
	
	$(".bootstrap-tagsinput input").focus(function(){
		$(this).parent().addClass('input-focus');		
	});
	
	/*$('#my-task-list').popover({ 
        html : true, 
        content: function() {
          return $('#notification-list').html();
        }
    });*/
	
	// $('#user-options').click(function(){
	// 	$('#my-task-list').popover('hide')
	// });

	$('.chat-menu-toggle').sidr({
		name:'sidr',
		side: 'right',
		complete:function(){		 
		}
	});

	$(".simple-chat-popup").click(function(){
		$(this).addClass('hide');
		$('#chat-message-count').addClass('hide');	
	});

	setTimeout( function(){
		$('#chat-message-count').removeClass('hide');	
		$('#chat-message-count').addClass('animated bounceIn');
		$('.simple-chat-popup').removeClass('hide');			
		$('.simple-chat-popup').addClass('animated fadeIn');		
	}, 5000);

	setTimeout( function(){
		$('.simple-chat-popup').addClass('hide');			
		$('.simple-chat-popup').removeClass('animated fadeIn');		
		$('.simple-chat-popup').addClass('animated fadeOut');		
	}, 8000);
	

	$('[data-height-adjust="true"]').each(function(){
		var h = $(this).attr('data-elem-height');
		$(this).css("min-height", h);
		$(this).css('background-image', 'url(' + $(this).attr("data-background-image") + ')');
		$(this).css('background-repeat', 'no-repeat');
		if($(this).attr('data-background-image')){		
		
		}	
	});

	function equalHeight(group) {
	   tallest = 0;
	   group.each(function() {
		  thisHeight = $(this).height();
		  if(thisHeight > tallest) {
			 tallest = thisHeight;
		  }
	   });
	   group.height(tallest);
	}

	$('[data-aspect-ratio="true"]').each(function(){
		$(this).height($(this).width());
	})

	$('[data-sync-height="true"]').each(function(){
		equalHeight($(this).children());
	});	

	$( window ).resize(function() {	
		$('[data-aspect-ratio="true"]').each(function(){
			$(this).height($(this).width());
		})
		$('[data-sync-height="true"]').each(function(){
			equalHeight($(this).children());
		});	
	});

	// var eleHeight =window.screen.height;
	// eleHeight=eleHeight-(eleHeight*22.5/100);
	
	// if( !(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))  ) {
	// 	$('#main-menu-wrapper').slimScroll({
	// 		color: '#a1b2bd',
	// 		size: '4px',
	// 		height: eleHeight,
	// 		alwaysVisible: false
	// 	});
	// }
	
	if ($.fn.lazyload){	
		$("img.lazy").lazyload({
			effect : "fadeIn"
		});
	}
	
	/*$('.grid .tools a.remove').on('click', function () {
        var removable = jQuery(this).parents(".grid");
            jQuery(this).closest(".grid").hide();
    });

    $('.grid .tools a.reload').on('click', function () {
        var el =  jQuery(this).parents(".grid");
        blockUI(el);
		window.setTimeout(function () {
           unblockUI(el);
        }, 1000);
    });*/
	
	$('.grid .tools .collapse, .grid .tools .expand').on('click', function () {
        var el = jQuery(this).parents(".grid").children(".grid-body");
        if (jQuery(this).hasClass("collapse")) {
            jQuery(this).removeClass("collapse").addClass("expand");
            el.slideUp(200);
        } else {
            jQuery(this).removeClass("expand").addClass("collapse");
            el.slideDown(200);
        }
    });		
		
	$('.user-info .collapse').on('click', function () {
        jQuery(this).parents(".user-info ").slideToggle();
	});

	$('.panel-group').on('hidden.bs.collapse', function (e) {
		// $(this).find('.panel-heading').not($(e.target)).addClass('collapsed');
	});
	
	$('.panel-group').on('shown.bs.collapse', function (e) {
		// $(e.target).prev('.accordion-heading').find('.accordion-toggle').removeClass('collapsed');
	});

	$(window).setBreakpoints({
		distinct: true, 
		breakpoints: [
			320,
			480,
			768,
			1024
		] 
	});   	

	//Break point entry 
	$(window).bind('enterBreakpoint320',function() {	
		$('#main-menu-toggle-wrapper').show();		
		$('#portrait-chat-toggler').show();	
		$('#header_inbox_bar').hide();	
		$('#main-menu').removeClass('mini');		   
		$('.page-content').removeClass('condensed');
		// rebuildSider();
	});	
	
	$(window).bind('enterBreakpoint480',function() {
		$('#main-menu-toggle-wrapper').show();		
		$('.header-seperation').show();		
		$('#portrait-chat-toggler').show();				
		$('#header_inbox_bar').hide();	
		//Incase if condensed layout is applied
		$('#main-menu').removeClass('mini');		   
		$('.page-content').removeClass('condensed');			
		// rebuildSider();
	});
	
	$(window).bind('enterBreakpoint800',function() {		
		$('#main-menu-toggle-wrapper').show();		
		$('#portrait-chat-toggler').show();			
		$('#header_inbox_bar').hide();	
		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {			
			$('#main-menu').removeClass('mini');	
			$('.page-content').removeClass('condensed');	
			// rebuildSider();
		}	
	});

	$(window).bind('enterBreakpoint1024', function() {	
		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {			
			var elem = jQuery('.page-sidebar ul');
		    elem.children('li.open').children('a').children('.arrow').removeClass('open');
            elem.children('li.open').children('a').children('.arrow').removeClass('active');
            elem.children('li.open').children('.sub-menu').slideUp(200);
            elem.children('li').removeClass('open');
		}
	});
	
	$(window).bind('exitBreakpoint320',function() {	
		$('#main-menu-toggle-wrapper').hide();		
		$('#portrait-chat-toggler').hide();	
		$('#header_inbox_bar').show();			
		closeAndRestSider();		
	});	
	
	$(window).bind('exitBreakpoint480',function() {
		$('#main-menu-toggle-wrapper').hide();		
		$('#portrait-chat-toggler').hide();	
		$('#header_inbox_bar').show();			
		closeAndRestSider();	
	});
	
	$(window).bind('exitBreakpoint768',function() {
		$('#main-menu-toggle-wrapper').hide();		
		$('#portrait-chat-toggler').hide();	
		$('#header_inbox_bar').show();			
		closeAndRestSider();
	});

	function closeAndRestSider(){
		if($('#main-menu').attr('data-inner-menu')=='1'){
			$('#main-menu').addClass("mini");	
			$.sidr('close', 'main-menu');
			$.sidr('close', 'sidr');		
			$('#main-menu').removeClass("sidr");	
			$('#main-menu').removeClass("left");	
		}
		else{
			$.sidr('close', 'main-menu');
			$.sidr('close', 'sidr');		
			$('#main-menu').removeClass("sidr");	
			$('#main-menu').removeClass("left");
		}
	}
	
	$('.scroller').each(function () {
        $(this).slimScroll({
            size: '7px',
            color: '#a1b2bd',
            height: $(this).attr("data-height"),
            alwaysVisible: ($(this).attr("data-always-visible") == "1" ? true : false),
            railVisible: ($(this).attr("data-rail-visible") == "1" ? true : false),
            disableFadeOut: true
        });
    });
	
	$('.dropdown-toggle').click(function () {
		$("img").trigger("unveil");
	});
   
	if ($.fn.sparkline) {
		$('.sparklines').sparkline('html', { enableTagOptions: true });
	}

	$('table th .checkall').on('click', function () {
		if($(this).is(':checked')){
			$(this).closest('table').find(':checkbox').attr('checked', true);
			$(this).closest('table').find('tr').addClass('row_selected');
			//$(this).parent().parent().parent().toggleClass('row_selected');	
		}
		else{
			$(this).closest('table').find(':checkbox').attr('checked', false);
			$(this).closest('table').find('tr').removeClass('row_selected');
		}
    });

	$('.animate-number').each(function(){
		 $(this).animateNumbers($(this).attr("data-value"), true, parseInt($(this).attr("data-animation-duration")));	
	});

	$('.animate-progress-bar').each(function(){
		 $(this).css('width', $(this).attr("data-percentage"));
		
	});

	$('.widget-item > .controller .reload').click(function () { 
		var el =$(this).parent().parent();
		blockUI(el);
		  window.setTimeout(function () {
               unblockUI(el);
            }, 1000);
	});

	$('.widget-item > .controller .remove').click(function () {
		$(this).parent().parent().parent().addClass('animated fadeOut');
		$(this).parent().parent().parent().attr('id', 'id_remove_temp_id');
		 setTimeout( function(){			
			$('#id_remove_temp_id').remove();	
		 },400);
	});
	
	$('.tiles .controller .reload').click(function () { 
		var el =$(this).parent().parent().parent();
		blockUI(el);
		  window.setTimeout(function () {
               unblockUI(el);
            }, 1000);
	});

	$('.tiles .controller .remove').click(function () {
		$(this).parent().parent().parent().parent().addClass('animated fadeOut');
		$(this).parent().parent().parent().parent().attr('id', 'id_remove_temp_id');
		 setTimeout( function(){			
			$('#id_remove_temp_id').remove();	
		 },400);
	});
        
    $(".sortable").sortable({
        connectWith: '.sortable',
        iframeFix: false,
        items: 'div.grid',
        opacity: 0.8,
        helper: 'original',
        revert: true,
        forceHelperSize: true,
        placeholder: 'sortable-box-placeholder round-all',
        forcePlaceholderSize: true,
        tolerance: 'pointer'
    });

    function blockUI(el) {		
        $(el).block({
            message: '<div class="loading-animator"></div>',
            css: {
                border: 'none',
                padding: '2px',
                backgroundColor: 'none'
            },
            overlayCSS: {
                backgroundColor: '#fff',
                opacity: 0.3,
                cursor: 'wait'
            }
        });
    }
	 
    // wrapper function to  un-block element(finish loading)
    function unblockUI(el) {
        $(el).unblock();
    }
	
	$(window).resize(function() {
		// setHeaderWidth();
	});
	
	$(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('.scrollup').fadeIn();
        } else {
            $('.scrollup').fadeOut();
        }
    });
		
	$('.scrollup').click(function(){
		$("html, body").animate({ scrollTop: 0 }, 700);
		return false;
    });	
	
	$("img").unveil();

	var update = function(){
		var loc = ['bottom', 'right'];
  		var style = 'flat';
	    var classes = 'messenger-fixed';

	    for (var i=0; i < loc.length; i++)
	      classes += ' messenger-on-' + loc[i];

	    $.globalMessenger({ extraClasses: classes, theme: style });
	    Messenger.options = { extraClasses: classes, theme: style };
	  };

	  update();

});