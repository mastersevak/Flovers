/**
 *
 * используется на страницах
 * 
 * 1. Меню
 * 
 */
(function($) {
	var methods = {
		init : function(options){
			if($('#visible').is(':checked')){
				$("#Menu_visible").prop('readonly', true);
			}

			if($('#active').is(':checked')){
				$("#Menu_active").prop('readonly', true);
			}
		},

		// Отобразить дерево выбранного меню
		getInfo : function(jthis){
			var idMenu = jthis.data('id');
			var url = $('#rootDiv').data('url');
			$.fn.tree("refreshTree", "#root-form-modal", idMenu);
			$(".getMenu").removeClass('active');
			jthis.addClass('active');
		},

		// Создать новое меню
		createMenu : function(){
			$('#rootDiv').addClass('grid-view-loading');
			jPost('/admin/menu/createRootMenu', {'create' : '1'}, 
				function(data){
					if(data.success){
						$("#rootDiv").append(data.new);
						$('#rootDiv').removeClass('grid-view-loading');
					}
					else if(data.errors){
						showErrorMessage(data.errors);
					}
				},'json'
			);
		},

		// редактируем меню
		updateMenu : function(node){
			$.fn.tree("updateNode", node,{beforeOpen : function(data){
		   		$.each(data, function(index, value){
		   			var target = node.data('target');
	   				var divSelector = $("#"+index+"Div");
	   				var collapsable = $("#"+index+"Div").closest(".panel-collapse");
	   				if(target == '#items-form-modal'){
	   					if(jQuery.inArray(index, ['htmlOptions','linkLabelWrapperHtmlOptions', 'submenuHtmlOptions'])!==-1){
							divSelector = $("#"+index+"SecondDiv");
							collapsable = $("#"+index+"SecondDiv").closest(".panel-collapse");
	   					}
	   				}
		   			if($.type(value) == 'array' && value !== null){
   						divSelector.html('');
		   				$.each(value, function(i, v){
		   					divSelector.append('<div class="mb10 clearfix"><input class="w140 small fl" value="'+v.key+'" name="Menu['+index+']['+i+'][key]" type="text"><input value="'+v.value+'" class="w200 ml5 small fl" name="Menu['+index+']['+i+'][value]"  type="text"></div>');
		   				});
		   			}else if(index.toLowerCase().indexOf("option") >= 0){
		   				divSelector.html('');
		   				divSelector.append('<div class="mb10 clearfix"><input class="w140 small fl" name="Menu['+index+'][0][key]" type="text"><input class="w200 ml5 small fl" name="Menu['+index+'][0][value]"  type="text"></div>');
		   			}
   					
	   				collapsable.removeClass('in').addClass('collapse');
   					// collapsable.collapse('hide');
   					collapsable.closest('div.bootstrap').find('a[data-toggle="collapse"]').addClass('collapsed');

		   			if(index == 'active'){
			   			if(value == ''){
				   			$("#Menu_active").prop('readonly', true);
							$("#active").prop('checked', true);
						}
						else{
							$("#Menu_active").prop('readonly', false);
							$("#active").prop('checked', false);
						}
			   		}
			   		if(index == 'visible'){
						if(value == ''){
				   			$("#Menu_visible").prop('readonly', true);
							$("#visible").prop('checked', true);
						}
						else{
							$("#Menu_visible").prop('readonly', false);
							$("#visible").prop('checked', false);
						}
			   		}
		   		});
		   }});
		},

		// добавляем новое подменю
		addNode : function(options, node){
			var tree = $('div.jstree');

			if(node == undefined || node.length == 0) var node = tree.find('a.jstree-clicked');

			if(node.length == 0){
				showErrorMessage("Не выбран меню");
				return false;
			}
			var collapsable = $(".panel-collapse");
			collapsable.removeClass('in').addClass('collapse');
			collapsable.closest('div.bootstrap').find('a[data-toggle="collapse"]').addClass('collapsed');
			var itemOptions = ['itemOptions', 'htmlOptions', 'htmlOptionsSecond', 'linkLabelWrapperHtmlOptions', 'linkLabelWrapperHtmlOptionsSecond', 'submenuHtmlOptions', 'submenuHtmlOptionsSecond', 'submenuOptions', 'linkOptions'];
			$.each( itemOptions, function(k,v){
				if(v == 'htmlOptionsSecond'){
					var name = 'htmlOptions';
				}else if(v == 'linkLabelWrapperHtmlOptionsSecond'){
					var name = 'linkLabelWrapperHtmlOptions';
				}else if(v == 'submenuHtmlOptionsSecond'){
					var name = 'submenuHtmlOptions';
				}else{
					var name = v;
				}
				$("#"+name+"Div").html('');
				$("#"+name+"Div").append('<div class="mb10 clearfix"><input class="w140 small fl" name="Menu['+name+'][0][key]" type="text"><input class="w200 ml5 small fl" name="Menu['+name+'][0][value]"  type="text"></div>');
			});
			var idRootParent = $("#getGeneralRoot").val();
			$.fn.openModal({'active_checkbox' : 1, 'visible_checkbox' : 1, 'id_parent' : node.closest('li').data('id'), idRootParent : idRootParent}, options);
			$(options.target).find('input:text:first').focus();
		},
	};

	$.fn.menu = function(method)
	{
		var orderCommentsTimeout;

		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Метод ' + method + ' не существует');
			return false;
		}
	};


})( jQuery );

