/**
 * Используется на страницах
 * 
 * 1. Управление категориями
 * 2. Управление материалами
 * 
 */
(function($) {

	var methods = {
		init : function(options){		   
		},
		refreshTree : function(checkRoot, id){
			var wrapper = $('div.tree-wrapper');
			var tree = wrapper.find('div.jstree');
			var settings = tree.jstree('get_settings');
			methods.loader(true, tree.attr('id'));

			jPost('', {refresh: true, id: id}, function(data){
				if(data.success){
					wrapper.html(data.tree);
					if(checkRoot){
						$.each(wrapper.find("ul:first > li"), function( index, item ){
							$(item).attr('data-target', checkRoot);
						});
					}

					wrapper.find('div[data-tree]')
						.jstree(settings)
						.bind("move_node.jstree", function(event, data){
							methods.moveNode(event, data);
						});
					methods.loader(false, tree.attr('id'));
				}
			});
		},
		addNode : function(options, node){
			var tree = $('div.jstree');

			if(node == undefined || node.length == 0) var node = tree.find('a.jstree-clicked');

			if(node.length == 0){
				showErrorMessage("Не выбрана категория");
				return false;
			}
			var idRootParent = $("#getGeneralRoot").val();
			$.fn.openModal({'id_parent' : node.closest('li').data('id'), idRootParent : idRootParent}, options);

			$(options.target).find('input:text:first').focus();
		},
		updateNode : function(node, params){
			var tree = node.closest('div.jstree');
			var id = node.data('id');
			var idRootParent = $("#getGeneralRoot").val();
			jPost(tree.data('prepare-url'), {id: id, idRootParent: idRootParent}, function(data){
				if(data.success){
					var additional = {
						"target" : node.data('target'),
						"model"  : node.data('model'),
						"title"	 : node.data('title'),
						"action" : node.data('action')
					};
					if(params != undefined){
						$.extend(additional, params);
					}
					$.fn.openModal(data, additional);
					$(node.data('target')).find('input:text:first').select();
				}
			});
		},
		deleteNode: function(node){
			var id = node.data('id');
			var tree = node.closest('div.jstree');
			var idRootParent = $("#getGeneralRoot").val();
			jConfirm("Удалить ?", " ", function(result){
				if(result){
					jPost(tree.data('delete-url'), {id: id}, function(data){
						if(data.success){
							if(data.rootId){
								var rootDiv = $('#rootDiv');
								rootDiv.find('div[data-id="' + data.rootId + '"]').remove();
							}

							if(idRootParent) methods.refreshTree(idRootParent);
							else methods.refreshTree();
						}
					});
				}
			});
		},
		moveNode : function(event, data){
			var idParent = data.rslt.np.data('id');
			var firstNode = $(data.rslt.o[0]);
			var prevNode = firstNode.prev('li');
			var idPrevNode = 0;
			var nodes = data.rslt.o;
			var tree = firstNode.closest('div.jstree');

			var nodeIds = nodes.map(function(){
				return $(this).data("id");
			}).get();

			if(prevNode.length > 0) idPrevNode = prevNode.data('id');

			jPost(tree.data('move-url'), {
				nodeIds: nodeIds,
				idParent: idParent,
				idPrevNode: idPrevNode
			}, function(data){
				if(data.success){

				}
			});
		},

		loader : function (on, treeId){
			if(on == true)
				$('#' + treeId).addClass("grid-view-loading");
			else
				$('#' + treeId).removeClass("grid-view-loading");
		}
	};

	$.fn.tree = function(method)
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

	//events
	$(function(){
		$(document).bind("dblclick.jstree", 'div[data-tree]', function(event){
		   var node = $(event.target).closest('li');
		   
		});

		$('div[data-tree]').bind("move_node.jstree", function(event, data){
			methods.moveNode(event, data);
		});
	});

})( jQuery );