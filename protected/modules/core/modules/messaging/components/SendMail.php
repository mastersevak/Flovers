<?php

class SendMail extends CApplicationComponent 
{
	public $options = []; // exchange, saveToDb, 
	protected $defaultOptions = ['exchange' => 'email', 'queue' => 'mail', 'saveToDb' => false];

	public $host;
	public $debug;
	public $auth;
	public $secure;
	public $port;
	public $username;
	public $password;
	public $charset;
	public $from;
	public $fromname;
	public $sendType='SMTP';
	private $_error;

	// public $exchange = 'email';

	public $layout;

	public $behaviors = [
		'customError' => ['class'=>'core.behaviors.CustomErrorBehavior']
	];	

	public function init(){
		parent::init();
		$this->options = CMap::mergeArray($this->defaultOptions, $this->options);
	}
	
	/**
	 * Отправка почты
	 * @param str $email
	 * @param str $subject
	 * @param str $message
	 */
	public function add($email, $subject, $message = null, $attachments){
		$mail = new Mail;
		$mail->email = $email;
		$mail->subject = $subject;
		if(is_array($attachments)){
			$attachments = implode(',', $attachments);
		}
		$mail->attachments = $attachments;
		$mail->message = $message;
		$mail->status = Mail::STATUS_NEW;
		$mail->save(false);

		return $mail->id;
	}
	
	public function send($email, $subject, $message = null, $from = null, $attachments = null, $direct = true) {
		if($this->options['saveToDb']){
			$emailId = $this->add($email, $subject, $message, $attachments);

			return $this->sendId($emailId, $direct);
		}else{
			$this->_send($email, $subject, $message, $from, $attachments, $direct);
		}
	}

	public function sendId($id, $direct = true){
		$mail = Mail::model()->findByPk($id);
		if($mail){
			if ($this->options['debug']){
				$mail->status = Mail::STATUS_SENT;
			}else{
				if($direct){ //ОТПРАВЛЯЕМ СРАЗУ
					$result = $this->_send($mail->email, $mail->subject, $mail->message, null, $mail->attachments, true);
					
					if($result == false){
						$mail->error = $this->_error;
						$mail->status = Mail::STATUS_ERROR;	
					}else{
						$mail->error = '';
						$mail->status = Mail::STATUS_SENT;
					}
				}else{ //ОТПРАВЛЯЕМ В ОЧЕРЕДЬ
					$mail->status = Mail::STATUS_QUEUED;
					
					if(Yii::app()->amqp->loaded){
						Yii::app()->amqp->exchange($this->options['exchange'])->publish(serialize(['type' => 'mail', 'id' => $id]), $this->options['queue']);
					}
				}
			}

			$mail->save(false);

			return $mail->status != Mail::STATUS_ERROR;
		}

		return false;
	}

	private function _send($email, $subject, $message = null, $from = null, $attachments = null, $direct = false) {
		if($direct){
			try {
				Yii::app()->mailer->ClearAddresses ();
				Yii::app()->mailer->ClearCCs ();
				Yii::app()->mailer->ClearBCCs ();
				Yii::app()->mailer->ClearReplyTos ();
				Yii::app()->mailer->ClearAllRecipients ();
				Yii::app()->mailer->ClearAttachments ();
				Yii::app()->mailer->ClearCustomHeaders ();
				Yii::app()->mailer->IsHTML (true);
				$sendType = 'Is'.$this->sendType;
				Yii::app()->mailer->$sendType();
				//Yii::app()->mailer->IsSMTP();
				Yii::app()->mailer->Subject = $subject;
				
				if($message)
					Yii::app()->mailer->Body = $message;


				$attachments = explode(',', $attachments);
				if(!empty($attachments) && is_array($attachments))
				{
					foreach ($attachments as $attachment) 
					{
						if (file_exists ($attachment)) 
						{
							Yii::app()->mailer->AddAttachment($attachment);
						}
					}
				}


				if ( is_array($email) ) 
				{
					foreach ($email as $value) 
					{
						Yii::app()->mailer->AddAddress($value);
					}
				} 
				else 
				{
					Yii::app()->mailer->AddAddress($email);
				}

				Yii::app()->mailer->Host		= Yii::app()->mail->host;
				Yii::app()->mailer->SMTPDebug	= Yii::app()->mail->debug;
				Yii::app()->mailer->SMTPAuth	= Yii::app()->mail->auth;
				Yii::app()->mailer->SMTPSecure  = Yii::app()->mail->secure;
				Yii::app()->mailer->Port		= Yii::app()->mail->port;
				Yii::app()->mailer->Username	= Yii::app()->mail->username;
				Yii::app()->mailer->Password	= Yii::app()->mail->password;
				Yii::app()->mailer->CharSet	= Yii::app()->mail->charset;

				if($from == null){
					Yii::app()->mailer->From		= Yii::app()->mail->from;
					Yii::app()->mailer->FromName	= Yii::app()->mail->fromname;
				}
				

				$res = Yii::app()->mailer->Send();

				if(Yii::app()->mailer->IsError())
				{
					$this->_error = Yii::app()->mailer->ErrorInfo;
					$this->addCustomErrorMessage($this->_error);

					return false;
				}
				else
				{
					return true;
				}
			} catch (Exception $e) {
				$this->_error = Yii::app()->mailer->ErrorInfo;
				$this->addCustomErrorMessage($this->_error);

				return false;
			}
		}else{
			if(Yii::app()->amqp->loaded)
				Yii::app()->amqp->exchange(
					$this->options['exchange'])->publish(serialize([
							'type'			=>	'mail', 
							'email' 		=> 	$email, 
							'subject' 		=> 	$subject,
							'message' 		=> 	$message,
							'from' 			=>  $from,
							'attachments'	=>  $attachments]), 
						$this->options['queue']);
		}		
	}
	
	public function getError(){
		return $this->_error;
	}

	public function getView($view, $vars = array(), $layout = null){
		if($layout == null)
			$layout = $this->layout;
		
		return Yii::app()->mailer->getView ($view, $vars, $layout);
	}
}
