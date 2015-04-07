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
 * E-mail messages sent via PHP function mail()
 *
 * @package SendMail\Sender
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Mail implements SenderInterface
{
    /**
     * Send E-mail message
     *
     * @param \Sendmail\Message $message
     *
     * @return boolean
     */
    public function send(Message $message)
    {
        return mail('', '', $message->getText(), $message->getHeaders());
    }
}
