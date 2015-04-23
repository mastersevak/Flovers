<?php

class m141222_120904_create_menu_lang_table extends EDbMigration
{
	public function up()
	{
		$transaction = Yii::app()->db->beginTransaction();

		try{
			if(Yii::app()->db->getSchema()->getTable("{{menu_lang}}")){
				$this->dropTable("{{menu_lang}}");
			}

			$this->createTable("{{menu_lang}}", array(
				"id" 				  => "int UNSIGNED AUTO_INCREMENT",
				"language"			  => "char(2) CHARACTER SET UTF8",
				"id_menu"         	  => "int UNSIGNED",
				"name"				  => "varchar(255)	CHARACTER SET UTF8",
				"PRIMARY KEY (id)",
				"KEY name  (name)",
				"KEY id_menu  (id_menu)",
				"KEY language (language)",
			));

			$menus = Menu::model()->findAll();

				
			foreach ($menus as $menu) {
				Yii::app()->db->createCommand()->insert('{{menu_lang}}',
					[
						'language'              =>	'ru',
						'name'                  =>  $menu->name,
						'id_menu'				=>	$menu->id,
					]
				);

				Yii::app()->db->createCommand()->insert('{{menu_lang}}',
					[
						'language'              =>	'en',
						'name'                  =>  $menu->name,
						'id_menu'				=>	$menu->id,
					]
				);
			}

			$this->dropColumn("{{menu}}", "name");
			$transaction->commit();
		}
		catch(Exception $e){
			$transaction->rollback();
		}
	}

	public function down()
	{
		$transaction = Yii::app()->db->beginTransaction();

		try{

			if(Yii::app()->db->getSchema()->getTable("{{menu}}")){
				$this->addColumn("{{menu}}", "name", "varchar(255)	CHARACTER SET UTF8");
			}

			$menus  = Yii::app()->db->createCommand()
				->select('name,id_menu')
				->from('menu_lang')
				->where('language = :language',[':language' => 'ru' ])
				->queryAll();

			foreach ($menus as $menu) {

				Yii::app()->db->createCommand()
					->update('{{menu}}', ['name' => $menu['name']], 'id=:id',[':id' => $menu['id_menu']]);

			}

			if(Yii::app()->db->getSchema()->getTable('menu_lang')){
				$this->dropTable('{{menu_lang}}');
			}

			$transaction->commit();
		}
		catch(Exception $e){
			$transaction->rollback();
		}

	}
}