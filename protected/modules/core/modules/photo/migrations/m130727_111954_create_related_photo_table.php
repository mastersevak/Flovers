<?php

class m130727_111954_create_related_photo_table extends EDbMigration
{
	public function up()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{related_photo}}")){
			$this->dropTable("{{related_photo}}");
		}

		$this->createTable("{{related_photo}}", array(
			"id"        => "int UNSIGNED AUTO_INCREMENT",
			"id_photo"  => "int UNSIGNED",
			"id_model"  => "int UNSIGNED",
			"model"     => "varchar(255) CHARACTER SET UTF8",
			"PRIMARY KEY (id)",
			"KEY id_photo (id_photo)",
			"KEY id_model (id_model)"
			));
	}

	public function down()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{related_photo}}")){
			$this->dropTable("{{related_photo}}");
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