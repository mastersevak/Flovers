<?php

class m141219_075607_add_fields_for_items_in_menu_table extends EDbMigration
{
	public function safeUp()
	{
		$this->addColumn('{{menu}}', 'submenuOptions',	'varchar(500) CHARACTER SET UTF8 AFTER `icon` ');
		$this->addColumn('{{menu}}', 'visible',	'tinyint(1) AFTER `icon` ');
		$this->addColumn('{{menu}}', 'active',	'tinyint(1) AFTER `icon` ');
		$this->addColumn('{{menu}}', 'linkOptions',	'varchar(500) CHARACTER SET UTF8 AFTER `icon` ');
	}

	public function safeDown()
	{
		$this->dropColumn('{{menu}}', 'submenuOptions');
		$this->dropColumn('{{menu}}', 'visible');
		$this->dropColumn('{{menu}}', 'active');
		$this->dropColumn('{{menu}}', 'linkOptions');
	}
}