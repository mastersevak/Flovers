<div id="photos_grid_<?=$model->id?>" class="photos_tabs photos_grid main-container"
	data-crop-url="<?=Yii::app()->createUrl('/core/photo/default/crop', array('id'=>'_id_', 'model'=>'Photo'))?>"
    data-rotate-url="<?=Yii::app()->createUrl('/core/photo/default/rotate', array('id'=>'_id_', 'model'=>'Photo'))?>"
    data-delete-url="<?=Yii::app()->createUrl('/core/photo/default/delete', array('id'=>'_id_', 'model'=>'Photo'))?>"
    data-sort-url="<?=Yii::app()->createUrl('/core/photo/default/sorting', array('id'=>'_id_', 'model'=>'Photo'))?>"
>

<? 

$alias = 'application.modules.core.widgets.uploader.views';
$filelist = Yii::app()->controller->renderPartial("$alias._filelist", compact('files', 'bigSize'), true);

$onComplete = <<< script
js:function(id, name, response){ 
    
    var item = this.getItemByFileId(id);
    
    //удалить лишние элементы
    qq(this._find(item, 'cancel')).remove();
    qq(this._find(item, 'spinner')).remove();
    qq(this._find(item, 'size')).remove();
    
    //показ картинки
    imgUrl = response.path + response.filename;
    image = "<img class='image' src='" + imgUrl + "' >";
    a = $(item).find('.qq-upload-file');
    href = imgUrl.replace('/thumb/', '/'+a.data('big-size')+'/');
    a.attr('href', href).html(image);
    
    $(item).data('id', response.id);    

    //инициализация сортировки для даного элемента
    initSortEvent($(item).closest('.photos_grid'));

    //добавление элемента в список редактирования названия
    $('#photos_list_{$model->id} ul').prepend('<li><a href=\"' + href + '\" rel=\"gallery_small\" class=\"fancybox\"><img src=\"' + response.path + response.filename +'\" ></a><div><textarea data-id=\"' + response.id + '\" data-type=\"title\" placeholder=\"Title\"></textarea></div></li>');

}
script;

$this->widget('core.extensions.EFineUploader.EFineUploader',
    array(
       'id'=>'dropZone_'.$model->id,
       'css'=>$css,
       'config'=>array(
           'autoUpload'=>true,
           'multiple'=>true,
           'request'=>array(
                'endpointTemplate' => Yii::app()->createUrl('/core/photo/default/upload', array('modelName'=>get_class($model), 'modelId'=>"__id__", 'params'=>'__params__')),
                'endpoint'=> Yii::app()->createUrl('/core/photo/default/upload', array('modelName'=>get_class($model), 'modelId'=>$model->id, 'params'=>$params)),
                'params' => array('YII_CSRF_TOKEN'=>Yii::app()->request->csrfToken),
            ),
            'retry' =>array('enableAuto'=>true,'preventRetryResponseProperty'=>true),
            //'chunking' =>array('enable'=>true,'partSize'=>100),//bytes
            'callbacks'=>array(
                'onComplete'=>$onComplete,
                //'onError'=>"js:function(id, name, errorReason){ }",
            ),
            'validation'=> array(
                'sizeLimit' => param('upload_size_limit'),
                'allowedExtensions'=>param('upload_allowed_extensions')
            ),
            'template' => Yii::app()->controller->renderPartial("$alias._template", array('filelist' => $filelist), true),
            'fileTemplate' => Yii::app()->controller->renderPartial("$alias._filetemplate", compact('bigSize'), true),
            'disableCancelForFormUploads' => true,
            'text' => array(
                'uploadButton' => 'Загрузить файл',
                'cancelButton' => 'Отмена',
                'retryButton' => 'Повторить',
                'deleteButton' => 'Удалить',
                'failUpload' => 'Ошибка загрузки',
                'dragZone' => 'Перетащите картинки сюда',
                'dropProcessing' => 'Обработка файлов ...',
                'formatProgress' => "{percent}% из {total_size}",
                'waitingForResponse' => "Обработка..."
            ),
            'dragAndDrop' => array(
                'hideDropzones' => false
            ),
            /*'messages'=>array(
                'tooManyItemsError'=>'Too many items error',
                'typeError'=>"Файл {file} имеет неверное расширение. Разрешены файлы только с расширениями: {extensions}.",
                'sizeError'=>"Размер файла {file} велик, максимальный размер {sizeLimit}.",
                'minSizeError'=>"Размер файла {file} мал, минимальный размер {minSizeLimit}.",
                'emptyError'=>"{file} is empty, please select files again without it.",
                'onLeave'=>"The files are being uploaded, if you leave now the upload will be cancelled."
                            ),*/
          )
      ));
?>

</div>

<br clear="both">

<div id="crop_<?=get_class($model).'_photos_'.$model->id?>" class="crop-container hidden" >

	<div class="sizes clearfix"></div>
	<div class="crop-area"></div>
	<div class="buttons hidden">
		<a class="apply-crop btn btn_orange">
			<i class="icon-crop"></i>ОБРЕЗАТЬ</a>
	</div>
</div>