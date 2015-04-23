<?php 


Yii::import('zii.widgets.CMenu');
/**
* SMenu
*/
class SMenu extends CMenu {
	public $submenuWrapper;
    public $submenuWrapperHtmlOptions = array();
    public $activateItems = true;
    public $activateParents;

    public $containerTag = 'ul';
    public $itemTag = 'li';

    /**
     * Initializes the menu widget.
     * This method mainly normalizes the {@link items} property.
     * If this method is overridden, make sure the parent implementation is invoked.
     */
    public function init()
    {
        $this->setVisible($this->items);
        parent::init();
    }

    /**
     * Renders the menu items.
     * @param array $items menu items. Each menu item will be an array with at least two elements: 'label' and 'active'.
     * It may have three other optional elements: 'items', 'linkOptions' and 'itemOptions'.
     */
    protected function renderMenu($items)
    {
        if(count($items))
        {
            echo CHtml::openTag($this->containerTag, $this->htmlOptions)."\n";
            $this->renderMenuRecursive($items);
            echo CHtml::closeTag($this->containerTag);
        }
    }

   /**
    * Recursively renders the menu items.
    * @param array $items the menu items to be rendered recursively
    */
   	protected function renderMenuRecursive($items)
   	{
       $count=0;
       $n=count($items);
       foreach($items as $item)
       {

            $count++;
            $options=isset($item['itemOptions']) ? $item['itemOptions'] : array();
            $class=array();
            if($item['active'] && $this->activeCssClass!='')
                $class[]=$this->activeCssClass;
            if($count===1 && $this->firstItemCssClass!==null)
                $class[]=$this->firstItemCssClass;
            if($count===$n && $this->lastItemCssClass!==null)
                $class[]=$this->lastItemCssClass;
            if($this->itemCssClass!==null)
                $class[]=$this->itemCssClass;

            if($class!==array())
            {
                if(empty($options['class']))
                    $options['class']=implode(' ',$class);
                else
                    $options['class'].=' '.implode(' ',$class);
            }

            echo CHtml::openTag($this->itemTag, $options);

            $menu=$this->renderMenuItem($item);
            if(isset($this->itemTemplate) || isset($item['template']))
            {
               $template=isset($item['template']) ? $item['template'] : $this->itemTemplate;
               echo strtr($template,array('{menu}'=>$menu));
            }
            else
                   echo $menu;

            if(isset($item['items']) && count($item['items']))
            {
               echo "\n";
               
                if($this->submenuWrapper)
                    echo CHtml::openTag($this->submenuWrapper, $this->submenuWrapperHtmlOptions);
               
                echo CHtml::openTag($this->containerTag,isset($item['submenuOptions']) ? $item['submenuOptions'] : $this->submenuHtmlOptions)."\n";
                $this->renderMenuRecursive($item['items']);
                echo CHtml::closeTag($this->containerTag);

                if($this->submenuWrapper)
                    echo CHtml::closeTag($this->submenuWrapper);

                echo "\n";
            }

            echo CHtml::closeTag('li')."\n";
       }
   	}  

   	protected function renderMenuItem($item)
	{
	    if(isset($item['url']))
	    {
	    	if(isset($item['linkLabelWrapper'])){ //эту часть добавили мы
	    		$label = CHtml::tag($item['linkLabelWrapper'], $item['linkLabelWrapperHtmlOptions'], $item['label']);
	    	}
	    	else{
		    	$label = $this->linkLabelWrapper===null ? 
        					$item['label'] : 
        					CHtml::tag($this->linkLabelWrapper, $this->linkLabelWrapperHtmlOptions, $item['label']);	
	    	}
	       
	        
	        return CHtml::link($label,$item['url'],isset($item['linkOptions']) ? $item['linkOptions'] : array());
	    }
	    else
	        return CHtml::tag('span',isset($item['linkOptions']) ? $item['linkOptions'] : array(), $item['label']);
	}

    private function setVisible(&$items){

        foreach($items as &$item){
            
            if(!isset($item['visible']) && $item['url'] && $item['url'] != '#' && !user()->isGuest){
                $authItem = '';
                $urlParts = explode(',', $item['url'][0]);
                $urlParts = explode('/', $urlParts[0]);

                array_walk($urlParts, function(&$item){
                    $item = ucfirst($item);
                });

                foreach($urlParts as $index => $part)
                    if(empty($part)) array_splice($urlParts, $index, 1);

                // Append the module id to the authorization item name
                // in case the controller called belongs to a module
                $authItem = implode(".", array_slice($urlParts, 0, count($urlParts) - 1));

                // Check if user has access to the controller
                if(!user()->checkAccess($authItem.'.*') )
                { 
                    // Append the action id to the authorization item name
                    $authItem .= '.'.ucfirst(end($urlParts));
                     // dump(user()->checkAccess($authItem));
                    // Check if the user has access to the controller action
                    $item['visible'] = user()->checkAccess($authItem);
                    // if($authItem == 'Core.Admin.Settings')
                }
                else{
                    
                    $item['visible'] = true;
                }
            }

            if(isset($item['items'])) $this->setVisible($item['items']);
        }
    }
}