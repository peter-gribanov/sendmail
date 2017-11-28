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
 * Sender interface
 *
 * @package Sendmail
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface SenderInterface
{
    /**
     * Send E-mail message
     *
     * @param Message $message
     *
     * @return boolean
     */
    public function send(Message $message);
}
