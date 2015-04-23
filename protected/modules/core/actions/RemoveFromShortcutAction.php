<?php 


/**
* RemoveFromShortcutAction - action для удаления объекта из таблицы shortcut
*/
class RemoveFromShortcutAction extends CAction
{
	
	public function run(){

        if(!isset($_POST['items'])) exception(404);
        $items = $_POST['items'];
        $ids = explode(',', $items);
        
        foreach ($ids as $id)
        {
            $model = Shortcut::model()->find('shortcode =:shortcut and object_id =:id ', 
                            array(':shortcut'=>$this->controller->shortcutCode, ':id'=>$id));
            if($model)
                $model->delete();
        }
	}

}