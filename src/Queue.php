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

use Sendmail\Sender\SenderInterface;
use Sendmail\Message;

/**
 * E-mail queue
 *
 * @package Sendmail
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Queue implements \IteratorAggregate, \Countable
{
    /**
     * List messages
     *
     * @var array
     */
    protected $messages = array();

    /**
     * Mail sender
     *
     * @var \Sendmail\Sender\SenderInterface
     */
    protected $sender;

    /**
     * Construct
     *
     * @param \Sendmail\Sender\SenderInterface $sender
     */
    public function __construct(SenderInterface $sender)
    {
        $this->sender = $sender;
    }

    /**
     * Get iterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->messages);
    }

    /**
     * Возвращает количество сообщений
     *
     * @return integer
     */
    public function count()
    {
        return count($this->messages);
    }

    /**
     * Clear list messages
     *
     * @return \Sendmail\Collection
     */
    public function clear()
    {
        unset($this->messages);
        $this->messages = array();
        return $this;
    }

    /**
     * Add message
     *
     * @param \Sendmail\Message $message
     *
     * @return \Sendmail\Collection
     */
    public function add(Message $message)
    {
        $this->messages[] = clone $message;
        return $this;
    }

    /**
     * Get sender
     *
     * @return \Sendmail\Sender\SenderInterface
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Add a message addressed to list recipients
     *
     * @param array $recipients
     * @param \Sendmail\Message $message
     *
     * @return \Sendmail\Collection
     */
    public function notify(array $recipients, Message $message)
    {
        $message = clone $message;
        foreach ($recipients as $recipient) {
            $this->add($message->setTo($recipient));
        }
        return $this;
    }

    /**
     * Send all messages
     *
     * @return boolean
     */
    public function send()
    {
        foreach ($this as $message) {
            if (!$this->sender->send($message)) {
                return false;
            }
        }
        return true;
    }
}
