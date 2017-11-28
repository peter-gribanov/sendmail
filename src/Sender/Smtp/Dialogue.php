<?php
/**
 * Sendmail package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail\Sender\Smtp;

/**
 * SMTP dialogue.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Dialogue
{
    /**
     * Dialogue text.
     *
     * @var string
     */
    protected $log = '';

    /**
     * SMTP server connection.
     *
     * @var resource
     */
    protected $connect;

    /**
     * Construct.
     *
     * @throws \Exception
     *
     * @param string $server
     * @param int $port
     * @param int $timeout
     */
    public function __construct($server, $port, $timeout = -1)
    {
        $errno = 0;
        $errstr = '';
        if ($timeout > 0) {
            $this->connect = fsockopen($server, $port, $errno, $errstr, $timeout);
        } else {
            $this->connect = fsockopen($server, $port, $errno, $errstr);
        }

        if (!is_resource($this->connect)) {
            if ($errno === 0 || !$errstr) {
                $errstr = 'Failed connect to: '.$server.':'.$port;
            }

            throw new \Exception($errstr, $errno);
        }

        // saving welcome message
        $this->log = fgets($this->connect, 4096);
    }

    /**
     * Sends the request and returns a response.
     *
     * @throws \Exception
     *
     * @param string $request
     * @param bool $verify
     *
     * @return bool
     */
    public function call($request, $verify = false)
    {
        fwrite($this->connect, $request."\r\n");

        $response = fread($this->connect, 4096);
        $this->log .= $request."\r\n".$response;

        if ($verify && $response[0] != 2) {
            throw new Exception($this->end());
        }

        return $response[0] != 2;
    }

    /**
     * Get dialogue text.
     *
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * End dialogue.
     *
     * @return self
     */
    public function end()
    {
        fclose($this->connect);

        return $this;
    }

    /**
     * Destruct.
     */
    public function __destruct()
    {
        $this->end();
    }
}
