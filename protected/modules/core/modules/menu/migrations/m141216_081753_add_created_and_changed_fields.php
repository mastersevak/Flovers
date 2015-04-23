<?php

class m141216_081753_add_created_and_changed_fields extends EDbMigration
{
	public function safeUp()
	{
		$this->addColumn('{{menu}}', 'id_creator', 'int(10)');
		$this->addColumn('{{menu}}', 'created', 'datetime');
		$this->addColumn('{{menu}}', 'id_changer', 'int(10)');
		$this->addColumn('{{menu}}', 'changed', 'datetime');
	}

	public function safeDown()
	{
		$this->dropColumn('{{menu}}', 'id_creator');
		$this->dropColumn('{{menu}}', 'created');
		$this->dropColumn('{{menu}}', 'id_changer');
		$this->dropColumn('{{menu}}', 'changed');
	}
}