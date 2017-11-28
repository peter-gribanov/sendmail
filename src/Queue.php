<?php
/**
 * Sendmail package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail;

use Sendmail\Sender\SenderInterface;

/**
 * E-mail queue.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Queue implements \IteratorAggregate, \Countable
{
    /**
     * List messages.
     *
     * @var array
     */
    protected $messages = array();

    /**
     * Mail sender.
     *
     * @var SenderInterface
     */
    protected $sender;

    /**
     * @param SenderInterface $sender
     */
    public function __construct(SenderInterface $sender)
    {
        $this->sender = $sender;
    }

    /**
     * Get iterator.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->messages);
    }

    /**
     * Возвращает количество сообщений.
     *
     * @return int
     */
    public function count()
    {
        return count($this->messages);
    }

    /**
     * Clear list messages.
     *
     * @return self
     */
    public function clear()
    {
        unset($this->messages);
        $this->messages = array();

        return $this;
    }

    /**
     * Add message.
     *
     * @param Message $message
     *
     * @return self
     */
    public function add(Message $message)
    {
        $this->messages[] = clone $message;

        return $this;
    }

    /**
     * Get sender.
     *
     * @return SenderInterface
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Add a message addressed to list recipients.
     *
     * @param array $recipients
     * @param Message $message
     *
     * @return self
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
     * Send all messages.
     *
     * @return bool
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
