<?php
/**
 * Sendmail package
 *
 * @package   Sendmail
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail;

use Sendmail\Collection;
use Sendmail\Message;
use Sendmail\Sender\Mail;
use Sendmail\Sender\Smtp;

/**
 * Класс отправки E-mail сообщений
 *
 * @package Sendmail
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Facade
{
    /**
     * Создает объект коллекцию сообщений
     *
     * Для отправки почты через PHP функцию mail()
     * необходимо передать строку mail
     *
     * Для отправки почты через SMTP протокол
     * необходимо передать строку с набором параметров соединения
     * smtp://username:password@server:port
     *
     * Если используются настройки по умалчанию для
     * SMTP соединения то можно передать строку smtp
     *
     * Настройки по умолчанию для SMTP
     *     Сервер: localhost
     *     Порт: 25
     *     Авторизации: не требуется
     *
     * @param string $options
     *
     * @return \Sendmail\Queue
     */
    public function createQueue($options)
    {
        return new Queue($this->createSender($options));
    }

    /**
     * Создает объект отправителя сообщений
     *
     * Для отправки почты через PHP функцию mail()
     * необходимо передать строку mail
     *
     * Для отправки почты через SMTP протокол
     * необходимо передать строку с набором параметров соединения
     * smtp://username:password@server:port
     *
     * Если используются настройки по умалчанию для
     * SMTP соединения то можно передать строку smtp
     *
     * Настройки по умолчанию для SMTP
     *     Сервер: localhost
     *     Порт: 25
     *     Авторизации: не требуется
     *
     * @param string $options
     *
     * @return \Sendmail\Sender\SenderInterface
     */
    public function createSender($options)
    {
        // модуль PHP функции mail()
        if ($options == 'mail') {
            return new Mail();
        }

        // настройки по умолчанию для SMTP
        $default = array(
            'scheme' => '',
            'host'   => 'localhost',
            'port'   => 25,
            'user'   => '',
            'pass'   => '',
        );

        // составление набора параметров
        if ($options == 'smtp') {
            $options = $default;
            $options['scheme'] = 'smtp';
        } else {
            $options = array_merge($default, parse_url($options));
        }

        // модуль SMTP соединения
        if ($options['scheme'] == 'smtp') {
            return new Smtp(
                $options['host'].':'.$options['port'],
                $options['user'],
                $options['pass']
            );
        } else {
            throw new \InvalidArgumentException('Selected module is not supported');
        }
    }

    /**
     * Создает объект сообщения
     *
     * @param string
     * @param string
     * @param string
     *
     * @return \Sendmail\Message
     */
    public function createMessage($to, $subject, $message)
    {
        $mess = new Message();
        return $mess
            ->setTo($to)
            ->setSubject($subject)
            ->setMessage($message);
    }
}
