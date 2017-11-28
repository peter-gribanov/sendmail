<?php
/**
 * Sendmail package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail\Sender;

use Sendmail\Message;
use Sendmail\Sender\Smtp\Dialogue;

/**
 * SMTP/ESMTP sender RFC 5321.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Smtp implements SenderInterface
{
    /**
     * The connection timeout.
     *
     * @var int
     */
    protected $timeout = -1;

    /**
     * Use a secure connection.
     *
     * @var bool
     */
    protected $secure = false;

    /**
     * SMTP server.
     *
     * @var string
     */
    protected $server = '';

    /**
     * Connection port.
     *
     * @var int
     */
    protected $port = 25;

    /**
     * Username for authorization.
     *
     * @var string
     */
    protected $auth_username = '';

    /**
     * Password for authorization.
     *
     * @var string
     */
    protected $auth_password = '';

    /**
     * @param string $server
     * @param int $port
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
     * Send E-mail message.
     *
     * @param Message $message
     *
     * @return bool
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
     * Set timeout connecting to the server.
     *
     * @param int $timeout
     *
     * @return self
     */
    public function setTimeOut($timeout)
    {
        if ($timeout > 0) {
            $this->timeout = $timeout;
        }

        return $this;
    }

    /**
     * Start secure connection.
     *
     * @param bool $secure
     *
     * @return self
     */
    public function setSecure($secure = true)
    {
        $this->secure = $secure;

        return $this;
    }
}
