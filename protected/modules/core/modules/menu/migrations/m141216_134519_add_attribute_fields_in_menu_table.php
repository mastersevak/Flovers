<?php

class m141216_134519_add_attribute_fields_in_menu_table extends EDbMigration
{
	public function safeUp()
	{
		$this->addColumn('{{menu}}', 'activateItems', 				'tinyint(1) 						AFTER `icon` ');
		$this->addColumn('{{menu}}', 'activateParents', 			'tinyint(1) 						AFTER `icon` ');
		$this->addColumn('{{menu}}', 'activeCssClass', 				'varchar(500) CHARACTER SET UTF8 	AFTER `icon` ');
		$this->addColumn('{{menu}}', 'encodeLabel', 				'tinyint(1) 						AFTER `icon` ');
		$this->addColumn('{{menu}}', 'htmlOptions', 				'varchar(500) CHARACTER SET UTF8	AFTER `icon` ');
		$this->addColumn('{{menu}}', 'itemCssClass', 				'varchar(500) CHARACTER SET UTF8	AFTER `icon` ');
		$this->addColumn('{{menu}}', 'itemTemplate', 				'varchar(500) CHARACTER SET UTF8	AFTER `icon` ');
		$this->addColumn('{{menu}}', 'linkLabelWrapper', 			'varchar(500) CHARACTER SET UTF8	AFTER `icon` ');
		$this->addColumn('{{menu}}', 'linkLabelWrapperHtmlOptions', 'varchar(500) CHARACTER SET UTF8	AFTER `icon` ');
		$this->addColumn('{{menu}}', 'submenuHtmlOptions', 			'varchar(500) CHARACTER SET UTF8	AFTER `icon` ');
	}

	public function safeDown()
	{
		$this->dropColumn('{{menu}}', 'activateItems');
		$this->dropColumn('{{menu}}', 'activateParents');
		$this->dropColumn('{{menu}}', 'activeCssClass');
		$this->dropColumn('{{menu}}', 'encodeLabel');
		$this->dropColumn('{{menu}}', 'htmlOptions');
		$this->dropColumn('{{menu}}', 'itemCssClass');
		$this->dropColumn('{{menu}}', 'itemTemplate');
		$this->dropColumn('{{menu}}', 'linkLabelWrapper');
		$this->dropColumn('{{menu}}', 'linkLabelWrapperHtmlOptions');
		$this->dropColumn('{{menu}}', 'submenuHtmlOptions');
	}
}