<?php
namespace Sendmail;

include 'Message.php';
include 'Sender.php';

/**
 * Класс отправки E-mail сообщений
 *
 * @package   SendMail
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     18.11.2010
 * @version   1.5
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
     * @return \Sendmail\Collection
     */
    public static function Collection($options)
    {
        include_once 'Collection.php';
        return Collection::create(self::createSender($options));
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
     * @return \Sendmail\Sender
     */
    public static function Sender($options)
    {
        // модуль PHP функции mail()
        if ($options == 'mail') {
            include_once 'SenderMail.php';
            return new SenderMail();
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
            include_once 'SenderSMTP.php';
            return new SenderSMTP(
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
    public static function Message($to, $subject, $message)
    {
        return Message::create()
            ->setTo($to)
            ->setSubject($subject)
            ->setMessage($message);
    }
}
