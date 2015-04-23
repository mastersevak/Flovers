<?php

class m131208_155115_create_user_block_table extends EDbMigration
{
	public function up()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{user_block}}")){
			$this->dropTable("{{user_block}}");
		}

		$this->createTable("{{user_block}}", array(
			"id"         => "int UNSIGNED AUTO_INCREMENT",
			"id_user"	 => "int UNSIGNED",
			"username"   => "varchar(255)",
			"ip"     	 => "varchar(20)",
			"date"		 => "datetime DEFAULT 0",
			"PRIMARY KEY (id)",
			"KEY id_user (id_user)",
			"KEY username (username)"
			));
	}

	public function down()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{user_block}}")){
			$this->dropTable("{{user_block}}");
		}

	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}