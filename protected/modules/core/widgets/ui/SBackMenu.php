<?php 


/**
* SBackMenu
*/
class SBackMenu extends SMenuComponent
{	
	//переопределяем чтобы не попадал в кеш
	public function load($submenu = '')
    {
        return call_user_func(array($this, $submenu . 'Menu'));
    }

	public function getMenuName(){
    	return 'BackMenu';
    }
	
   	public function Menu(){ //root menu
        $items = [
            [ //ГЛАВНАЯ
                'label'   => CHtml::tag('i',    ['class'=>'icon-custom-home'], '') . 
                             CHtml::tag('span', ['class'=>'title'], 'Главная'),
                'url'     => [param('adminHome')],
                'linkOptions' => ['data-original-title' => 'Главная', 'data-toggle'=>'tooltip', 'data-placement'=>'right'],
            ],

            [ // Уведомления
                'label'   => CHtml::tag('i',    ['class'=>'fa fa-phone'], '') . 
                             CHtml::tag('span', ['class'=>'title'], 'Пропущенные вызовы'),
                'url'     => ['/market/notifications/missed'],
                'active'  => $this->uniqueId == 'market/notifications',
                'linkOptions' => ['data-original-title' => 'Пропущенные вызовы', 'data-toggle'=>'tooltip', 'data-placement'=>'right'],
            ],

            [ //СТРАНИЦЫ И БЛОКИ
                'label'   => CHtml::tag('i',    ['class'=>'fa fa-file'], '') .  
                             CHtml::tag('span', ['class'=>'title'], 'Страницы и блоки') .
                             CHtml::tag('span', ['class'=>'arrow'], ''),
                'url'     => '#',
                'active'  => $this->uniqueId == 'page/back' || $this->uniqueId == 'page/blocks',
                'items'   => $this->PageMenu(),
                'visible' => false
            ],

            [ //info
                'label'   => CHtml::tag('i',    ['class'=>'fa fa-info-circle'], '') .  
                             CHtml::tag('span', ['class'=>'title'], 'Справка'),
                'url'     => '#',
                'linkOptions' => ['data-original-title' => 'Справка', 'data-toggle'=>'tooltip', 'data-placement'=>'right'],
            ],
        ];

    	return $items;
    }

    public function ArticleMenu(){
    	return [
            [
                'label'   => 'Список статей',
                'url'     => ['/article/back/index'],
                'active'  => $this->uniqueId == 'article/back' && 
                        	 ($this->action->id == 'index' || $this->action->id == 'update' || $this->action->id == 'create')
            ],

            [
                'label'   => 'Категории статей', 
                'url'     => ['/article/back/categories'],
                'visible' => false
            ]
        ];
    }

    public function CatalogMenu(){
    	return [
            [
                'label'   => 'Продукты',
                'url'     => ['/store/product/index'],
                'active'  => $this->uniqueId == 'store/product'
            ],
            [
                'label'   => 'Услуги',
                'url'     => ['/store/service/index'],
                'active'  => $this->uniqueId == 'store/service'
            ],
            [
                'label'   => 'Заказы'.$this->getNotifications('Catalog', 'Orders'),
                'url'     => ['/order/back/index'],
                'active'  => $this->uniqueId == 'order/back'
            ],
            [
                'label'   => 'Способы доставки',
                'url'     => ['/store/delivery/index'],
                'active'  => $this->uniqueId == 'store/delivery'
            ],
            [
                'label'   => 'Способы оплаты',
                'url'     => ['/store/paymentmethod/index'],
                'active'  => $this->uniqueId == 'store/paymentmethod'
            ],
            [
                'label'   => 'Валюта',
                'url'     => ['/store/currency/index'],
                'active'  => $this->uniqueId == 'store/currency'
            ]
        ];
    }

    public function BannerMenu(){
        return [
            [
                'label'   => 'Банеры',
                'url'     => ['/banner/back/index'],
                'active'  => $this->uniqueId == 'banner/back'
            ],
            [
                'label'   => 'Категории банеров',
                'url'     => ['/banner/zones/index'],
                'active'  => $this->uniqueId == 'banner/zones',
                'visible' => false
            ],
        ];
    }

    public function PageMenu(){
        return [
            [
                'label'   => 'Страницы',
                'url'   => ['/page/back/index'],
                'active'=> $this->uniqueId == 'page/back'
            ],
            [
                'label'   => 'Блоки',
                'url'   => ['/page/blocks/index'],
                'active'=> $this->uniqueId == 'page/blocks'
            ],
        ];
    }


    public function getNotifications($menu, $submenu = false){
    	$result = '';

    	switch($menu){
			case 'Catalog':
				$class = !$submenu ? 'ml10' : 'pull-right mr20';
				$count = Order::getOrdersCount(Order::STATUS_NEW);
				break;
		}

    	return  $count > 0 ? CHtml::tag('span', ['class'=>'badge badge-important '.$class], $count) : '';
    }

}