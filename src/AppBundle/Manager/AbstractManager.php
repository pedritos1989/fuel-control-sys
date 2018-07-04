<?php

namespace AppBundle\Manager;

use AppBundle\Exceptions\ErrorHandler;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractManager
 * Abstract representation of a manager. Include commons services used in other managers.
 *
 * @package AppBundle\Manager
 */
class AbstractManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var ErrorHandler
     */
    protected $errorHandler;


    /**
     * Constructor.
     *
     * @param EntityManager $em
     * @param ErrorHandler $errorHandler
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManager $em, ErrorHandler $errorHandler, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->errorHandler = $errorHandler;
        $this->logger = $errorHandler->getLogger();
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @return ErrorHandler
     */
    public function getErrorHandler()
    {
        return $this->errorHandler;
    }

    /**
     * Begin Transaction
     */
    protected function beginTransaction()
    {
        $this->em->getConnection()->beginTransaction();
    }

    /**
     * Flush & Commit Transaction
     */
    protected function commit()
    {
        $this->em->flush();
        $this->em->getConnection()->commit();
    }

    /**
     * Rollback Transaction
     */
    protected function rollback()
    {
        $this->em->close();
        $this->em->getConnection()->rollBack();
    }

    /**
     * Dispatch Event
     *
     * @param $eventName
     * @param Event $event
     */
    protected function dispatch($eventName, Event $event)
    {
        if ($this->dispatcher->hasListeners($eventName)) {
            $this->dispatcher->dispatch($eventName, $event);
        }
    }

    /**
     * @param \PHPExcel $objPHPExcel
     * @param string $column
     * @param int $row
     * @param array $borders
     * @throws \PHPExcel_Exception
     */
    public function setBorders(\PHPExcel $objPHPExcel, string $column, int $row, array $borders)
    {
        $objPHPExcel->getActiveSheet()->getStyle(strtoupper($column) . $row)->applyFromArray($borders);
    }

}
