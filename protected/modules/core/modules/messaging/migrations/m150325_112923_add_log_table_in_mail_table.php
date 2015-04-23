<?php

class m150325_112923_add_log_table_in_mail_table extends EDbMigration
{
	public function up()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{log}}")){
			$this->dropTable("{{log}}");
		}

		$this->createTable("{{log}}", array(
			"id"			=> "int UNSIGNED AUTO_INCREMENT",
			"level"			=> "varchar(128) CHARACTER SET UTF8",
			"category"		=> "varchar(128) CHARACTER SET UTF8",
			"logtime"		=> "integer",
			"message"		=> "text CHARACTER SET UTF8",
			"PRIMARY KEY (id)",
			));
	}

	public function down()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{log}}")){
			$this->dropTable("{{log}}");
		}
	}
}