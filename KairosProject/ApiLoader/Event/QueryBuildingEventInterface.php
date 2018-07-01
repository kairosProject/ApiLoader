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

use KairosProject\ApiController\Event\ProcessEventInterface;

/**
 * QueryBuilding event interface
 *
 * This interface define the basic methods of the queryBuildingEvent.
 *
 * @category Api_Loader_Event
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
interface QueryBuildingEventInterface
{
    /**
     * Get query builder.
     *
     * Return the stored query builder instance, in order to modify it's configuration.
     *
     * @return mixed
     */
    public function getQuery();

    /**
     * Set query builder.
     *
     * Set the internal query builder instance or override the current one.
     *
     * @param mixed $queryBuilder The query builder to store inside the current instance
     *
     * @return $this
     */
    public function setQuery($queryBuilder) : QueryBuildingEventInterface;

    /**
     * Get ProcessEvent.
     *
     * Return the original ProcessEventInterface instance in order to access the original request informations.
     *
     * @return ProcessEventInterface
     */
    public function getProcessEvent() : ProcessEventInterface;
}
