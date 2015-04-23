<?
$this->beginWidget('application.extensions.flash.EJqueryFlash',
                   array(
                         'name'=>'flash1',
                         'htmlOptions'=>array('src'=>'http://jquery.lukelutman.com/plugins/flash/example.swf'),
                        )
                  );
?>

<!-- You need Flash Player. -->
<? $this->endWidget('application.extensions.flash.EJqueryFlash');  ?>
 
<?
$this->widget('application.extensions.flash.EJqueryFlash',
               array(
                     'name'=>'flash2',
                        'htmlOptions'=>array(
                                             'src'=>'http://jquery.lukelutman.com/plugins/flash/example.swf', 
                                             'text'=>'You need Flash Player'),
                        )
                  );
?>