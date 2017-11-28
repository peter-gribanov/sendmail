<?php
/**
 * SendMail package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail\Sender;

use Sendmail\Message;

/**
 * E-mail messages sent via PHP function mail().
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Mail implements SenderInterface
{
    /**
     * Send E-mail message.
     *
     * @param Message $message
     *
     * @return bool
     */
    public function send(Message $message)
    {
        return mail('', '', $message->getText(), $message->getHeaders());
    }
}
