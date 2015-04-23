<?php

class m150311_115551_create_right_menu_migration extends EDbMigration
{
	public function up()
	{
		if($menu = Menu::model()->findByAttributes(array('slug' => 'administrator')))
			$menu->deleteNode();
		
		$menu = new Menu;
		$menu->name 								= 'Администратор';
		$menu->slug 								= 'administrator';
		$menu->submenuWrapper 						= 'div';
		$menu->htmlOptions 							= '[{"key":"class","value":"menu menu-horizontal nav quick-section"}]';
		$menu->saveNode(false);

		$firstChild = new Menu;
		$firstChild->name 							= 'Меню настройки';
		$firstChild->slug 							= 'menyu-nastroyki';
		$firstChild->linkOptions 					= '[{"key":"class","value":"fa fa-gear"}]';
		$firstChild->linkLabelWrapper  				= 'div';
		$firstChild->linkLabelWrapperHtmlOptions	= '[{"key":"class","value":"hidden"}]';
		$firstChild->appendTo($menu, false);

		$menuList = [
			'Настройки' => '/core/admin/settings/index', 
			'Справочник' => '/core/admin/lookup/index', 
			'Пользователи' => '/staff/back/index', 
			'Права доступа' => '/core/rights/assignment/view',
			'Сообщение'		=> '/core/notify/default/index/index', 
			'Меню' => '/core/menu/back/index'
		];

		foreach($menuList as $k => $v){
			$child = new Menu;
			$child->name 							= $k;
			$child->url 							= $v;
			$child->appendTo($firstChild, false);
		}
	}

	public function down()
	{
		$menu = Menu::model()->findByAttributes(array('slug' => 'administrator'));
		$menu->deleteNode();
	}
}