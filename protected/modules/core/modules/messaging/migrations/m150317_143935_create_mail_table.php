<?php

class m150317_143935_create_mail_table extends EDbMigration
{
	public function up(){
		if(Yii::app()->db->getSchema()->getTable("{{mail}}")){
			$this->dropTable("{{mail}}");
		}

		$this->createTable("{{mail}}", array(
			"id"			=> "int UNSIGNED AUTO_INCREMENT",
			"created"		=> "datetime",
			"id_creator"	=> "int UNSIGNED",
			"changed"		=> "datetime",
			"id_changer"	=> "int UNSIGNED",
			"email"			=> "varchar(255) CHARACTER SET UTF8",
			"subject"		=> "varchar(255) CHARACTER SET UTF8",
			"message"		=> "text CHARACTER SET UTF8",
			"status"		=> "tinyint(1)",
			"PRIMARY KEY (id)",
			"KEY created  (created)",
			"KEY id_changer  (id_changer)",
			"KEY email (email)",
			"KEY subject (subject)"
		));
	}

	public function down(){
		if(Yii::app()->db->getSchema()->getTable("{{mail}}")){
			$this->dropTable("{{mail}}");
		}
	}
}