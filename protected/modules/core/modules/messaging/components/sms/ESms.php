<?php

/**
 * Include the Sms model.
 */
class ESms extends CApplicationComponent
{
	/**
	* The path to the directory where the view for getView is stored. Must not
	* have ending dot.
	*
	* @var string
	*/
	protected $pathViews = 'application.views.sms';
	/**
	* The path to the directory where the layout for getView is stored. Must
	* not have ending dot.
	*
	* @var string
	*/
	protected $pathLayouts = 'application.views.sms.layouts';

	public $options = array(); //debug, exchange, saveToDb, 
	public $provider;
	private $_provider_instance;

	protected $defaultOptions = array('debug' => false, 'exchange' => 'sms', 'queue' => 'sms', 'saveToDb' => false);

	public function init()
	{
		$this->options = CMap::mergeArray($this->defaultOptions, $this->options);
		$this->initProviderInstance();
		
		parent::init();
	}

	/**
	* Setter
	*
	* @param string $value pathLayouts
	*/
	public function setPathLayouts($value)
	{
		if (!is_string($value) && !preg_match("/[a-z0-9\.]/i"))
		 throw new CException(Yii::t('EMailer', 'pathLayouts must be a Yii alias path'));
		$this->pathLayouts = $value;
	}

	/**
	* Getter
	*
	* @return string pathLayouts
	*/
	public function getPathLayouts()
	{
		return $this->pathLayouts;
	}

	/**
	* Setter
	*
	* @param string $value pathViews
	*/
	public function setPathViews($value)
	{
		if (!is_string($value) && !preg_match("/[a-z0-9\.]/i"))
		 throw new CException(Yii::t('EMailer', 'pathViews must be a Yii alias path'));
		$this->pathViews = $value;
	}

	/**
	* Getter
	*
	* @return string pathViews
	*/
	public function getPathViews()
	{
		return $this->pathViews;
	}

	/**
	* Displays an e-mail in preview mode. 
	*
	* @param string $view the class
	* @param array $vars
	* @param string $layout
	*/
	public function getView($view, $vars = array(), $layout = null)
	{
		$sms = new Sms;
		$body = Yii::app()->controller->renderPartial($this->pathViews.'.'.$view, array_merge($vars, array('content'=>$sms)), true);
		if ($layout === null) {
			$sms->body = $body;
		}
		else {
			$sms->body = Yii::app()->controller->renderPartial($this->pathLayouts.'.'.$layout, array('content'=>$body), true);
		}

		return $sms->body;
	}


	private function initProviderInstance()
	{
		$class = $this->provider['class'];

		$dot = strrpos($class, '.');

		if ($dot > 0)
		{
			Yii::import($class);
			$class = substr($class, $dot + 1);
		}

		$this->_provider_instance = new $class();
		$this->_provider_instance->init($this->provider);
	}

	public function getProviderInstance()
	{
		return $this->_provider_instance;
	}

	public function add($phone, $body)
	{
		$sms = new Sms;
		$sms->phone = $phone;//StringHelper::cleanup_phone($phone, true);
		$sms->body = $body;
		$sms->queue_status = Sms::STATUS_NEW;
		$sms->save(false);

		return $sms->id;
	}

	public function send($phone, $body, $direct = true)
	{ 
		if($this->options['saveToDb']){
			$smsId = $this->add($phone, $body);

			return $this->sendId($smsId, $direct);
		}else{
			$this->_send($phone, $body, [], $direct);	
		}		
	}

	public function sendId($id, $direct = true)
	{
		$sms = Sms::model()->findByPk($id);
		if ($sms){
			if (!$this->isWorkTime()){
				$sms->queue_status = Sms::STATUS_QUEUED;
				$sms->save(false);

				return true;
			} 

			if ($this->options['debug']){
				$sms->queue_status = Sms::STATUS_SENT;
			}
			else{
				if($direct){ //ОТПРАВЛЯЕМ СРАЗУ
					$result = $this->_send($sms->phone, $sms->body, $direct);
					if ($result['success']){
						$sms->queue_status = Sms::STATUS_SENT;
						$sms->id_message = $result['id_message'];
					}
					//elseif($result['error']){
					else{
						$sms->queue_status = Sms::STATUS_ERROR;
						Yii::log(__FUNCTION__."\n".CVarDumper::dumpAsString($result));
					}
				}
				else{ //ОТПРАВЛЯЕМ В ОЧЕРЕДЬ
					$sms->queue_status = Sms::STATUS_QUEUED;
					
					if(Yii::app()->amqp->loaded)
						Yii::app()->amqp->exchange($this->options['exchange'])->publish(
							serialize(['type'=>'sms', 'id' => $id]), 
							$this->options['queue']);
				}
			}
			
			$sms->save(false);

			return $sms->queue_status != Sms::STATUS_ERROR;
		}

		return false;
	}

	public function isWorkTime()
	{
		if (isset($this->options['start_time']) && isset($this->options['end_time']))
		{
			$current_time = strtotime(date('H:i', time()));

			if ($current_time >= strtotime($this->options['start_time']) && $current_time <= strtotime($this->options['end_time']))
				return true;

			else
				return false;
		}

		return true;
	}

	private function _send($phone, $message, $params = array(), $direct = false)
	{
		if($direct)
			return $this->_provider_instance->send($phone, $message, $params);
		else{
			
			if(Yii::app()->amqp->loaded)
				Yii::app()->amqp->exchange($this->options['exchange'])->publish(
					serialize(['type'=>'sms', 'phone' => $phone, 'message' => $message]), 
					$this->options['queue']);
		}
	}
}

class ESmsException extends CException {}