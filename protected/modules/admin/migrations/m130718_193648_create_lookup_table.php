<?php

class m130718_193648_create_lookup_table extends EDbMigration
{
	public function up()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{lookup}}")){
			$this->dropTable("{{lookup}}");
		}

		$this->createTable("{{lookup}}", array(
			"id"       => "int UNSIGNED AUTO_INCREMENT",
			"code"     => "varchar(255) CHARACTER SET UTF8",
			"type"     => "varchar(255) CHARACTER SET UTF8",
			"pos"      => "int UNSIGNED",
			"PRIMARY KEY (id)"
			));

		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{lookup_lang}}")){
			$this->dropTable("{{lookup_lang}}");
		}

		$this->createTable("{{lookup_lang}}", array(
			"id"        => "int UNSIGNED AUTO_INCREMENT",
			"language"  => "char(2) CHARACTER SET UTF8",
			"lookup_id" => "int UNSIGNED",
			"name"      => "varchar(255) CHARACTER SET UTF8",
			"PRIMARY KEY (id)"
			));

		//вставка значений по умолчанию
		$this->execute(
			"INSERT INTO `{{lookup}}` VALUES
			('5', '1', 'UserSocial', '8'), 
			('6', '0', 'UserSocial', '9'), 
			('7', '2', 'UserGender', '10'), 
			('8', '1', 'UserGender', '11'), 
			('9', '1', 'StandartStatus', '12'), 
			('10', '0', 'StandartStatus', '13'),
			('11', '0', 'CommentStatus', '14'),
			('12', '1', 'CommentStatus', '15'),
			('13', '2', 'CommentStatus', '16'),
			('14', '1', 'YesNo', '0'),
			('15', '0', 'YesNo', '1');"
		);

		$this->execute(
			"INSERT INTO `{{lookup_lang}}` VALUES 
			('13', 'ru', '5', 'Да'), 
			('14', 'en', '5', 'Yes'), 
			('16', 'ru', '6', 'Нет'), 
			('17', 'en', '6', 'No'), 
			('19', 'ru', '7', 'Женский'), 
			('20', 'en', '7', 'Female'), 
			('22', 'ru', '8', 'Мужской'), 
			('23', 'en', '8', 'Male'), 
			('25', 'ru', '9', 'Активный'), 
			('26', 'en', '9', 'Active'), 
			('28', 'ru', '10', 'Не активный'), 
			('29', 'en', '10', 'Inactive'),
			('31', 'ru', '11', 'Отказано'),
			('32', 'en', '11', 'Declined'),
			('34', 'ru', '12', 'Принято'),
			('35', 'en', '12', 'Accepted'),
			('37', 'ru', '13', 'Новый'),
			('38', 'en', '13', 'New'),
			('40', 'ru', '14', 'Да'),
			('41', 'en', '14', 'Yes'),
			('43', 'ru', '15', 'Нет'),
			('44', 'en', '15', 'No');"
		);


	}

	public function down()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{lookup}}")){
			$this->dropTable("{{lookup}}");
		}

		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{lookup_lang}}")){
			$this->dropTable("{{lookup_lang}}");
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