<?php

Yii::import('ext.payment.yandexmoney.YandexMoneyConfigurationModel');
Yii::import('application.modules.orders.models.Order');

/**
 * YandexMoney payment system
 *
 * Sample callback payment url: /processPayment/{$payment_system_id}
 * Docs http://api.yandex.ru/money/doc/dg/reference/notification-p2p-incoming.xml
 */
class YandexMoneyPaymentSystem extends BasePaymentSystem
{

	/**
	 * This method will be triggered after redirection from payment system site.
	 * If payment accepted method must return Order model to make redirection to order view.
	 *
	 * @param StorePaymentMethod $method
	 * @throws CHttpException
	 */
	public function processPaymentRequest(StorePaymentMethod $method)
	{
		$settings = $this->getSettings($method->id);
		$request  = Yii::app()->request;

		$hash_params = array(
			'notification_type'   => $request->getParam('notification_type'),
			'operation_id'        => $request->getParam('operation_id'),
			'amount'              => $request->getParam('amount'),
			'currency'            => $request->getParam('currency'),
			'datetime'            => $request->getParam('datetime'),
			'sender'              => $request->getParam('sender'),
			'codepro'             => $request->getParam('codepro'),
			'notification_secret' => $settings['password'],
			'label'               => $request->getParam('label')
		);

		// Build and check payment hash
		$hash = sha1(implode('&', $hash_params));

		if($hash !== $request->getParam('sha1_hash'))
			throw new CHttpException(404, 'Wrong hash');

		// Load order
		$order = $this->loadOrder($hash_params['label']);

		if(!$order)
			throw new CHttpException(404, 'Order not found');

		if (Yii::app()->currency->convert($order->full_price, Yii::app()->currency->active->id, $method->currency_id) < (float)$hash_params['amount'])
			throw new CHttpException(404, 'Wrong amount');

		// Make order paid
		$order->paid = true;
		$order->save(false);

		echo 'OK';
	}

	/**
	 * Generate payment form.
	 *
	 * @param StorePaymentMethod $method
	 * @param Order $order
	 * @return string
	 */
	public function renderPaymentForm(StorePaymentMethod $method, Order $order)
	{
		$settings=$this->getSettings($method->id);

		$sum = Yii::app()->currency->convert($order->full_price, Yii::app()->currency->active->id, $method->currency_id);

		$html = '<iframe frameborder="0" allowtransparency="true" scrolling="no"
		src="https://money.yandex.ru/embed/small.xml?uid={uid}&amp;button-text=01&amp;button-size=s&amp;button-color=white&amp;targets={comment}&amp;default-sum={sum}" width="auto" height="31">
		</iframe>';

		return strtr($html, array(
			'{uid}'     => $settings['uid'],
			'{comment}' => $this->getComment($order),
			'{sum}'     => $sum,
		));
	}

	public function getComment(Order $order)
	{
		return Yii::t('YandexMoneyPaymentSystem', 'Оплата заказа #{id}', array('{id}'=>$order->id));
	}

	/**
	 * This method will be triggered after payment method saved in admin panel
	 *
	 * @param $paymentMethodId
	 * @param $postData
	 */
	public function saveAdminSettings($paymentMethodId, $postData)
	{
		$this->setSettings($paymentMethodId, $postData['YandexMoneyConfigurationModel']);
	}

	/**
	 * @param $paymentMethodId
	 * @return string
	 */
	public function getSettingsKey($paymentMethodId)
	{
		return $paymentMethodId.'_YandexMoneyPaymentSystem';
	}

	/**
	 * Get configuration form to display in admin panel
	 *
	 * @param string $paymentMethodId
	 * @return CForm
	 */
	public function getConfigurationFormHtml($paymentMethodId)
	{
		$model             = new YandexMoneyConfigurationModel();
		$model->attributes = $this->getSettings($paymentMethodId);
		$form              = new BasePaymentForm($model->getFormConfigArray(), $model);

		return $form;
	}

	/**
	 * Find order by payment comment
	 *
	 * @param $label
	 * @return Order
	 */
	public function loadOrder($label)
	{
		preg_match('/#(\d+)/', $label, $m);
		if(!isset($m[1]))
			return false;

		return Order::model()->findByPk((int)$m[1]);
	}

}
