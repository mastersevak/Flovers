(function ( $ ) {
 
	$.fn.initTools = function( options ) {

		var settings = $.extend({
			crop : false,
			del : false,
			rotate: false,
			cropContainer: false
		}, options );

		return this.each(function() {

			var container = $(this);
			
			var cropContainer = settings.cropContainer ? 
								settings.cropContainer : 
								container.find('.crop-container');
			
			var cropBtn = container.find('.tools .crop-btn');
			var applyCropButton = cropContainer.find('.apply-crop');

			var cropUrl = container.data('crop-url');
			var deleteUrl = container.data('delete-url');
			var rotateUrl = container.data('rotate-url');
			
			var previewArea = false;

			var _index = 0;

			function init(){
				if(settings.crop) initCropEvent();

				if(settings.del) initDeleteEvent();

				if(settings.rotate) initRotateEvent();

				initSortEvent(container);
			}

			//initCropEvent
			function initCropEvent(){
				
				container.on('click', '.crop-btn', function(event){
					event.preventDefault();
					event.stopPropagation();
					self = $(this);
					
					jPost(cropUrl.replace('_id_', self.closest('li.image-container').data('id')), 
						{action:'init'}, function(data){
						
						if(data.success){
							//get images
							cropContainer.find('.sizes').html(data.small);

							var smallContainers = cropContainer.find('.sizes > div');
							var smallImages = cropContainer.find('.sizes > div img');
							var cropArea = cropContainer.find('.crop-area');
							
							cropArea.html('');

							for(i=0; i<data.sizes.length; i++){
								cropArea.append(data.big[i].image );
							}

							var bigContainers = cropArea.find('div.big');
							var bigImages = bigContainers.find('> img');

							//init apply crop
							initApplyCrop(cropContainer, bigContainers, smallContainers, cropUrl, self.closest('li.image-container'));
							
							//initSmallImages
							initSmallImages(smallImages, bigContainers, smallContainers);
							
							//показ больших картинок, и инициализация jcrop
							initBigImages(bigImages, cropContainer, smallContainers, data);

							fancyboxCrop(cropContainer, {
								beforeShow : function() {
									cropContainer.removeClass('hidden').find('.buttons').removeClass('hidden');
								}
							});
							
						}
						else{
							jAlert(data.message, "Ошибка");
						}
					}, 'json');
				});
			} //initCropEvent

			function initSmallImages(smallImages, bigContainers, smallContainers){
				//события при нажатии на маленькие картинки
				smallImages.on('click', function(){
					
					smallImages.removeClass('active');

					$(this).addClass('active');

					bigContainers.attr('style', 'width:0; height:0; overflow:hidden');

					index = $(this).parent().index();
						
					bigContainers.eq(index).removeAttr('style');

					previewArea = smallContainers.eq(index).find('img');
				});
			}
			//показ больших картинок, и инициализация jcrop
			function initBigImages(bigImages, cropContainer, smallContainers, data){
				bigImages.each(function(){
					
					$(this).load(function(){
						self = $(this);
						index = self.parent().index();
		
						var options;
						options = data.big[index].options;
						
						options.onChange = function(coords){
							showPreview(coords, cropContainer, previewArea);
						}

						options.onSelect = function(coords){ 
							changeCoords(coords, cropContainer, previewArea);
						}

						$(this).Jcrop(options);

						if(index == 0){
							window.setTimeout(function(){
								firstImage = smallContainers.first().find('img');
								firstImage.trigger('click');
								previewArea = firstImage;
								$.fancybox.reposition();
								$.fancybox.update();
							}, 1000);
						}
						
					});
				});
			}
			
			//нажатие на кнопку crop
			function initApplyCrop(cropContainer, bigContainers, smallContainers, url, element){
				applyCropButton.on('click', function(event){
					
					event.preventDefault();
					event.stopPropagation();

					$(this).unbind('click');

					button = $(this);

					var data = {};

					bigContainers.each(function(){
						index = $(this).index();

						var size = $(this).find('>img').data('size');
						coords = {
							'x': $(this).find('input#'+size+'_x').val(),
							'y': $(this).find('input#'+size+'_y').val(),
							'x2': $(this).find('input#'+size+'_x2').val(),
							'y2': $(this).find('input#'+size+'_y2').val(),
							'w': $(this).find('input#'+size+'_w').val(),
							'h': $(this).find('input#'+size+'_h').val(),
							'width': smallContainers.eq(index).find('img').data('output-width'),
							'height': smallContainers.eq(index).find('img').data('output-height'),
						};

						data[size] = coords;	
					});

					//процесс обрезки
					jPost(url.replace('_id_', element.data('id')), 
						{
							data: data,
							size: element.data('size'),
							action: 'crop'
						},
						function(data){
							if(data.success){
								element.find('.image').attr('src', data.src);
								//close fancybox
								$.fancybox.close();
							}
						},
						'json');
				});
			}

			//показ preview при изменении размеров crop
			function showPreview(coords, cropContainer){

				if(!previewArea){
					previewArea = cropContainer.find('.sizes div.big').eq(_index).find('img');
				}

				_index ++;

				var rx = previewArea.parent().width() / coords.w;
				var ry = previewArea.parent().height() / coords.h;

				index = previewArea.parent().index();
				big = cropContainer.find('.crop-area div.big:nth-child(' + (index + 1) + ') > img');

				if(previewArea.attr('src') != big.attr('src'))
					previewArea.attr('src', big.attr('src'));

				previewArea.css({
					width: Math.round(rx * big.width()) + 'px',
					height: Math.round(ry * big.height()) + 'px',
					marginLeft: '-' + Math.round(rx * coords.x) + 'px',
					marginTop: '-' + Math.round(ry * coords.y) + 'px'
				});
			}

			//сохранение новых координат, после их изменения
			function changeCoords(coords, cropContainer){
				
				index = previewArea.parent().index();
				div = cropContainer.find('.crop-area div.big').eq(index);

				size = div.find('>img').data('size');
				
				div.find('input#' + size + '_x').val(coords.x);
				div.find('input#' + size + '_y').val(coords.y);
				div.find('input#' + size + '_x2').val(coords.x2);
				div.find('input#' + size + '_y2').val(coords.y2);
				div.find('input#' + size + '_w').val(coords.w);
				div.find('input#' + size + '_h').val(coords.h);

			}

			//удаление аватарки
			function initDeleteEvent(){
				container.on('click', '.delete-btn', function(event){
					event.preventDefault();
					event.stopPropagation();
					self = $(this);

					jConfirm('Вы уверены, что хотите удалить данное изображение?',
						'Удалить изображение?', function(result){
							if(result)
								jPost(
									deleteUrl.replace('_id_', self.closest('li.image-container').data('id')),
									{},
									function(data){
										if(data.success){
											index = self.closest('li.image-container').index();
											self.closest('li.image-container').remove();

											$('.photos_list ul.photos li:eq('+index+')').remove();
										}
										
									},
									"json"
								);
					});
				});
			}

			//поворот
			function initRotateEvent(){

				container.on('click', '.rotate-left-btn', function(event){
					event.preventDefault();
					event.stopPropagation();
					self = $(this);

					jPost(
						rotateUrl.replace('_id_', self.closest('li.image-container').data('id')), 
						{
							size: self.closest('li.image-container').data('size'),
							side: 'left'
						}, 
						function(data){
							if(data.success)
								self.closest('li.image-container').find('.image').attr('src', data.src);
						},
						"json"
					);
				});

				container.on('click', '.rotate-right-btn', function(event){
					event.preventDefault();
					event.stopPropagation();
					self = $(this);

					jPost(
						rotateUrl.replace('_id_', self.closest('li.image-container').data('id')), 
						{
							size: self.closest('li.image-container').data('size'),
							side: 'right'
						}, 
						function(data){
							if(data.success)
								self.closest('li.image-container').find('.image').attr('src', data.src);
						
						},
						"json"
					);
				});
			}
			
			init();
		});

	}

})(jQuery);

//вывел отдельно, так только эта функция вызывается также после каждого upload
function initSortEvent(container){
	
	var list = container.find('.qq-uploader .qq-upload-list');

	list.sortable({handle: '.move-btn'})
		.bind('sortupdate', function(e){
		
		ids = [];
		$(this).find('>li').each(function(){
			ids.push($(this).data('id'));
		});

		//в случае с несколькими upload area, на одной странице, 
		//без этого не обойтись
		url = container.data('sort-url');

		list = $(this);
		list.sortable('disable');
		jPost(
			url,
			{
				'ids[]':ids
			},
			function(success){
				if(success) list.sortable('enable');
			}
		);
	});         
}

function fancyboxCrop(selector, params){
	$.fancybox.showLoading();

	options = $.extend({
		href:selector, 
		type: 'inline',
		autoCenter : true,
		autoHeight: true,
		closeBtn: false,
		openEffect: 'none',	
		wrapCSS: 'wrap-crop-container',
		helpers : {
			title : {type: 'outside'},
			overlay : {css : {'background' : 'rgba(0,0,0, 0.4)'} }
		},
	   
		beforeShow : function(){
			$.fancybox.hideLoading();
		}
	}, params);

	$.fancybox.open(options);
}