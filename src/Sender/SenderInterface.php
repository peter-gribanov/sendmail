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

use Sendmail\Message;

/**
 * Интерфейс отправителей сообщений
 *
 * @package Sendmail
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface SenderInterface
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
