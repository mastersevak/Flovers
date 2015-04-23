<?php

/**
 * Used to set buttons to use Glyphicons instead of the defaults images.
 */
class SDropdownColumn extends StatusButtonColumn{

	public $statusButtonOptions=array('class'=>'dropdown btn no_bg');

	protected function initExtraButtons()
    {
        if(!$this->statusButtonUrl)
            $this->statusButtonUrl = str_replace("{status}", $this->action, 'Yii::app()->controller->createUrl("{status}",array("id"=>$data->primaryKey))');
        
         $this->statusButtonOptions['class'] .= ' _'.$this->name; //для того чтобы при использовании нескольких колонок, не путались их события

        foreach(array('status') as $id)
        {
            $button=array(
                'id'=>$id,
                'label'=>$this->{$id.'ButtonLabel'},
                'url'=>$this->statusButtonUrl,
                'options'=>$this->{$id.'ButtonOptions'},
            );
            if(isset($this->buttons[$id]))
                $this->buttons[$id]=array_merge($button, $this->buttons[$id]);
            else
                $this->buttons[$id]=$button;
        }

        if(!isset($this->buttons['status']['click']))
        {

            if(Yii::app()->request->enableCsrfValidation)
            {
                $csrfTokenName = Yii::app()->request->csrfTokenName;
                $csrfToken = Yii::app()->request->csrfToken;
                $csrf = "\n\t\tdata:{ '$csrfTokenName':'$csrfToken' },";
            }
            else
                $csrf = '';

            if($this->afterStatus===null)
                $this->afterStatus='function(){}';

            $statuslabels = json_encode($this->filter);

            $this->buttons['status']['click']=<<<EOD
function(e) {
    e.preventDefault();
    popup = $('<ul class="changeStatus" />');
    labels = $statuslabels;

    button = $(this);
    button.parent().append(popup);
    
    for(label in labels){
        url = button.attr('href') + '?val=' + label;
        link = $('<a href="'+url+'">'+labels[label]+'</a>');
        link.click(function(e){
            e.preventDefault();
            var th=this;
            var afterStatus=$this->afterStatus;

            $.fn.yiiGridView.update('{$this->grid->id}', {
                type:'POST',
                data: {fieldName: '{$this->name}'},
                url: $(this).attr('href'),$csrf
                success:function(data) {
                    $.fn.yiiGridView.update('{$this->grid->id}');
                    afterStatus(th,true,data);
                },
                error:function(XHR) {
                    return afterStatus(th,false,XHR);
                }
            });
           
            
        });
        li = $('<li></li>');
        li.append(link).appendTo(popup);
        
    };
    overlay = $('<div class="status-overlay" />');

    $("body").append(overlay);
    $("body").delegate(overlay, 'click', function(){
        popup.remove();
        overlay.remove();
    });
    
}
EOD;
        }
    }

    protected function renderDataCellContent($row,$data)
    {
        $tr=array();
        ob_start();
        foreach($this->buttons as $id=>$button)
        {
            $this->renderButton($id,$button,$row,$data);
            $tr['{'.$id.'}']='<div class="posrel">'.ob_get_contents().'</div>';
            ob_clean();
        }
        ob_end_clean();
        echo strtr($this->template,$tr);
    }

}

