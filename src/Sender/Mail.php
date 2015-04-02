<?php
/**
 * SendMail package
 *
 * @package   SendMail
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail\Sender;

use Sendmail\Sender\SenderInterface;
use Sendmail\Message;

/**
 * Класс отправки E-mail сообщений через PHP функцию mail()
 *
 * @package SendMail\Sender
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Mail implements SenderInterface
{
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
        return mail('', '', $message->getMessage(), $message->getHeaders());
    }
}
