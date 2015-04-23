<?php

class m141028_181843_create_sms_table extends EDbMigration
{
	public function up(){
		if(Yii::app()->db->getSchema()->getTable("{{sms}}")){
			$this->dropTable("{{sms}}");
		}

		$this->createTable("{{sms}}", array(
			"id"					=> "int AUTO_INCREMENT",
			"created"				=> "datetime",
			"id_creator"			=> "int UNSIGNED",
			"changed"				=> "datetime",
			"id_changer"			=> "int UNSIGNED",
			"id_message"			=> "int UNSIGNED",
			"phone"					=> "varchar(50)",
			"body"					=> "text",
			"queue_status"			=> "tinyint(1)",
			"id_error"				=> "int(10)",
			"PRIMARY KEY (id)"
		));
	}

	public function down(){
		if(Yii::app()->db->getSchema()->getTable("{{sms}}")){
			$this->dropTable("{{sms}}");
		}
	}
}