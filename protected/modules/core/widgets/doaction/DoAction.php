<?

class DoAction extends SWidget {

	protected $_actions;

	public $actions;
	public $id;
	public $updateGrid;

	public function init(){
		parent::init();

		$this->_actions = array(
			'add_article_to_shortcut' => array(
				'action' => url("/article/back/addtoshortcut"),
				'title' => t('admin', "Добавить статью на главную"),
				),
			'remove_article_from_shortcut' => array(
				'action' => url("/article/back/removefromshortcut"),
				'title' => t('admin', "Удалить статью из главной"),
				),
		);

		cs()->registerScriptFile($this->assetsUrl.'/doaction.js');
	}


	public function run() {
		echo CHtml::form('', 'post', array('class'=>'actions clearfix'));
			
			$gridId = $this->id ? $this->id : 'grid';
			echo CHtml::label(t('admin', 'Action'), 'doAction');

			echo CHtml::openTag('select', array(
				'id'=>'doAction-'.$gridId, 
				'class'=>'doAction',
				'onChange'=>'DoAction.onChange(this)'));
			echo "<option value=''>".t('admin', 'Choose action')."</option>";

			foreach($this->actions as $action){
				$params = [
					'data-action'=>$this->_actions[$action]['action'],
					'value'=>$action];

				if($this->updateGrid){
					$params['data-update-grid'] = $this->updateGrid;
				}

				echo CHtml::tag('option', $params, $this->_actions[$action]['title']);
			}
			echo CHtml::closeTag('select');

        CHtml::endForm();
    }

}
	

?>