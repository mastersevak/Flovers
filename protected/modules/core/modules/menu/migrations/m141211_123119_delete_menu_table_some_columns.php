<?php

class m141211_123119_delete_menu_table_some_columns extends EDbMigration
{
	public function up(){
		$this->dropColumn('menu', 'id_creator');
		$this->dropColumn('menu', 'created');
		$this->dropColumn('menu', 'id_changer');
		$this->dropColumn('menu', 'changed');
	}

	public function down(){
		$this->addColumn('menu', 'id_creator', 'int(10)');
		$this->addColumn('menu', 'created', 'datetime');
		$this->addColumn('menu', 'id_changer', 'int(10)');
		$this->addColumn('menu', 'changed', 'datetime');
	} 
}