<?php

class m150317_080605_add_indexes_in_menu_table extends EDbMigration
{
	public function up()
	{
		$transaction = Yii::app()->db->beginTransaction();

		try{
			$this->createIndex('lft', 'menu', 'lft');
			$this->createIndex('rgt', 'menu', 'rgt');
			$this->createIndex('level', 'menu', 'level');
			$this->createIndex('root', 'menu', 'root');

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
			$this->dropIndex('lft', 'menu', 'lft');
			$this->dropIndex('rgt', 'menu', 'rgt');
			$this->dropIndex('level', 'menu', 'level');
			$this->dropIndex('root', 'menu', 'root');

			$transaction->commit();
		}
		catch(Exception $e){
			$transaction->rollback();
		}
	}
}