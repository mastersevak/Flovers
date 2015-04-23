<?php

Yii::import('ext.payment.webmoney.WebMoneyConfigurationModel');

/**
 * WebMoney payment system
 */
class WebMoneyPaymentSystem extends BasePaymentSystem
{
	/**
	 * Enable testing mode
	 * @var bool
	 */
	public $testingMode = YII_DEBUG;

	/**
	 * This method will be triggered after redirection from payment system site.
	 * If payment accepted method must return Order model to make redirection to order view.
	 * @param StorePaymentMethod $method
	 * @return boolean|Order
	 */
	public function processPaymentRequest(StorePaymentMethod $method)
	{
		
		$request  = Yii::app()->request;
		$payment_id = $request->getParam('LMI_PAYMENT_NO');
		$settings = $this->getSettings($method->id);
		
		// Yii::log("Order: {$payment_id}\n".CVarDumper::dumpAsString($_REQUEST));
		
		$order = Order::model()->findByPk($payment_id);

		// Grab WM variables from post.
		// Variables to create md5 signature.
		$forHash = array(
			'LMI_PAYEE_PURSE'    => '',
			'LMI_PAYMENT_AMOUNT' => '',
			'LMI_PAYMENT_NO'     => '',
			'LMI_MODE'           => '',
			'LMI_SYS_INVS_NO'    => '',
			'LMI_SYS_TRANS_NO'   => '',
			'LMI_SYS_TRANS_DATE' => '',
			'LMI_SECRET_KEY'     => '',
			'LMI_PAYER_PURSE'    => '',
			'LMI_PAYER_WM'       => '',
		);

		foreach($forHash as $key=>$val)
		{
			if($request->getParam($key))
				$forHash[$key]=$request->getParam($key);
		}

		// Set Secret key from settings.
		$forHash['LMI_SECRET_KEY'] = $settings['LMI_SECRET_KEY'];

		// Check testing mode
		if ($this->testingMode === true)
			$forHash['LMI_MODE'] = 1;
		else
			$forHash['LMI_MODE'] = 0;

		// For first WebMoney pre-request 
		// (в первый раз webmoney обращается к странице, для того чтобы проверить доступность страницы)
		if (isset($_POST['LMI_PREREQUEST'])){
			//перепроверяем данные если нужно
			if(!$order) $this->addError("Нет такого заказа");

			// Check if order is paid.
			if ($order->paid) $this->addError("Заказ уже оплачен");

			// Check LMI_PAYEE_PURSE with settings.
			if ($settings['LMI_PAYEE_PURSE'] != $forHash['LMI_PAYEE_PURSE'])
				$this->addError("Номера кошельков не совпадают");

			// Check amount.
			if (Yii::app()->currency->convert($order->full_price, Yii::app()->currency->active->id, $method->currency_id) != $forHash['LMI_PAYMENT_AMOUNT'])
				$this->addError("Неверная сумма заказа");

			// Check for testing payment.
			if ($forHash['LMI_MODE'] == 1 && $this->testingMode == false)
				$this->addError("В настройках сайта стоит тестовый режим");
			
			//если ошибок нет
			die('YES');
		}

		if (!$request->getParam('LMI_HASH'))
			$this->addError("Нет хеш кода");

		// Create and check signature.
		$sign = strtoupper(md5(implode('', $forHash)));

		// If ok make order paid.
		if ($sign != $request->getParam('LMI_HASH'))
			$this->addError("Хеш код не совпадает");

		// Set order paid
		$order->setPaid(1);
		return $order->save();
	}

	public function renderPaymentForm(StorePaymentMethod $method, Order $order)
	{
		$html = '
		<form method="POST" action="https://merchant.webmoney.ru/lmi/payment.asp" accept-charset="windows-1251">
			<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="{PAYMENT_AMOUNT}">
			<input type="hidden" name="LMI_PAYMENT_NO" value="{PAYMENT_NO}">
			<input type="hidden" name="LMI_PAYMENT_DESC" value="{PAYMENT_DESC}">
			<input type="hidden" name="LMI_PAYEE_PURSE" value="{PAYEE_PURSE}">
			<input type="hidden" name="LMI_RESULT_URL" value="{RESULT_URL}">
			<input type="hidden" name="LMI_SUCCESS_URL" value="{SUCCESS_URL}">
			<input type="hidden" name="LMI_FAIL_URL" value="{FAIL_URL}">
			{SUBMIT}
		</form>';

		$settings=$this->getSettings($method->id);

		$html= strtr($html,array(
			'{PAYMENT_AMOUNT}' => Yii::app()->currency->convert($order->full_price, Yii::app()->currency->active->id, $method->currency_id),
			'{PAYMENT_NO}'     => $order->id,
			'{PAYMENT_DESC}'   => $order->getTitle(),
			'{PAYEE_PURSE}'    => $settings['LMI_PAYEE_PURSE'],
			'{SIM_MODE}'       => '0',
			'{RESULT_URL}'     => Yii::app()->createAbsoluteUrl('/order/payment/process', array('payment_id'=>$method->id)),
			'{SUCCESS_URL}'    => Yii::app()->createAbsoluteUrl('/order/payment/success'),
			'{FAIL_URL}'       => Yii::app()->createAbsoluteUrl('/order/payment/fail'),
			'{SUBMIT}'         => $this->renderSubmit(),
		));

		return $html;
	}

	/**
	 * This method will be triggered after payment method saved in admin panel
	 * @param $paymentMethodId
	 * @param $postData
	 */
	public function saveAdminSettings($paymentMethodId, $postData)
	{
		$this->setSettings($paymentMethodId, $postData['WebMoneyConfigurationModel']);
	}

	/**
	 * @param $paymentMethodId
	 * @return string
	 */
	public function getSettingsKey($paymentMethodId)
	{
		return $paymentMethodId.'_WebMoneyPaymentSystem';
	}

	/**
	 * Get configuration form to display in admin panel
	 * @param string $paymentMethodId
	 * @return CForm
	 */
	public function getConfigurationFormHtml($paymentMethodId)
	{
		$model = new WebMoneyConfigurationModel;
		$model->attributes=$this->getSettings($paymentMethodId);
		$form  = new BasePaymentForm($model->getFormConfigArray(), $model);
		return $form;
	}

	private function addError($message){
		Yii::log("Error: {$message}");
		die("Error: {$message}");
	}

}
