<?php
/**
 * Sendmail package
 *
 * @package   Sendmail
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2010, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Sendmail\Tests;

/**
 * Runkit test case
 *
 * @package Sendmail\Tests
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class RunkitTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Runkit override functions
     *
     * @var array
     */
    private static $override_functions = array();

    const BACKUP_SUFFIX = '_runkit_mocker_backup';

    /**
     * Method to call from overridden functions.
     * Calls given mock's method with given arguments.
     *
     * @param string $method
     * @param array $args
     */
    public static function call($func, array $args)
    {
        return call_user_func_array(array(self::$override_functions[$func], $func), $args);
    }

    /**
     * Mark test skipped if runkit is not enabled
     */
    protected function skipTestIfNoRunkit()
    {
        if (!extension_loaded('runkit')) {
            $this->markTestSkipped('Runkit extension is not loaded');
        }
    }
 
    /**
    * Override given functions with mock
    *
    * @param array $func_list
    *
    * @return \PHPUnit_Framework_MockObject_MockObject
    */
    protected function getRunkitMock(array $funcs)
    {
        $this->skipTestIfNoRunkit();
 
        $mock = $this->getMock('stdClass', $funcs);
 
        foreach ($funcs as $func) {
            $this->runkitOverride(
                $func,
                '',
                'return ' . __CLASS__ . "::call('{$func}', func_get_args());",
                $mock
            );
        }
 
        return $mock;
    }

    /**
     * Override function
     *
     * @param string $func
     * @param string $args
     * @param string $body
     * @param mixed $mock
     */
    protected function runkitOverride($func, $args, $body, $mock = null)
    {
        $this->skipTestIfNoRunkit();
 
        if (array_key_exists($func, self::$override_functions)) {
            throw new \RuntimeException("Function '{$func}' is marked as mocked already");
        }
        self::$override_functions[$func] = $mock;
        \runkit_function_copy($func, $func . self::BACKUP_SUFFIX);
        \runkit_function_redefine($func, $args, $body);
    }

    /**
     * Revert previously overridden function
     *
     * @param string $func
     */
    protected function runkitRevert($func)
    {
        $this->skipTestIfNoRunkit();
 
        if (!array_key_exists($func, self::$override_functions)) {
            throw new \RuntimeException("Function '{$func}' is not marked as mocked");
        }
        unset(self::$override_functions[$func]);
 
        \runkit_function_remove($func);
        \runkit_function_copy($func . self::BACKUP_SUFFIX, $func);
        \runkit_function_remove($func . self::BACKUP_SUFFIX);
    }
 
    /**
     * Revert all previously overridden functions
     */
    protected function runkitRevertAll()
    {
        foreach (array_keys(self::$override_functions) as $func) {
            $this->runkitRevert($func);
        }
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        $this->runkitRevertAll();
    }
}
