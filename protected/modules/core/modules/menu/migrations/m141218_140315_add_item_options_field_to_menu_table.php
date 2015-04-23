<?php

class m141218_140315_add_item_options_field_to_menu_table extends EDbMigration
{
	public function safeUp()
	{
		$this->addColumn('{{menu}}', 'itemOptions',	'varchar(500) CHARACTER SET UTF8	AFTER `icon` ');
	}

	public function safeDown()
	{
		$this->dropColumn('{{menu}}', 'itemOptions');
	}
}