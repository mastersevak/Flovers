<?php

class m140208_192634_create_auth_tables extends EDbMigration
{
	public function up()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{auth_item}}")){
			$this->dropTable("{{auth_item}}");
		}

		$this->createTable("{{auth_item}}", array(
			"name"           => "varchar(255) CHARACTER SET UTF8 NOT NULL",
			"type"	         => "int not null",
			"description"    => "text CHARACTER SET UTF8",
			"bizrule"     	 => "text CHARACTER SET UTF8",
			"data"		     => "text CHARACTER SET UTF8",
			"PRIMARY KEY (name)"
			));

		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{auth_item_child}}")){
			$this->dropTable("{{auth_item_child}}");
		}

		$this->createTable("{{auth_item_child}}", array(
			"parent"  => "varchar(64) CHARACTER SET UTF8 NOT NULL",
			"child"	  => "varchar(64) CHARACTER SET UTF8 NOT NULL",
			"PRIMARY KEY (parent, child)",
			));

		// $this->addForeignKey("parent", "auth_item_child", "name", "{{auth_item}}");

		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{auth_assignment}}")){
			$this->dropTable("{{auth_assignment}}");
		}

		$this->createTable("{{auth_assignment}}", array(
			"itemname"  => "varchar(64) CHARACTER SET UTF8 NOT NULL",
			"userid"	=> "varchar(64) CHARACTER SET UTF8 NOT NULL",
			"bizrule"	=> "text CHARACTER SET UTF8",
			"data"	    => "text CHARACTER SET UTF8",
			"PRIMARY KEY (itemname, userid)"
			));

	}

	public function down()
	{
		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{auth_item}}")){
			$this->dropTable("{{auth_item}}");
		}

		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{auth_item_child}}")){
			$this->dropTable("{{auth_item_child}}");
		}

		//delete table if exists
		if(Yii::app()->db->getSchema()->getTable("{{auth_assignment}}")){
			$this->dropTable("{{auth_assignment}}");
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