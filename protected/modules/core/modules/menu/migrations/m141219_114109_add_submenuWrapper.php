<?php

class m141219_114109_add_submenuWrapper extends EDbMigration
{
	public function safeUp()
	{
		$this->addColumn('{{menu}}', 'submenuWrapper',	'varchar(500) CHARACTER SET UTF8 AFTER `icon` ');
	}

	public function safeDown()
	{
		$this->dropColumn('{{menu}}', 'submenuWrapper');
	}
}