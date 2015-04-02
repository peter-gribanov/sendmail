<?php
/**
 * Sendmail package
 *
 * @package   Sendmail
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail\Sender;

use Sendmail\Sender\SenderInterface;
use Sendmail\Message;

/**
 * Класс отправки E-mail сообщений через соединение с сервером почты
 *
 * Модуль собран по стандартам протоколов SMTP и ESMTP
 *
 * @package Sendmail\Sender
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Smtp implements SenderInterface
{
    /**
     * Текст последнего диалога с сервером
     *
     * @var string
     */
    public $log = '';

    /**
     * Код ошибки, если таковая была. Иначе 0
     *
     * @var integer
     */
    public $errno = 0;

    /**
     * Текст ошибки, если таковая была
     *
     * @var string
     */
    public $errstr = '';

    /**
     * Время ожидания реакции от сервера
     *
     * @var integer
     */
    private $timeout = 60;

    /**
     * Требуется ли именно безопасное соединение
     * По умолчанию нет
     *
     * @var boolean
     */
    private $secure = false;

    /**
     * Cоединения с SMTP сервером
     *
     * @var integer
     */
    private $connect = 0;

    /**
     * Параметры соединения
     *
     * @var array
     */
    private $options = array();


    /**
     * Конструктор класса
     *
     * @param string SMTP сервер
     * @param string Логин пользователя для авторизации
     * @param string Пароль пользователя для авторизации
     */
    public function __construct($server, $username = '', $password = '')
    {
        $port = 25;
        if (strpos($server, ':') !== false) {
            list($server, $port) = explode(':', $server, 2);
        }

        $this->options = array(
            'server'   => $server,
            'port'     => $port,
            'username' => $username,
            'password' => $password
        );

        if (PHP_SAPI != 'cli') {
            // за таймаут берет 90% от max_execution_time
            $this->timeout = ini_get('max_execution_time') * 0.9;
        }
    }

    /**
     * Метод подготавливает письмо к отправке и отправляет его
     * Возвращает true, если отправка прошла успешно
     *
     * @param \Sendmail\Message
     *
     * @return boolean
     */
    public function send(Message $message)
    {
        $this->errno = 0;
        $this->errstr = '';
        // Обнуляем лог
        $this->log = '';

        // Установка соединения с SMTP сервером
        $this->connect = fsockopen(
            $this->options['server'],
            $this->options['port'],
            $this->errno,
            $this->errstr,
            $this->timeout
        );

        // Проверка, установлено ли SMTP соединение
        if (!is_resource($this->connect)) {
            if ($this->errno == 0) {
                $this->errstr = 'Failed connect to: '.$this->options['server'];
            }
            return false;
        }

        // SMTP-сессия установлена, можно отправлять запросы

        try {
            // Соединено с?
            $this->log = fgets($this->connect, 4096);

            // Говорим EHLO
            $reply =& $this->call('EHLO '.$_SERVER['HTTP_HOST']);

            // Это протокол ESMTP
            if ($this->isSuccess($reply)) {
                // Если требуется, открываем TLS соединение, то открываем его
                if ($this->secure) {
                    $this->call('STARTTLS');
                    // После старта TLS надо сказать еще раз EHLO
                    $this->valid($this->call('EHLO '.$_SERVER['HTTP_HOST']));
                }
            } else {
                $this->valid($this->call('HELO '.$_SERVER['HTTP_HOST']));
            }

            if ($this->options['username'] && $this->options['password']) {
                // Запрос на авторизованный логин
                $this->call('AUTH LOGIN');
                // Отправка имени пользователя
                $this->call(base64_encode($this->options['username']));
                // Отправка пароля
                $this->valid($this->call(base64_encode($this->options['password'])));
            }

            // отправителя
            $this->valid($this->call('MAIL FROM: '.$message->getFrom()));


            // получателя
            $this->valid($this->call('RCPT TO: '.$message->getTo()));


            // готовимся к отправке данных
            $this->call('DATA');

            // Отправляем заголовок и само сообщение.
            // Точка в самом конце означает конец сообщения
            $this->valid($this->call($message->getHeaders()
                ."\r\n\r\n".$message->getMessage()."\r\n."));

        } catch (\Exception $e) {
            $this->errno = $e->getCode();
            $this->errstr = $e->getMessage();
        }

        // Завершаем передачу данных
        $reply =& $this->call('QUIT');

        // Закрываем SMTP соединение
        fclose($this->connect);

        return $this->errno == 0 && $this->isSuccess($reply);
    }

    /**
     * Устанавливает максимальное время ожидания ответа от сервера 
     *
     * @param integer
     *
     * @return \Sendmail\Sender\Smtp
     */
    public function setTimeOut($timeout)
    {
        if (!is_int($timeout) || $timeout <= 0 || $timeout > ini_get('max_execution_time')){
            throw new \InvalidArgumentException('Incorrect maximum waiting time from the server.');
        }
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Стартовать безопасное соединение
     *
     * @return \Sendmail\Sender\Smtp
     */
    public function startSecure()
    {
        $this->secure = true;
        return $this;
    }

    /**
     * Отправляет серверу запрос и возвращает ответ
     *
     * @param string
     *
     * @return string
     */
    private function &call($call)
    {
        fputs($this->connect, $call."\r\n");

        $reply = fread($this->connect, 4096);
        // Запись отладочной информации
        $this->log .= $call."\r\n".$reply;

        return $reply;
    }

    /**
     * Проверяет, является ли ответ сервера успешным
     * и в случае ошибки вызывает исключение
     *
     * @param string
     */
    private function valid(&$reply)
    {
        if (!$this->isSuccess($reply)) {
            list($code, $message) = $this->parse($reply);
            throw new \Exception($message, $code);
        }
    }

    /**
     * Разбирает ответ на код ответа и текст сообщения возвращая их
     *
     * <code>
     * [
     *     <code>,
     *     <message>
     * ]
     * </code>
     *
     * @param string
     *
     * @return array 
     */
    private function parse(&$reply)
    {
        if (preg_match('/^(\d{3}).*?([-_ \.a-zA-Z]+)[\r|\n]/', $reply, $match)) {
            return array(intval($match[1]), trim($match[2]));
        }
        return array(1, trim($reply));
    }

    /**
     * Проверяет, является ли ответ сервера успешным
     *
     * @param string
     */
    private function isSuccess(&$reply)
    {
        // код положительного ответа начинается с двойки
        return $reply[0] == 2;
    }
}
