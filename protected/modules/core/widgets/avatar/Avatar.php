<?php 


class Avatar extends SWidget
{
    public $id = 'avatar-container';
	public $thumbWidth = 120; //ширина выводимого изображения
	public $thumbHeight = 120; //высота выводимого изображения
	public $model, $field, $form;
    public $alt = '';
    public $size = 'thumb';
    public $hiddenFile = false;
    public $autoSave = false;
    public $hiddenLink = false;
    public $bigSize = 'big';

	public $image;

    public $assetsUrl;

    public $deleteUrl;

	public function init(){
		parent::init();

		$fileUploader = get_class($this->model) . '_' . $this->field;

		if(!$this->image){
			$this->image = 'thumbnail';
		}

        cs()->registerScriptFile($this->assetsUrl.'/avatar.js', CClientScript::POS_END);
    	cs()->registerScriptFile($this->assetsUrl.'/resample.js', CClientScript::POS_END);
        cs()->registerScriptFile($this->assetsUrl.'/tools.js', CClientScript::POS_END);

        cs()->registerScriptFile(app()->controller->rootAssetsUrl.'/plugins/jquery-jcrop/jquery.Jcrop.min.js', CCLientScript::POS_END);
        cs()->registerCssFile(app()->controller->rootAssetsUrl.'/plugins/jquery-jcrop/jquery.Jcrop.css');

        cs()->registerCssFile($this->assetsUrl.'/avatar.css');
        cs()->registerCssFile($this->assetsUrl.'/crop.css');
        
        $m = get_class($this->model);

        $autoSave = $this->autoSave ? 'true' : 'false';
        $script = <<< script
            $('#{$this->id}').avatar({autoSave: $autoSave})
                             .avatarTools({crop:true, del:true});

            //add fancybox to thumnails
            /*$("#{$this->id} a.fancybox").fancybox({
                padding: 5,
                nextEffect: 'fade', prevEffect: 'fade', 
                openEffect: 'fade', closeEffect: 'fade',
                helpers: {
                    title : {type: 'outside'},
                    overlay : {css : {'background' : 'rgba(0,0,0, 0.4)'} }
                }
            });*/
script;
        
    	cs()->registerScript('run_drag_image_'.$fileUploader, $script, CClientScript::POS_READY);
	}

    public function makeVars() {
        $thumbWidth    = $this->thumbWidth;
        $thumbHeight   = $this->thumbHeight;
        $model         = $this->model;
        $field         = $this->field;
        $form          = $this->form;
        $thumbID       = $this->image;
        $alt           = $this->alt;
        $size          = $this->size;
        $hiddenFile    = $this->hiddenFile;
        $hiddenLink    = $this->hiddenLink;
        $deleteUrl     = $this->controller->createUrl('/core/photo/default/deleteThumbnail', array('id'=>$model->id, 'model'=>get_class($model)));
        $cropUrl       = Yii::app()->createUrl('/core/photo/default/crop', array('id'=>$model->id, 'model'=>get_class($model)));
        $uploadUrl     = Yii::app()->createUrl('/core/photo/default/ajaxUpload', array('id'=>$model->id, 'model'=>get_class($model)));
        $autoSave      = $this->autoSave;
        $bigSize       = $this->bigSize;

        return compact('field', 'model', 'thumbWidth', 'thumbHeight', 'form', 
                        'thumbID', 'alt', 'size', 'hiddenFile', 'deleteUrl', 
                        'autoSave', 'cropUrl', 'uploadUrl', 'hiddenLink', 'bigSize');
        
    }

    public function run() {
    	
        $vars = $this->makeVars();

        $this->render('main', $vars);
    }
}