//events
$(function(){
	$.fn.menu('init');
	var options = ['itemOptions', 'htmlOptions', 'htmlOptionsSecond', 'linkLabelWrapperHtmlOptions', 'linkLabelWrapperHtmlOptionsSecond', 'submenuHtmlOptions', 'submenuHtmlOptionsSecond', 'submenuOptions', 'linkOptions'];
	$(document).
	// Отобразить дерево выбранного меню
	on('click', '.getMenu', function(){
		$("#getGeneralRoot").val($(this).data('id'));
		$.fn.menu('getInfo', $(this).closest('div'));
	})
	// Создать новое меню
	.on('click', '#createMenu', function(event){
		event.preventDefault();
		$.fn.menu('createMenu');
	})
	// visible checkbox change
	.on('change', '#visible', function(event){
		event.preventDefault();
		if($(this).is(':checked')){
			$("#Menu_visible").val('');
			$("#Menu_visible").prop('readonly', true);
		}
		else
			$("#Menu_visible").prop('readonly', false);
	})
	// active checkbox change
	.on('change', '#active', function(event){
		event.preventDefault();
		if($(this).is(':checked')){
			$("#Menu_active").val('');
			$("#Menu_active").prop('readonly', true);
		}
		else
			$("#Menu_active").prop('readonly', false);
	});

	// options add events
	$.each( options, function(k,v){
		var option = v;
		if(option == 'htmlOptionsSecond'){
			var name = 'htmlOptions';
		}else if(option == 'linkLabelWrapperHtmlOptionsSecond'){
			var name = 'linkLabelWrapperHtmlOptions';
		}else if(option == 'submenuHtmlOptionsSecond'){
			var name = 'submenuHtmlOptions';
		}else{
			var name = option;
		}
		$("#add"+option).on('click', function(){
			event.preventDefault();
			var counter = $("#"+option+"Div > div").length;
			$("#"+option+"Div").append('<div class="mb10 clearfix"><input class="w140 small fl" name="Menu['+name+']['+counter+'][key]"  type="text"><input class="w200 ml5 small fl" name="Menu['+name+']['+counter+'][value]"  type="text"></div>');
		});
	});
	
	// разделяем верхние и нижние меню
	$.each($("#MenuTree ul:first > li"), function( i, l ){
		$(l).attr('data-target', "#root-form-modal");
	});
});
