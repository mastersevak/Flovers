<?php

interface ISmsProvider
{
    const ERROR_INVALID_REQUEST                     = 1;//Проверьте наличие всех необходимых параметров в запросе
    const ERROR_AUTHENTICATION_FAILED               = 2;//Проверьте логин и пароль и то, что вашаккаунт не заблокирован
    const ERROR_INVALID_OR_MISSING_FROM_ADDRESS     = 3;//Проверьте наличие и длину адреса отправителя
    const ERROR_INVALID_OR_MISSING_TO_ADDRESS       = 4;//Проверьте наличие и формат номера получателя
    const ERROR_INVALID_OR_MISSING_CODING           = 5;//Проверьте наличие и значение параметра coding
    const ERROR_MISSING_TEXT                        = 6;//Проверьте наличие параметра text
    const ERROR_TEXT_TOO_LONG                       = 7;//Проверьте длину параметра text
    const ERROR_INVALID_OR_MISSING_MCLASS           = 8;//Проверьте наличие и значение параметра mclass
    const ERROR_INVALID_OR_MISSING_PRIORITY         = 9;//Проверьте наличие и значение параметра priority
    const ERROR_INVALID_OR_MISSING_DLRMASK          = 10;//Проверьте наличие и значение параметра dlrmask
    const ERROR_IP_NOT_ALLOWED                      = 11;//Ваш IP блокирован, обратитесь к администратору системы
    const ERROR_MAX_LIMIT_EXCEEDED                  = 12;//Вы достигли максимального числа sms, обратитесь к администратору системы
    const ERROR_INSUFFICIENT_BALANCE                = 13;//У вас недостаточно средств на балансе

    const STATUS_DELIVERED_TO_GATEWAY               = 0;//Сообщение передано шлюзу
    const STATUS_DELIVERED_TO_RECIPIENT             = 1;//Успешно доставлено до получателя
    const STATUS_DEVICE_REJECTED                    = 2;//Аппарат получателя отклонил SMS
    const STATUS_IN_QUEUE                           = 4;//Сообщение в очереди у оператора связи
    const STATUS_OPERATOR_ACCEPTED                  = 8;//Оператор связи принял SMS
    const STATUS_OPERATOR_REJECTED                  = 16;//Оператор связи отклонил SMS
    const STATUS_GATEWAY_REJECTED                   = 32;//Шлюз отклонил SMS

    public function init($options = array());

    public function send($phone, $message, $params = array());

    public function getStatus($message_id);

    public function getBalance();
}