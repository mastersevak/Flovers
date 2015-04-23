<nav class="menu-top" >

	{widget name="SMenu" 
			submenuWrapper="div"
			htmlOptions=["class"=>"menu menu-horizontal nav quick-section"]
			items=[
				[
					'label' => "",
					'url' => "#",
					'linkOptions' => ["class"=>"fa fa-clipboard", "data-original-title"=>"Контент"],
					'items' => [
						[
							'label'   => 'Product',
							'url'   => ['/hends/product/index'],
							'active'=> $this->uniqueId == '/hends/product/'
						],
						[
							'label'   => 'Product Collection',
							'url'   => ['/hends/collection/index'],
							'active'=> $this->uniqueId == '/hends/collection/'
						],
						[
							'label'   => 'Product Category',
							'url'   => ['/hends/category/index'],
							'active'=> $this->uniqueId == '/hends/category/'
						],
						[
							'label'   => 'Product Material',
							'url'   => ['/hends/material/index'],
							'active'=> $this->uniqueId == '/hends/material/'
						],
						[
							'label'   => 'Product Brand',
							'url'   => ['/hends/brand/index'],
							'active'=> $this->uniqueId == '/hends/brand/'
						]
						
					]
				],
				[
					'label' => "",
					'url' => "#",
					'linkOptions' => ["class"=>"fa fa-book", "data-original-title"=>"Справочники"],
					'items' => [
					]
				],
				[
					'label' => "",
					'url' => "#",
					'linkOptions' => ["class"=>"fa fa-gear", "data-original-title"=>"Администратор"],
					'items' => [
						[
							'label'   => 'Настройки',
							'url'   => ['/core/admin/settings/index'],
							'active'=> $this->uniqueId == 'core/admin/settings'
						],
						[
							'label'   => 'Справочник',
							'url'   => ['/core/admin/lookup/index'],
							'active'=> $this->uniqueId == 'core/admin/lookup'
						],
						[
							'label'   => 'Пользователи',
							'url'     => ['/hends/person/index'],
							'active'  => $this->uniqueId == 'hends/person' && 
										 ($this->action->id == 'index' || $this->action->id == 'update') 
						],
						[
							'label'   => 'Права доступа',
							'url'     => ['/core/rights/assignment/view'],
							'active'  => $this->module->id == 'rights'
						]
					]
				]

			]
	}

</nav>