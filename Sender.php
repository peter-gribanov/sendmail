<?php
namespace Sendmail;

/**
 * Интерфейс отправителей сообщений
 *
 * @package   SendMail
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     18.11.2010
 * @version   1.0
 */
interface Sender
{
    /**
     * Метод подготавливает письмо к отправке и отправляет его
     * Возвращает true, если отправка прошла успешно
     *
     * @param \Sendmail\Message
     *
     * @return boolen
     */
    public function send(Message $message);
}
