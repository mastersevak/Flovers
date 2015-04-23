<?php

/**
 * Класс для отправки смс провайдеру(Stramedia - http://stramedia.ru/)
 *
 * Для посылки смс, в пост запросе нужно послать следующие поля:
 *
 * 1)  username  - Имя пользователя в системе провайдера
 * 2)  password  - Пароль в системе провайдера
 * 3)  to        - Номер получателя в международном формате. Для массовой отправки, номера следует разделять через запятую.
 * Максимальное количество номеров в одном HTTP запросе равно 10. При превышении допустимого количества номеров, сообщения
 * будут отправлены на первые 10 номеров
 * 4)  from      - Номер отправителя, максимально до 11 цифр или латинских символов
 * 5)  coding    - Кодировка: 0 - латинские символы, 1 - бинарное сообщение, 2 - кириллические символы
 * 6)  text      - Текст сообщения, который может содержать до 765 латинских символов, или 335 кириллических (5 смс)
 * 7)  priority  - Приоритет сообщения, от 0 до 3, по умолчанию равен 0
 * 8)  mclass    - Класс сообщения, 0 - флешь, 1 - обычное смс. По умолчанию равен 1
 * 9)  dlrmask   - Уведомление о доставке, 0 - выключен, 31 - включен. По умолчанию равен 31
 * 10) deferred  - Интервал, после которого следует отправить сообщение, в минутах. Например, если сейчас время 21:15
 * и вы хотите отправить сообщение в следующий день в 09:00, значением поля deferred должно быть 705(11 * 60 + 45).
 * 
 */

require_once dirname(dirname(__FILE__)).'/SmsProviderBase.php';

class StramediaProvider extends SmsProviderBase
{
	protected $gateway_url = 'https://www.stramedia.ru/modules/';

	public $errorNames = [
		"Invalid request"                                   => self::ERROR_INVALID_REQUEST,
		"Invalid username or password or user is blocked"   => self::ERROR_AUTHENTICATION_FAILED,
		"Invalid or missing 'from' address"                 => self::ERROR_INVALID_OR_MISSING_FROM_ADDRESS,
		"Invalid or missing 'to' address"                   => self::ERROR_INVALID_OR_MISSING_TO_ADDRESS,
		"Invalid or missing coding"                         => self::ERROR_INVALID_OR_MISSING_CODING,
		"Missing text"                                      => self::ERROR_MISSING_TEXT,
		"Text too long"                                     => self::ERROR_TEXT_TOO_LONG,
		"Invalid or missing mclass"                         => self::ERROR_INVALID_OR_MISSING_MCLASS,
		"Invalid or missing priority"                       => self::ERROR_INVALID_OR_MISSING_PRIORITY,
		"Invalid or missing dlrmask"                        => self::ERROR_INVALID_OR_MISSING_DLRMASK,
		"IP not allowed"                                    => self::ERROR_IP_NOT_ALLOWED,
		"Max limit exceeded"                                => self::ERROR_MAX_LIMIT_EXCEEDED,
		"Insufficient balance"                              => self::ERROR_INSUFFICIENT_BALANCE
	];

	public function send($phone, $message, $params = array())
	{
		$data = array(
			'username'          => $this->login,
			'password'          => $this->password,
			'to'                => $phone, //$this->preparePhone($phone),
			'from'              => isset($params['from']) ? $params['from'] : $this->from,
			'coding'            => isset($params['coding']) ? $params['coding'] : $this->coding,
			'text'              => $message, //$this->prepareMessage($message),
			'priority'          => isset($params['priority']) ? $params['priority'] : $this->priority,
			'mclass'            => isset($params['flash']) ? (int) $params['flash'] : $this->flash,
			'dlrmask'           => isset($params['delivery_mask']) ? $params['delivery_mask'] : $this->delivery_mask,
			'deferred'          => isset($params['deferred']) ? $params['deferred'] : $this->deferred
		);
		$result = array('success' => false, 'message' => 'unknown error', 'id_message' => NULL);
		$output = $this->execute('send_sms.php', $data);
		if ($output !== false)
		{
			if(preg_match('/ID: (?P<id_message>\w+)/', $output, $matches)){
				$result['success'] = true;
				$result['id_message'] = $matches['id_message'];
			}
			// elseif(preg_match('/Error: (?P<error>\w+)/', $output, $matches)){
			//         $result['success'] = false;
			//         $result['id_error'] = $errorNames[$matches['error']];
			//     }
			// }

			$result['message'] = $output;
		}

		return $result;
	}

	private function execute($url_end, $data)
	{
		try
		{
			$curl = Yii::app()->curl;
			$curl->setOptions([
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_SSL_VERIFYHOST => FALSE,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_RETURNTRANSFER => TRUE
			]);
			$result = $curl->post($this->gateway_url.$url_end, $data, true);
		}
		catch (Exception $e)
		{
			return false;
		}

		return $result;
	}

	public function getBalance()
	{
		$result = array('success' => false, 'count' => 0, 'unit' => 'sms');

		$data = array(
			'user'  => $this->login,
			'pass'  => $this->password,
		);

		$output = $this->execute('get_credit_info.cgi', $data);

		if ($output !== false && preg_match('/Your credit: (?P<count>\d+) SMS/', $output, $matches))
		{
			$result['success'] = true;
			$result['count']   = $matches['count'];
		}

		return $result;
	}

	public function getStatus($id_message)
	{
		$result = array('success' => false, 'status' => self::STATUS_UNKNOWN);

		$data = array(
			'username'  => $this->login,
			'password'  => $this->password,
			'id'        => $id_message
		);

		$output = $this->execute('sms_status2.cgi', $data);

		if ($output !== false)
		{
			if ($output == 'Status: delivered')
			{
				$result['success'] = true;
				$result['status']  = self::STATUS_DELIVERED;
			}
			else if ($output == 'Status: not delivered')
			{
				$result['success'] = true;
				$result['status']  = self::STATUS_NOT_DELIVERED;
			}
			else if ($output == 'Status : unknown')
			{
				$result['success'] = true;
				$result['status']  = self::STATUS_UNKNOWN;
			}
		}

		return $result;
	}

	private function prepareMessage($message, $encoding = 'utf-8')
	{
		return iconv($encoding, 'windows-1251', $message);
	}

	private function preparePhone($phone)
	{
		// удаляем ненужные символы и плюс перед номером телефона
		return StringHelper::cleanup_phone($phone, true);
	}

}