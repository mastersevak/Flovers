var DoAction = {

	onChange : function(element){
		var self = $(this);

		selOption = $(element).find('option:selected');
		url = selOption.data('action');
		action = $(element).val();

		gridId = UIButtons.findGrid(element);

		//проверка выборки
		selected = $.fn.yiiGridView.getChecked(gridId, 'checked_rows');

		if( selected == ''){ //если ничего не выбрано
			var text = Yii.t('admin', 'Вы должны выбрать элементы перед действием!');
			$.jGrowl(text, { life: 2000, position: "customtop-right"});
			playNotificationSound();

			$(element).val('').trigger('refresh');
		}
		else {
			
			jConfirm(Yii.t('admin', 'Are you sure you want to') + ' ' + 
				Yii.t('admin', selOption.text()).toLowerCase() + '?', 
				Yii.t('admin', selOption.text()), function(r) {
				if(r){

					items  = selected.join(',');
					
					$.post(url, {items: items},
						function(){
							if(selOption.data('update-grid') != undefined){
								ids = selOption.data('update-grid').split(', ');

								$.each(ids, function(key, value){
									$.fn.yiiGridView.update(value);
								});
							}
							else
								$.fn.yiiGridView.update(gridId);

							$(element).val('').trigger('refresh');
						});
				}
				else{
					$(element).val('').trigger('refresh');
				}
			});
		}

	}

	
};
