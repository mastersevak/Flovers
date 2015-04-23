<?php 

class Uploader extends SWidget
{
	public $files = array();
	public $model;
	public $params;
    public $bigSize = 'big';
    public $showTypeLink = true;

    public function run() {

        $files = $this->files;
        $model = $this->model;
        $params = $this->params;
        $bigSize = $this->bigSize;
        $showTypeLink = $this->showTypeLink;

    	cs()->registerScriptFile($this->assetsUrl.'/js/tools.js', CClientScript::POS_END);
    	cs()->registerScriptFile($this->assetsUrl.'/js/uploader.js', CClientScript::POS_END);
    	
    	cs()->registerCssFile($this->assetsUrl.'/css/uploader.css');
    	cs()->registerCssFile($this->assetsUrl.'/css/crop.css');

    	//jcrop
    	cs()->registerScriptFile(app()->controller->rootAssetsUrl.'/plugins/jquery-jcrop/jquery.Jcrop.min.js', CCLientScript::POS_END);
		cs()->registerCssFile(app()->controller->rootAssetsUrl.'/plugins/jquery-jcrop/jquery.Jcrop.css');

		/**
    	 * register FANCYBOX
    	 */
    	// $fancy = app()->controller->rootAssetsUrl.'/js/plugins/fancybox';
    	//Add mousewheel plugin (this is optional)
    	// cs()->registerScriptFile($fancy.'/jquery.mousewheel-3.0.6.pack.js', CCLientScript::POS_END);
    	//Add fancyBox
    	// cs()->registerScriptFile($fancy.'/jquery.fancybox.pack.js', CCLientScript::POS_END);
		// cs()->registerCssFile($fancy.'/jquery.fancybox.css');
		// cs()->registerCssFile($fancy.'/custom.css');

		//sortable
		cs()->registerScriptFile($this->assetsUrl.'/js/jquery.sortable.min.js', CClientScript::POS_END);

    	$id = $model->id;
		$mName = get_class($model);
		$script = <<< script
		setTimeout(function(){ 
            $("#photos_grid_{$model->id}").initTools({
				del: true, rotate: true, crop: true, cropContainer: $('#crop_{$mName}_photos_{$id}')
			});

            //add fancybox to thumnails
            if($.fn.fancybox != undefined){
                $(".photos_tabs a.fancybox").fancybox({
                    padding: 5,
                    nextEffect: 'fade', prevEffect: 'fade', 
                    openEffect: 'fade', closeEffect: 'fade',
                    helpers: {
                        title : {type: 'outside'},
                        overlay : {css : {'background' : 'rgba(0,0,0, 0.4)'} }
                    }
                });
            }
                

        }, 1000);
script;
		
		cs()->registerScript('uploader_tools', $script, CClientScript::POS_END);

		$css = $this->assetsUrl.'/css/custom_multiple.css';

        $this->render('uploader', compact('files', 'model', 'params', 'css', 'bigSize', 'showTypeLink'));
    }
}