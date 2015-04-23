<?php 


/**
* AddToShortcutAction - action для добавления объекта в таблицу shortcut
*/
class AddToShortcutAction extends CAction
{
	
	public function run(){

        if(!isset($_POST['items'])) exception(404);
        $ids = explode(',', $_POST['items']);
        $shortcode = $this->controller->shortcutCode;
        

        //get all shortcuts
        $all = Shortcut::model()->queryAll('shortcode =:shortcut', 
                            [':shortcut'=>$shortcode], ['object_id']);
        
        //найти те id, из ids которых нет в all
        $ids = array_diff($ids, $all); 

        foreach ($ids as $id)
        {
    		$model = new Shortcut;
    		$model->object_id = $id; 
    		$model->shortcode = $shortcode;
    		$model->save();
        }
	}

}