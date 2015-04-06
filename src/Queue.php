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
     * The object has not been created
     *
     * @var integer
     */
    const STATUS_NOT_INIT = 0;

    /**
     * Created and ready to go
     *
     * @var integer
     */
    const STATUS_READY = 1;

    /**
     * Sends a message
     *
     * @var integer
     */
    const STATUS_SENDS = 2;

    /**
     * List messages
     *
     * @var array
     */
    protected $messages = array();

    /**
     * Queue status
     *
     * @var integer
     */
    protected $status = self::STATUS_NOT_INIT;

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
        $this->status = self::STATUS_READY;
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
        $this->messages[] = $message;
        return $this;
    }

    /**
     * Add a message addressed to list recipients
     *
     * @param array $recipients
     * @param \Sendmail\Message $message
     *
     * @return \Sendmail\Collection
     */
    public function notification(array $recipients, Message $message)
    {
        foreach ($recipients as $recipient) {
            $this->messages[] = $message->setTo($recipient);
        }
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
     * Send all messages
     *
     * @return boolean
     */
    public function send()
    {
        foreach ($this as $message) {
            // wait the queue is released
            while ($this->status != self::STATUS_READY) {}

            $this->status = self::STATUS_SENDS;
            $result = $this->sender->send($message);
            $this->status = self::STATUS_READY;

            if (!$result) {
                return false;
            }
        }
        return true;
    }
}
