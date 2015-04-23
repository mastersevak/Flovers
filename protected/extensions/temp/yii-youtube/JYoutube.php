<?php
/**
 * JYoutube class file.
 *
 * @author Stefan Volkmar <volkmar_yii@email.de>
 * @version 1.1
 * @license BSD
 */

/**
 * This widget encapsulates the jQuery youtube plugin for loading and handling
 * Youtube videos and images in an easy an simple way.
 * ({@link https://github.com/kilhage/jquery-youtube}).
 *
 * @author Stefan Volkmar <volkmar_yii@email.de>
 */

class JYoutube extends CWidget
{
	/**
	 * @var string the youtube id
	 */
	public $youtubeId;

	/**
	 * @var string the width of the youtube element
     * Defaults to 600 pixel
	 */
	public $width='600';

	/**
	 * @var string the heigth of the youtube element
     * Defaults to 500 pixel
	 */
	public $height='500';

	/**
	 * @var string the The type of element you want to get ('image','video')
	 */
	public $type='video';

	/**
	 * @var array the options of the plugin
     * Defaults to...
     * autohide: false,
     * autoplay: false,
     * disablekb: false,
     * enablejsapi: false,
     * hd: true,
     * showinfo: false,	
     * version: "4"
	 */
	public $options=array();

	/**
	 * @var boolean enable playing the video after clicking the thumbnail image
	 */

    public $enableImageClickEvent = false;
    
	/**
	 * @var array the HTML attributes that should be rendered in the link tag
	 */
	public $htmlOptions=array();

	/**
	 * Initializes the widget.
	 */
	public function init()
	{  
		$id=$this->getId();
		if (isset($this->htmlOptions['id']))
			$id = $this->htmlOptions['id'];
		else
			$this->htmlOptions['id']=$id;
        
      	$dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'assets';
      	$baseUrl = CHtml::asset($dir);

        $JsFile = (YII_DEBUG)
                ? "/js/jquery.youtube.js"
                : "/js/jquery.youtube.min.js";

        // prevent trouble
        if (isset($this->htmlOptions['alt']))
            unset($this->htmlOptions['alt']);

        $this->options['width']=$this->width;
        $this->options['height']=$this->height;
        // $this->options['modestbranding'] = 1; //for hide logo
        // $this->options['autohide'] = 1; 

        $options=($this->options===array())
                ? ''
                : ', '.CJavaScript::encode($this->options);

        if (strtolower($this->type)=='image'){
            if ($this->enableImageClickEvent){
                $jscript="jQuery('#$id').youtube('image'$options).click(function(){jQuery(this).youtube('video'$options);});";
            } else {
                $jscript="jQuery('#$id').youtube('image'$options);";
            }
        } else {
            $jscript="jQuery('#$id').youtube('".strtolower($this->type)."'$options);";
        }

  		Yii::app()->getClientScript()
            ->registerCoreScript('jquery')            
            ->registerScriptFile($baseUrl.$JsFile)
            ->registerScript(__CLASS__.'#'.$id ,$jscript);

        echo CHtml::link('', $this->youtubeId, $this->htmlOptions);
	}
}