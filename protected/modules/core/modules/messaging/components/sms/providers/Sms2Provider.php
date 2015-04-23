<?php

require_once dirname(dirname(__FILE__)).'/SmsProviderBase.php';

class Sms2Provider extends SmsProviderBase
{
    protected $gateway_url = 'http://sms.spb.ru/';

    public function send($phone, $message, $params = array())
    {
        $flash = isset($params['flash']) ? (int)$params['flash'] : 0;

        $data = array(
            'user'      => $this->login,
            'pass'      => $this->password,
            'message'   => $this->prepareMessage($message),
            'flash'     => $flash,
            'from'      => isset($params['from']) ? $params['from'] : $this->from,
            'phone'     => $this->preparePhone($phone),
        );

        $result = array('success' => false, 'message' => 'unknown error', 'message_id' => NULL);
        $output = $this->execute('sms.cgi', $data);

        if ($output !== false)
        {
            if (preg_match('/Message_ID=(?P<message_id>\w+)/', $output, $matches))
            {
                $result['success'] = true;
                $result['message_id'] = $matches['message_id'];
            }

            $result['message'] = $output;
        }

        return $result;
    }

    private function execute($url_end, $data)
    {
        try
        {
            $result =  Yii::app()->curl->post($this->gateway_url.$url_end, $data);
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

    public function getStatus($message_id)
    {
        $result = array('success' => false, 'status' => self::STATUS_UNKNOWN);

        $data = array(
            'user'  => $this->login,
            'pass'  => $this->password,
            'mess_id' => $message_id
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