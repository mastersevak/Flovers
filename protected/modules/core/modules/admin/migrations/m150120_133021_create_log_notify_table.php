<?php

class m150120_133021_create_log_notify_table extends EDbMigration
{
	public function up(){
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{log_notify}}"))
			$this->dropTable("{{log_notify}}");

		$this->createTable("{{log_notify}}", array(
			"id"			=> "int UNSIGNED AUTO_INCREMENT",
			"created"		=> "datetime",
			"id_creator"	=> "int UNSIGNED",
			"changed"		=> "datetime",
			"id_changer"	=> "int UNSIGNED",
			"level"			=> "varchar(255) CHARACTER SET UTF8",
			"category"		=> "varchar(255) CHARACTER SET UTF8",
			"message"		=> "text CHARACTER SET UTF8",
			"PRIMARY KEY (id)"
			));
	}

	public function down(){
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{log_notify}}"))
			$this->dropTable("{{log_notify}}");
	}
}