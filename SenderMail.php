<?php
namespace Sendmail;

/**
 * Класс отправки E-mail сообщений через PHP функцию mail()
 *
 * @package   SendMail
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     18.11.2010
 * @version   1.1
 */
class SenderMail implements Sender
{
    /**
     * Метод подготавливает письмо к отправке и отправляет его
     * Возвращает true, если отправка прошла успешно
     *
     * @param \Sendmail\Message
     *
     * @return boolen
     */
    public function send(Message $message)
    {
        return @mail('', '', $message->getMessage(), $message->getHeaders());
    }
}
