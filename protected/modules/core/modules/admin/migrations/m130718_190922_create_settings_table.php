<?php

class m130718_190922_create_settings_table extends EDbMigration
{
	public function up()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{settings}}")){
			$this->dropTable("{{settings}}");
		}

		$this->createTable("{{settings}}", array(
			"id"               => "int UNSIGNED AUTO_INCREMENT",
			"created"          => "datetime",
			"id_creator"       => "int UNSIGNED",
			"changed"          => "datetime",
			"id_changer"       => "int UNSIGNED",
			"title"            => "varchar(255) CHARACTER SET UTF8",
			"value"            => "varchar(255) CHARACTER SET UTF8",
			"code"             => "varchar(255) CHARACTER SET UTF8",
			"pos"              => "int UNSIGNED",
			"category"         => "varchar(255) CHARACTER SET UTF8",
			"PRIMARY KEY (id)",
			"KEY code (code)",
			"KEY category (category)",
			"KEY value (value)"
			));

		//вставка значений по умолчанию
		$this->execute(
			"INSERT INTO `{{settings}}`(id, title, value, code, pos, category) VALUES 
			('1', 'Название сайта', 'Маркет.рф', 'appname', '1', 'Core'), 
			('2', 'Эл. почта администратора', 'Маркет.рф <admin@маркет.рф>', 'adminEmail', '2', 'Contacts'), 
			('3', 'Эл. почта для уведомлений', 'Маркет.рф <noreply@маркет.рф>', 'notifyEmail', '3', 'Contacts'), 
			('4', 'Эл. почта модератора', 'Маркет.рф <info@маркет.рф>', 'infoEmail', '4', 'Contacts'), 
			('5', 'Название сайта в шапке', 'Маркет<span class=\"semi-bold\">.РФ</span>', 'backendHeaderSiteName', '5', 'Core'),
			('6', 'Статей на странице', '5', 'front-articles-page-size', '6', 'Articles')"
		);

	}

	public function down()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{settings}}")){
			$this->dropTable("{{settings}}");
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