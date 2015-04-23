<?php

class m140209_094304_create_rights_table extends EDbMigration
{
	public function up()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{rights}}")){
			$this->dropTable("authItem");
		}

		$this->createTable("{{rights}}", array(
			"itemname"   => "varchar(64) CHARACTER SET UTF8 NOT NULL",
			"type"	     => "int not null",
			"weight"     => "int not null",
			"PRIMARY KEY (itemname)"
			));
	}

	public function down()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{rights}}")){
			$this->dropTable("{{rights}}");
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
