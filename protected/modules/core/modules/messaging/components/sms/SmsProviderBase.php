<?php

require_once 'ISmsProvider.php';

abstract class SmsProviderBase extends CComponent implements ISmsProvider
{
    protected $gateway_url;     // URL шлюза

    protected $login;           // Логин для доступа к API

    protected $password;        // Пароль для доступа к API

    protected $from;            // Идентификатор отправителя по-умолчанию

    protected $coding;          // Кодировка по умолчанию(0 - Латинские символы, 1 - Бинарное сообщение, 2 - Кириллические символы)
    
    protected $priority;        // Приоритет сообщения, от 0 до 3, по умолчанию равен 0

    protected $flash;           // Класс сообщения, 0 - Флеш, 1 - Обычное sms, по умолчанию равен 1

    protected $delivery_mask;   // Уведомление о доставке, 0 - Выключен, 31 - Включен, по умолчанию равен 31

    protected $deferred;         // Интервал, после которого следует отправить сообщение, в минутах.

    public function init($options = array())
    {
        foreach ($options as $key => $val)
            if (property_exists($this, $key))
                $this->$key = $val;
    }
}