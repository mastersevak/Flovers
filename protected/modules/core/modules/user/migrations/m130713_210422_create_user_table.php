<?php

/**
 * @todo  
 * Переименовать колонки во время заливки
 * -----------------
 * login - username
 * pass - password
 * name - firstname
 * mail - email
 * name - firstname
 * last_name - lastname
 * middle_name - middlename
 * passport_gender - gender
 * 
 */


class m130713_210422_create_user_table extends EDbMigration
{
	public function up()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{user}}")){
			$this->dropTable("{{user}}");
		}

		$transaction = Yii::app()->db->beginTransaction();

		try{

			$this->createTable("{{user}}", array(
				"id"                  => "int UNSIGNED AUTO_INCREMENT",
				"created"             => "datetime",
				"id_creator"		  => "int UNSIGNED",
				"changed"             => "datetime",
				"id_changer"          => "int UNSIGNED",
				"activated"           => "int UNSIGNED",
				"last_visit"          => "int UNSIGNED",
				"status"              => "tinyint(1)",
				"username"            => "varchar(50)",
				"email"               => "varchar(50)",
				"password"            => "char(64)",
				"salt"            	  => "char(20)",
				"email_confirmed"     => "tinyint(1)",
				"registration_ip"     => "varchar(20)",
				"activation_ip"       => "varchar(20)",
				"avatar"              => "int UNSIGNED",
				"reset_key"           => "char(32)",
				"activation_key"      => "char(32)",
				"api_key"             => "char(32)",
				"hash"				  => "char(32)",
				"is_social_user"      => "varchar(50)",
				//перенесли из профиля
				"firstname"           => "varchar(30) CHARSET UTF8",
				"lastname"            => "varchar(30) CHARSET UTF8",
				"middlename"          => "varchar(30) CHARSET UTF8",
				
				//колонки которые остаются под вопросом
				"PRIMARY KEY (id)",
				"KEY firstname (firstname)", 
				"KEY lastname (lastname)", 
				"KEY middlename (middlename)", 
				"KEY name (firstname, lastname, middlename)", 
				"KEY status (status)", 
				"KEY username (username)", 
				"KEY email (email)", 
				"KEY is_social_user (is_social_user)", 
				"KEY changed (changed)", 
			));

			//insert default user for superadmin
			$this->insert("{{user}}", array(
				"id"                => 2,
				"created"           => date('Y-m-d H:i:s'),
				"changed"           => date('Y-m-d H:i:s'),
				"activated"         => date('Y-m-d H:i:s'),
				"status"            => User::STATUS_ACTIVE,
				"username"          => "amanukian",
				"email"             => "amanukian@mail.ru",
				"password"          => md5('master'.'6056'), //Password::hashPassword('master'),
				"salt"				=> "6056",
				"firstname"         => "Александр",
				"lastname"          => "Манукян",
				"middlename"        => "Амаякович",
				"email_confirmed"   => User::EMAIL_CONFIRM_YES,
				"registration_ip"   => "127.0.0.1",
				"activation_ip"     => "127.0.0.1",
				"reset_key"         => md5("LAOPret12"),
				"activation_key"    => md5("P[dssOk3z"),
				"api_key"           => md5("OUSn_pW1s"),
				"hash"        	    => md5("a0pn_p72xR"),
				"is_social_user"    => User::SOCIAL_USER_NO
				));
		
			$transaction->commit();
		}
		catch(Exception $e){
			$transaction->rollback();
			throw $e;
		}
	}

	public function down()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{user}}")){
			$this->dropTable("{{user}}");
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