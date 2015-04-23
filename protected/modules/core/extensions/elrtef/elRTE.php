<?php
class elRTE extends CInputWidget
{
    public $options = array();
    public $elfoptions = array();
    public $defaultOptions = array(
        'doctype'      => "js:'<!DOCTYPE html>'",
        'absoluteURLs' => true,
        'allowSource'  => true,
        'cssClass'     => 'el-rte',
        'lang'         => 'en',
        'width'        => '100%',
        'height'       => 400,
        'fmAllow'      => true, //if you want to use Media-manager
        'toolbar'      => 'complete', //tiny,compact,normal,complete,maxi,eldorado 
        'fmOpen'       => 'js:function(callback) {$("<div id=\'elfinder\' />").appendTo(\'body\').dialogelfinder(%elfopts%);}',//here used placeholder for settings
    );
    public $defaultElfoptions = array(
        'url' => '',
        'title' => 'File Manager',
        'dialog'               => array('width'=>'900','modal'=>true, 'title'=>'Media Manager'),
        'lang'                 => 'en',
        'dragUploadAllow'  => true, //Allow to drag and drop to upload files
        'width'        => '80%', //finder window width
        'commandsOptions' => array('getfile'=>array('onlyURL'=>true, 'oncomplete'=>'destroy')),
        'getFileCallback' => 'js: callback',
    );
    public $jui_elrte_css = "default";
    public $jui_elfinder_css = "default";


    public function run()
    {
        $this->options = CMap::mergeArray($this->defaultOptions, $this->options);
        //это добавил я
        $this->defaultElfoptions['url'] = bu().'protected/modules/core/extensions/elrtef/elfinder/php/connector.php'.
                                              '?root_url='.bu().
                                              '&root_path='.Yii::getPathOfAlias('webroot');

        $this->elfoptions = CMap::mergeArray($this->defaultElfoptions, $this->elfoptions);

        $cs=Yii::app()->clientScript;

        $dir = dirname(__FILE__).DIRECTORY_SEPARATOR;
        $baseUrl = Yii::app()->getAssetManager()->publish($dir, false, -1, YII_DEBUG).'/elrte';
        $baseUrlE = Yii::app()->getAssetManager()->publish($dir, false, -1, YII_DEBUG).'/elfinder';
        
        list($name, $id) = $this->resolveNameID();
        //set default id if not set
        if(isset($this->htmlOptions['id'])) $id=$this->htmlOptions['id'];
        else $this->htmlOptions['id']=$id;
        //set default name if not set
        if(isset($this->htmlOptions['name'])) $name=$this->htmlOptions['name'];
        else  $this->htmlOptions['name']=$name;

        $clientScript = Yii::app()->getClientScript();

        //$clientScript->registerCssFile($baseUrl.'/js/ui-themes/base/ui.all.css');
        if(!empty($this->jui_elrte_css))
        {
            if($this->jui_elrte_css == "default") //backward Compatibility
                $clientScript->registerCssFile($baseUrl.'/css/elrte.full.css');
            else 
                $clientScript->registerCssFile($baseUrl.'/css/'.$this->jui_elrte_css);
        } 
        else 
            $clientScript->registerCssFile($baseUrl.'/css/elrte.full.css');

        Yii::app()->clientScript->scriptMap = array(
            'jquery-ui.css' => false
        );

        $clientScript->registerCoreScript('jquery');
        $clientScript->registerPackage('migrate');
        $clientScript->registerCssFile($baseUrl.'/css/smoothness/jquery-ui-1.8.13.custom.css');
        $clientScript->registerScriptFile($baseUrl.'/js/elrte.full.js');

        // $clientScript->registerCoreScript('jquery.ui');

        // $clientScript->registerCssFile($baseUrl.'/js/codemirror/codemirror.css');
        // $clientScript->registerScriptFile($baseUrl.'/js/codemirror/codemirror.js', CClientScript::POS_HEAD);
        // $clientScript->registerScriptFile($baseUrl.'/js/codemirror/elrte.codehighlight.js', CClientScript::POS_END);

        //load custom js files
        $clientScript->registerScriptFile($baseUrl.'/js/ui/custom.js');

        if (isset($this->options['lang']))
            $clientScript->registerScriptFile($baseUrl.'/js/i18n/elrte.'.$this->options['lang'].'.js');
      
        if(!empty($this->options['cssfiles']))
        {
            $css_paths = array();
            
            foreach ($this->options['cssfiles'] as $cssf)
                $css_paths[] = $baseUrl.'/'.$cssf;
            
            $this->options['cssfiles'] = $css_paths;
        }

        //from here ELRTE FINDER
        $elfopts = "";
        if(!empty($this->options['fmAllow']) && $this->options['fmAllow'])
        {
            //$clientScript->registerCssFile($baseUrlE.'/js/ui-themes/base/ui.all.css');
            if(!empty($this->jui_elfinder_css))
            {
                if($this->jui_elfinder_css == "default") {//backward Compatibility
                    $clientScript->registerCssFile($baseUrlE.'/css/elfinder.min.css');
                    $clientScript->registerCssFile($baseUrlE.'/css/theme.css');
                }
                else 
                    $clientScript->registerCssFile($baseUrlE.'/css/'.$this->jui_elfinder_css);
            } 
            else{
                $clientScript->registerCssFile($baseUrlE.'/css/elfinder.min.css');
                $clientScript->registerCssFile($baseUrlE.'/css/theme.css');
            }
                


            $clientScript->registerScriptFile($baseUrlE.'/js/elfinder.full.js');

            //register language files for elrte aand elfinder
            // if (isset($this->options['lang']) && !isset($this->elfoptions['lang']))
            //     $clientScript->registerScriptFile($baseUrlE.'/js/i18n/elfinder.'.$this->options['lang'].'.js',CClientScript::POS_HEAD);
            // elseif(isset($this->elfoptions['lang']))
            //     $clientScript->registerScriptFile($baseUrlE.'/js/i18n/elfinder.'.$this->elfoptions['lang'].'.js',CClientScript::POS_HEAD);


            if(!empty($this->elfoptions))
            {
                
                if($this->elfoptions['url'] == 'auto') 
                    $this->elfoptions['url'] =  $baseUrlE.'/php/connector.php';
                if(!empty($this->elfoptions['passkey'])) 
                    $this->elfoptions['url'] .= '?passkey='.urlencode($this->elfoptions['passkey']) ;
            }
            $elfopts = CJavaScript::encode($this->elfoptions);
        }

        //to here!
        $optsenc = CJavaScript::encode($this->options);
        if(!empty($elfopts)) $optsenc = str_replace('%elfopts%', $elfopts, $optsenc);
        
        $js="var opts=";
        $js.= $optsenc;
        $js.=";";
        $js.="$('#".$id."').elrte(opts);";

        $cs->registerScript($id, $js, CClientScript::POS_READY);

        $value = $this->model->{$this->attribute};

        if(isset($this->htmlOptions['value'])) {
            $value = $this->htmlOptions['value'];
            unset($this->htmlOptions['value']);
        }

        echo CHtml::textArea($name, $value, ['id' => $id, "rows"=>"10", "cols"=>"40"]);
    }
}
?>
