<?php

class m130713_205344_create_photo_table extends EDbMigration
{
	public function up()
	{	
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{photo}}")){
			$this->dropTable("{{photo}}");
		}

		$this->createTable("{{photo}}", array(
			"id"          => "int UNSIGNED AUTO_INCREMENT",
			"created"     => "datetime",
			"id_creator"  => "int UNSIGNED",
			"changed"     => "datetime",
			"id_changer"  => "int UNSIGNED",
			"path"        => "varchar(255) CHARACTER SET UTF8",
			"filename"    => "varchar(255) CHARACTER SET UTF8",
			"sizes"       => "varchar(255) CHARACTER SET UTF8",
			"title"       => "varchar(255) CHARACTER SET UTF8",
			"pos"         => "int UNSIGNED",
			"status"      => "tinyint(1)",
			"PRIMARY KEY (id)"
			));
		
		//delete table if exists
		// if(Yii::app()->db->getSchema()->getTable("{{photo_lang}}")){
		// 	$this->dropTable("{{photo_lang}}");
		// }

		// $this->createTable("{{photo_lang}}", array(
		// 	"id"       => "int UNSIGNED AUTO_INCREMENT",
		// 	"language" => "char(2) CHARACTER SET UTF8",
		// 	"photo_id" => "int UNSIGNED",
		// 	"title"    => "varchar(255) CHARACTER SET UTF8",
		// 	"PRIMARY KEY (id)"
		// 	));
	}

	public function down()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{photo}}")){
			$this->dropTable("{{photo}}");
		}

		//delete table if exists
		// if(Yii::app()->db->getSchema()->getTable("{{photo_lang}}")){
		// 	$this->dropTable("{{photo_lang}}");
		// }
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