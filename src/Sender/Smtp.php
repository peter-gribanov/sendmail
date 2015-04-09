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

use Sendmail\Sender\SenderInterface;
use Sendmail\Message;
use Sendmail\Sender\Smtp\Dialogue;

/**
 * SMTP/ESMTP sender RFC 5321
 *
 * @package Sendmail\Sender
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Smtp implements SenderInterface
{
    /**
     * The connection timeout
     *
     * @var integer
     */
    protected $timeout = -1;

    /**
     * Use a secure connection
     *
     * @var boolean
     */
    protected $secure = false;

    /**
     * SMTP server
     *
     * @var string
     */
    protected $server = '';

    /**
     * Connection port
     *
     * @var integer
     */
    protected $port = 25;

    /**
     * Username for authorization
     *
     * @var string
     */
    protected $auth_username = '';

    /**
     * Password for authorization
     *
     * @var string
     */
    protected $auth_password = '';


    /**
     * Construct
     *
     * @param string $server
     * @param integer $port
     * @param string $username
     * @param string $password
     */
    public function __construct($server, $port = 25, $username = '', $password = '')
    {
        $this->server = $server;
        $this->port = $port;
        $this->auth_username = $username;
        $this->auth_password = $password;
    }

    /**
     * Send E-mail message
     *
     * @param \Sendmail\Message $message
     *
     * @return boolean
     */
    public function send(Message $message)
    {
        $dialogue = new Dialogue($this->server, $this->port, $this->timeout);

        // SMTP-session is established, can send requests

        // is ESMTP?
        if ($dialogue->call('EHLO '.$_SERVER['HTTP_HOST'])) {
            // open the TLS connection if need
            if ($this->secure) {
                $dialogue->call('STARTTLS');
                // after starting TLS need to say again EHLO
                $dialogue->call('EHLO '.$_SERVER['HTTP_HOST'], true);
            }
        } else {
            $dialogue->call('HELO '.$_SERVER['HTTP_HOST'], true);
        }

        // authorizing
        if ($this->auth_username && $this->auth_password) {
            $dialogue->call('AUTH LOGIN');
            $dialogue->call(base64_encode($this->auth_username));
            $dialogue->call(base64_encode($this->auth_password), true);
        }

        $dialogue->call('MAIL FROM: '.$message->getFrom(), true);
        $dialogue->call('RCPT TO: '.$message->getTo(), true);
        $dialogue->call('DATA');

        // point at the end means the end of the message
        $dialogue->call(
            $message->getHeaders()."\r\n\r\n".
            $message->getText()."\r\n.",
            true
        );

        // completes data transmission and close SMTP connect
        $result = $dialogue->call('QUIT');
        $dialogue->end();

        return $result;
    }

    /**
     * Set timeout connecting to the server
     *
     * @param integer $timeout
     *
     * @return \Sendmail\Sender\Smtp
     */
    public function setTimeOut($timeout)
    {
        if ($timeout > 0) {
            $this->timeout = $timeout;
        }
        return $this;
    }

    /**
     * Start secure connection
     *
     * @param boolean $secure
     *
     * @return \Sendmail\Sender\Smtp
     */
    public function setSecure($secure = true)
    {
        $this->secure = $secure;
        return $this;
    }
}
