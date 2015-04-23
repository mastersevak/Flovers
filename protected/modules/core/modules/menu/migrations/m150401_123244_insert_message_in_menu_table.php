<?php

class m150401_123244_insert_message_in_menu_table extends EDbMigration
{
	public function up()
	{
		$firstChild = Menu::model()->findByAttributes(['slug' => 'menyu-nastroyki']);

		if($firstChild && !Menu::model()->findByAttributes(['slug' => 'soobshchenie'])){
			$child = new Menu;
			$child->name 							= 'Сообщение';
			$child->url 							= '/core/messaging/logs';
			$child->slug 							= 'soobshchenie';
			$child->visible 						= '1';
			$child->active 							= '1';
			$child->appendTo($firstChild, false);
		}

		return true;
		
	}

	public function down()
	{
		$model = Menu::model()->findByAttributes(['slug' => 'soobshchenie']);
		if($model){
			$model->deleteNode();
		}
	}
}