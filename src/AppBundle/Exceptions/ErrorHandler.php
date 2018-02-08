<?php

namespace AppBundle\Exceptions;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ErrorHandler.
 */
final class ErrorHandler
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * Errors collection
     *
     * @var array
     */
    protected $errors;


    /**
     * ErrorHandler constructor.
     * @param Logger $logger
     * @param KernelInterface $kernel
     */
    public function __construct(Logger $logger, KernelInterface $kernel)
    {
        $this->logger = $logger;
        $this->kernel = $kernel;
        $this->errors = array();
    }

    /**
     * @param \Exception $e
     * @param string $prefixmsg
     */
    public function warn(\Exception $e, $prefixmsg = 'Be careful next time.')
    {
        $this->add('warn', $prefixmsg, $e->getMessage());

        if ($this->kernel->isDebug()) {
            $this->logger->error(sprintf(
                '%s Details: {message: %s, file: %s, line: %d}',
                $prefixmsg,
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));
        } else {
            $this->logger->error(sprintf('%s %s', $prefixmsg, $e->getMessage()));
        }
    }

    /**
     * @param \Exception $e
     * @param string $prefixmsg
     */
    public function error(\Exception $e, $prefixmsg = 'Something is wrong and need our attention.')
    {
        $this->add('error', $prefixmsg, $e->getMessage());

        if ($this->kernel->isDebug()) {
            $this->logger->error(sprintf(
                '%s Details: {message: %s, file: %s, line: %d}',
                $prefixmsg,
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));
        } else {
            $this->logger->error(sprintf('%s %s', $prefixmsg, $e->getMessage()));
        }
    }

    /**
     * @param \Exception $e
     * @param string $prefixmsg
     */
    public function critical(\Exception $e, $prefixmsg = 'Things get\'s pretty bad.')
    {
        $this->add('critical', $prefixmsg, $e->getMessage());

        if ($this->kernel->isDebug()) {
            $this->logger->critical(sprintf(
                '%s Details: {message: %s, file: %s, line: %d}',
                $prefixmsg,
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));
        } else {
            $this->logger->critical(sprintf('%s %s', $prefixmsg, $e->getMessage()));
        }
    }

    /**
     * @param \Exception $e
     * @param string     $prefixmsg
     * @param Logger     $logger
     * @param bool       $debug
     *
     * @deprecated Deprecated call using public static function.
     */
    public static function handleError(\Exception $e, $prefixmsg = 'Something is wrong and need our attention.', Logger $logger, $debug = true)
    {
        trigger_error(sprintf('The method %s is deprecated public static call.', __METHOD__), E_USER_DEPRECATED);

        if ($debug) {
            $logger->error(sprintf(
                '%s Details: {message: %s, file: %s, line: %d}',
                $prefixmsg,
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));
        } else {
            $logger->error(sprintf('%s %s', $prefixmsg, $e->getMessage()));
        }
    }

    /**
     * @param \Exception $e
     * @param string     $prefixmsg
     * @param Logger     $logger
     * @param bool       $debug
     *
     * @deprecated Deprecated call using public static function.
     */
    public static function handleCritical(\Exception $e, $prefixmsg = 'Things get\'s pretty bad.', Logger $logger, $debug = true)
    {
        trigger_error(sprintf('The method %s is deprecated public static call.', __METHOD__), E_USER_DEPRECATED);

        if ($debug) {
            $logger->critical(sprintf(
                '%s Details: {message: %s, file: %s, line: %d}',
                $prefixmsg,
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));
        } else {
            $logger->critical(sprintf('%s %s', $prefixmsg, $e->getMessage()));
        }
    }

    /**
     * @param $message
     * @param Logger $logger
     * @param $type
     *
     * @deprecated Deprecated call using public static function.
     */
    public static function handleCustom($message, Logger $logger, $type)
    {
        trigger_error(sprintf('The method %s is deprecated public static call.', __METHOD__), E_USER_DEPRECATED);

        $logger->$type($message);
    }

    /**
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * Adds any type of error
     *
     * @param $type
     * @param $error
     * @param string $description
     */
    protected function add($type, $error, $description = '')
    {
        if (!isset($this->errors[$type])) {
            $this->errors[$type] = array();
        }

        $this->errors[$type][] = array(
            'message' => $error,
            'description' => $description,
        );
    }

    /**
     * Return true in case of errors
     *
     * @param string|null $type
     * @return bool
     */
    public function hasErrors($type = null) : bool
    {
        if ($type !== null && !in_array($type, $this->errors)) {
            return false;
        }

        if ($type !== null && in_array($type, $this->errors)) {
            return count($this->errors[$type]) > 0;
        }

        $types = array_keys($this->errors);
        $count = 0;
        foreach ($types as $type) {
            $count = count($this->errors[$type]);
        }

        return $count > 0;
    }

    /**
     * Gets errors array
     *
     * @param string|null $type
     * @return array
     */
    public function getErrors($type = null) : array
    {
        if ($type !== null && !in_array($type, $this->errors)) {
            return array();
        }

        if ($type !== null) {
            return $this->errors[$type];
        }

        return $this->errors;
    }
}
