<?php 


/**
 * Компонент для работы с amqp для php
 *
 * @author Alexander Manukian
 */

class SAMQP extends CApplicationComponent {
    private $_host;
    private $_login;
    private $_password;
    private $_port;
    private $_vhost;

    private $_connection;
    private $_channel;
    private $_exchange;
    private $_queue;


    public function init()
    {
        /*if (!extension_loaded("amqp")) {
            // print "loading extension\n";
            dl('amqp.so'); //для загрузки динамической библиотеки
        }*/

        parent::init();
    }

    /**
     * Проверяем загружена ли библиотека
     * @return [bool]
     */
    public function getLoaded(){
        return extension_loaded("amqp") && $this->getConnection();
    }

    public function __construct() { }

    public function __destruct() {
        if(  $this->_connection ) {
            $this->_connection->disconnect();
        }
    }

    private function getConnection() {
        if( !$this->_connection ) {
            $this->_connection = new AMQPConnection();
            $this->_connection->setLogin($this->_login);
            $this->_connection->setPassword($this->_password);
            $this->_connection->setHost($this->_host);

            try{ 
                $this->_connection->connect();
            }
            catch(Exception $e){
                $this->_connection = null;
                return;
            }

            if (!$this->_connection->isConnected()) {
                $this->_connection = null;
                return;
            }
        }
        return $this->_connection;
    }

    private function getChannel() {
        if( !$this->_channel ) {
            $this->_channel = new AMQPChannel( $this->getConnection() );
        }
        return $this->_channel;
    }

    private function getExchange($name, $type = 'direct') {
        if( !$this->_exchange ) {
            $this->_exchange = new AMQPExchange( $this->getChannel() );
            $this->_exchange->setName( $name );
            $this->_exchange->setType( $type );
            $this->_exchange->declareExchange();
        }
        return $this->_exchange;
    }

    private function getQueue($name) {
        if( !$this->_queue ) {
            $this->_queue = new AMQPQueue( $this->getChannel() );
            $this->_queue->setName( $name );
            $this->_queue->setFlags(AMQP_DURABLE); //durable - долгосрочный (не удаляется после падения сервера)
            $this->_queue->declareQueue();
            $this->_queue->bind( $this->_exchange->getName(), $name );
        }
        return $this->_queue;
    }

    public function exchange($name, $type = 'direct'){
        $this->getExchange($name, $type);
        return $this;
    }

    public function publish($msg, $queue, $exchange = NULL, $exchangeType = NULL)
    {
        if($exchange){
            $ex = $this->getExchange($exchange, $exchangeType);
        }
        else {
            $ex = $this->_exchange;
        }
        try {
             
            /**
             * вдруг очередь еще не объявлена?, 
             * но с другой стороны если мы будем создавать очередь с флагом DURABLE, 
             * то при отправке сообщения в очередь если создавать очередь это вызовет конфликт
             */
            // $this->getQueue($queue, $exchange); 
            
            if( !$ex->publish( (string)$msg, $queue, AMQP_NOPARAM, ['delivery_mode' => 2]) ) { //delivery_mode = 2 //persistent message (не удаляется после падения сервера)
                throw new Exception('Message not sent!');
            }
            // if( !$ex->publish( (string)$msg, $queue) ) { //delivery_mode = 2 //persistent message (не удаляется после падения сервера)
            //     throw new Exception('Message not sent!');
            // }
        }
        catch( Exception $e ) {
            die( $e->getMessage() );
        }
    }

    //забирает все сообщения из очереди
    public function checkForMessages($queue,  $exchange = NULL, $exchangeType = NULL) {
        if($exchange){
            $this->getExchange($exchange, $exchangeType); // вдруг обменник еще не объявлен?
        }
        
        while ($envelope = $this->getQueue($queue)->get(AMQP_NOPARAM)) { //AMQP_AUTOACK - удаляет из очереди
            echo $envelope->getBody(); // здесь некая полезная работа происходит. имеет смысл ее реализацию вынести в отдельное расширение
        }
    }

    //прослушка очереди, без окончания скрипта
    public function consume($queue, $callback, $exchange = NULL, $exchangeType = NULL) {
        if($exchange){
            $this->getExchange($exchange, $exchangeType); // вдруг обменник еще не объявлен?
        }

        $this->getQueue($queue)->consume($callback);
    }

    public function setHost( $_host ) {
        $this->_host = $_host;
    }
    public function setLogin( $_login ) {
        $this->_login = $_login;
    }
    public function setPassword( $_password ) {
        $this->_password = $_password;
    }
    public function setPort( $_port ) {
        $this->_port = $_port;
    }
    public function setVhost( $_vhost ) {
        $this->_vhost = $_vhost;
    }

}