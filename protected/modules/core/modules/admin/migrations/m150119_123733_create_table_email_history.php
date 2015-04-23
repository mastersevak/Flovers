<?php

class m150119_123733_create_table_email_history extends EDbMigration
{
	public function up(){
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{email_history}}"))
			$this->dropTable("{{email_history}}");

		$this->createTable("{{email_history}}", array(
			"id"			=> "int UNSIGNED AUTO_INCREMENT",
			"created"		=> "datetime",
			"id_creator"	=> "int UNSIGNED",
			"changed"		=> "datetime",
			"id_changer"	=> "int UNSIGNED",
			"to"			=> "varchar(255) CHARACTER SET UTF8",
			"subject"		=> "varchar(255) CHARACTER SET UTF8",
			"message"		=> "text CHARACTER SET UTF8",
			"status"		=> "int UNSIGNED",
			"PRIMARY KEY (id)"
			));
	}

	public function down(){
		if(Yii::app()->db->getSchema()->getTable("{{email_history}}"))
			$this->dropTable("{{email_history}}");
	}
}