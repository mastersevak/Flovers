<?php

class m130728_091302_create_shortcut_table extends EDbMigration
{
	public function up()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{shortcut}}")){
			$this->dropTable("{{shortcut}}");
		}

		$this->createTable("{{shortcut}}", array(
			"id"         => "int UNSIGNED AUTO_INCREMENT",
			"id_object"  => "int UNSIGNED",
			"shortcode"  => "int(5) UNSIGNED",
			"pos"		 => "int UNSIGNED",
			"PRIMARY KEY (id)",
			"KEY id_object (id_object)",
			"KEY shortcode (shortcode)"
			));
	}

	public function down()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{shortcut}}")){
			$this->dropTable("{{shortcut}}");
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