<?php
declare(strict_types=1);
/**
 * This file is part of the kairos project.
 *
 * As each files provides by the CSCFA, this file is licensed
 * under the MIT license.
 *
 * PHP version 7.2
 *
 * @category Api_Loader_Event
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
namespace KairosProject\ApiLoader\Event;

use Symfony\Component\EventDispatcher\Event;
use KairosProject\ApiController\Event\ProcessEventInterface;

/**
 * QueryBuilding event
 *
 * This class is the default implementation of the QueryBuildingEventInterface.
 *
 * @category Api_Loader_Event
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
class QueryBuildingEvent extends Event implements QueryBuildingEventInterface
{
    /**
     * Process event.
     *
     * This property store the original ProcessEventInterface instance, in order to offer access to the original
     * request informations.
     *
     * @var ProcessEventInterface
     */
    private $processEvent;

    /**
     * Query builder.
     *
     * This property store an instance of query builder. This builder will be configured during a process.
     *
     * @var mixed
     */
    private $query;

    /**
     * QueryBuildingEvent constructor.
     *
     * This method initialize the QueryBuildingEvent instance by storing the original ProcessEventInterface instance.
     *
     * @param ProcessEventInterface $processEvent The original ProcessEvent instance
     *
     * @return void
     */
    public function __construct(ProcessEventInterface $processEvent)
    {
        $this->processEvent = $processEvent;
    }

    /**
     * Get ProcessEvent.
     *
     * Return the original ProcessEventInterface instance in order to access the original request informations.
     *
     * @return ProcessEventInterface
     */
    public function getProcessEvent() : ProcessEventInterface
    {
        return $this->processEvent;
    }

    /**
     * Get query builder.
     *
     * Return the stored query builder instance, in order to modify it's configuration.
     *
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set query builder.
     *
     * Set the internal query builder instance or override the current one.
     *
     * @param mixed $queryBuilder The query builder to store inside the current instance
     *
     * @return $this
     */
    public function setQuery($queryBuilder) : QueryBuildingEventInterface
    {
        $this->query = $queryBuilder;
        return $this;
    }
}
