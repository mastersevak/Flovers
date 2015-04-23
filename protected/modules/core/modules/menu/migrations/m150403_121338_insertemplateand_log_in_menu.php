<?php

class m150403_121338_insertemplateand_log_in_menu extends EDbMigration
{
	public function up()
	{
		$firstChild = Menu::model()->findByAttributes(['slug' => 'menyu-nastroyki']);

		if(!Menu::model()->findByAttributes(['slug' => 'logi'])){
			$child = new Menu;
			$child->name 							= 'Логи';
			$child->url 							= '/core/messaging/logs/index';
			$child->slug 							= 'logi';
			$child->appendTo($firstChild, false);
		}

		if(!Menu::model()->findByAttributes(['slug' => 'shabloni-soobshcheniya'])){
			$child = new Menu;
			$child->name 							= 'Шаблоны сообщений';
			$child->url 							= '/core/messaging/templates/index';
			$child->slug 							= 'shabloni-soobshcheniya';
			$child->appendTo($firstChild, false);
		}

		if($model = Menu::model()->findByAttributes(['slug' => 'soobshchenie'])){
			$model->deleteNode();
		}

		return true;
	}

	public function down()
	{
		if($model = Menu::model()->findByAttributes(['slug' => 'shabloni-soobshcheniya'])){
			$model->deleteNode();
		}

		if($model = Menu::model()->findByAttributes(['slug' => 'logi'])){
			$model->deleteNode();
		}

		$firstChild = Menu::model()->findByAttributes(['slug' => 'menyu-nastroyki']);

		if(!Menu::model()->findByAttributes(['slug' => 'soobshchenie'])){
			$child = new Menu;
			$child->name 							= 'Сообщение';
			$child->url 							= '/core/messaging/logs';
			$child->slug 							= 'soobshchenie';
			$child->visible 						= '1';
			$child->active 							= '1';
			$child->appendTo($firstChild, false);
		}
	}
}