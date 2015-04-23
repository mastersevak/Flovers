<?php

class m150323_074916_add_error__column_in_mail_table extends EDbMigration
{
	public function up()
	{
		$this->addColumn('{{mail}}', 'error', 'text CHARACTER SET UTF8');
	}

	public function down()
	{
		$this->dropColumn('{{mail}}', 'error');
	}
}