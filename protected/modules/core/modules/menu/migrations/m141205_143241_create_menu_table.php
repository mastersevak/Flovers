<?php

class m141205_143241_create_menu_table extends EDbMigration
{
	public function up()
	{
		if(Yii::app()->db->getSchema()->getTable("{{menu}}")){
			$this->dropTable("{{menu}}");
		}

		$this->createTable("{{menu}}", array(
			"id"					=> "int AUTO_INCREMENT",
			"lft"				    => "int(10)",
			"rgt"			        => "int(10)",
			"level"		            => "smallint(6)",
			"slug"			        => "varchar(255)",
			"url"				    => "varchar(255)",
			"root"			        => "smallint(6)",
			"name"					=> "varchar(255)",
			"icon"					=> "varchar(255)",
			"enabled"				=> "tinyint(1)",
			"id_creator"			=> "int(10)",
			"created"				=> "datetime",
			"id_changer"			=> "int(10)",
			"changed"				=> "datetime",
			"PRIMARY KEY (id)"
		));
	}

	public function down()
	{
		if(Yii::app()->db->getSchema()->getTable("{{menu}}")){
			$this->dropTable("{{menu}}");
		}
	}

}