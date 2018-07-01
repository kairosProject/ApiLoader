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
 * @category Api_Loader_Loader
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
namespace KairosProject\ApiLoader\Loader;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use KairosProject\ApiLoader\Event\QueryBuildingEventInterface;
use KairosProject\ApiController\Event\ProcessEventInterface;
use Psr\Log\LoggerInterface;

/**
 * Abstract ApiLoader
 *
 * This abstract class define the funcamental workflow of the ApiLoaders.
 *
 * @category Api_Loader_Loader
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
abstract class AbstractApiLoader implements ApiLoaderInterface
{
    /**
     * Collection event name.
     *
     * This constant define the collection event name.
     *
     * @var string
     */
    public const COLLECTION_EVENT_NAME = 'on_collection_query_building';

    /**
     * Item event name.
     *
     * This constant define the item event name.
     *
     * @var string
     */
    public const ITEM_EVENT_NAME = 'on_item_query_building';

    /**
     * Event key storage.
     *
     * This constant define the storage key of the query result.
     *
     * @var string
     */
    public const EVENT_KEY_STORAGE = 'query_storage';

    /**
     * Configure for collection.
     *
     * This constant define the method name to configure the query building for collection.
     *
     * @var string
     */
    private const CONFIGURE_FOR_COLLETION = 'configureQueryForCollection';

    /**
     * Configure for item.
     *
     * This constant define the method name to configure the query building for item.
     *
     * @var string
     */
    private const CONFIGURE_FOR_ITEM = 'configureQueryForItem';

    /**
     * Logger.
     *
     * The application logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Collection event name.
     *
     * This property store the event name dispatched to configure the collection loading query.
     *
     * @var string
     */
    private $collectionEventName = self::COLLECTION_EVENT_NAME;

    /**
     * Item event name.
     *
     * This property store the event name dispatched to configure the item loading query.
     *
     * @var string
     */
    private $itemEventName = self::ITEM_EVENT_NAME;

    /**
     * Event key storage.
     *
     * This property store the key name where the query result will be stored. This element is applyed to the original
     * process event.
     *
     * @var string
     */
    private $eventKeyStorage = self::EVENT_KEY_STORAGE;

    /**
     * AbstractApiLoader constructor.
     *
     * The default AbstractApiLoader constructor will store the logger and the configuration elements.
     *
     * @param LoggerInterface $logger              The application logger.
     * @param string          $collectionEventName The event name dispatched to configure the collection loading query.
     * @param string          $itemEventName       The event name dispatched to configure the item loading query.
     * @param string          $eventKeyStorage     The key name where the query result will be stored.
     *
     * @return void
     */
    public function __construct(
        LoggerInterface $logger,
        string $collectionEventName = self::COLLECTION_EVENT_NAME,
        string $itemEventName = self::ITEM_EVENT_NAME,
        string $eventKeyStorage = self::EVENT_KEY_STORAGE
    ) {
        $this->logger = $logger;
        $this->collectionEventName = $collectionEventName;
        $this->itemEventName = $itemEventName;
        $this->eventKeyStorage = $eventKeyStorage;
    }

    /**
     * Load item
     *
     * Load a specific item, then insert the result inside the original event. Extensions methods will be fired,
     * using the event dispatcher dispatch method.
     *
     * @param ProcessEventInterface    $processEvent The original processEvent instance
     * @param string                   $eventName    The current event name
     * @param EventDispatcherInterface $dispatcher   The original event dispatcher
     *
     * @return void
     */
    public function loadItem(
        ProcessEventInterface $processEvent,
        string $eventName,
        EventDispatcherInterface $dispatcher
    ) : void {
        $this->logger->debug(
            'Item loading started',
            [
                'from event' => $eventName,
                'dispatched event' => $this->itemEventName,
                'storage key' => $this->eventKeyStorage
            ]
        );
        $this->load($processEvent, $eventName, $dispatcher, self::CONFIGURE_FOR_ITEM, $this->itemEventName);
    }

    /**
     * Load collection
     *
     * Load a collection of item, then insert the result inside the original event. Extensions methods will be fired,
     * using the event dispatcher dispatch method.
     *
     * @param ProcessEventInterface    $processEvent The original processEvent instance
     * @param string                   $eventName    The current event name
     * @param EventDispatcherInterface $dispatcher   The original event dispatcher
     *
     * @return void
     */
    public function loadCollection(
        ProcessEventInterface $processEvent,
        string $eventName,
        EventDispatcherInterface $dispatcher
    ) : void {
        $this->logger->debug(
            'Collection loading started',
            [
                'from event' => $eventName,
                'dispatched event' => $this->itemEventName,
                'storage key' => $this->eventKeyStorage
            ]
        );
        $this->load($processEvent, $eventName, $dispatcher, self::CONFIGURE_FOR_COLLETION, $this->collectionEventName);
    }

    /**
     * Load.
     *
     * This method load an item or a collection of item, depending of the input parameters.
     *
     * @param ProcessEventInterface    $processEvent      The original event.
     * @param string                   $eventName         The original event name.
     * @param EventDispatcherInterface $dispatcher        The event dispatcher.
     * @param string                   $configurationType The configuration method to be used. Use
     *                                                    self::CONFIGURE_FOR_* constants.
     * @param string                   $dispatchEvent     The dispatching event name.
     *
     * @return void
     */
    private function load(
        ProcessEventInterface $processEvent,
        string $eventName,
        EventDispatcherInterface $dispatcher,
        string $configurationType,
        string $dispatchEvent
    ) : void {
        $queryBuildingEvent = $this->getQueryBuildingEvent($processEvent);
        $this->instanciateQueryBuilder($queryBuildingEvent, $eventName, $dispatcher);
        $this->{$configurationType}($queryBuildingEvent, $eventName, $dispatcher);

        $dispatcher->dispatch($dispatchEvent, $queryBuildingEvent);

        $processEvent->setParameter(
            $this->eventKeyStorage,
            $this->executeQuery(
                $queryBuildingEvent,
                $eventName,
                $dispatcher
            )
        );
    }

    /**
     * Execute query.
     *
     * This method execute the query and return the result.
     *
     * @param QueryBuildingEventInterface $event      The query building event
     * @param string                      $eventName  The current event name
     * @param EventDispatcherInterface    $dispatcher The current event dispatcher
     *
     * @return void
     */
    abstract protected function executeQuery(
        QueryBuildingEventInterface $event,
        string $eventName,
        EventDispatcherInterface $dispatcher
    );

    /**
     * Configure query for collection.
     *
     * This method configure the query builder to load a collection of item.
     *
     * @param QueryBuildingEventInterface $event      The query building event
     * @param string                      $eventName  The current event name
     * @param EventDispatcherInterface    $dispatcher The current event dispatcher
     *
     * @return void
     */
    abstract protected function configureQueryForCollection(
        QueryBuildingEventInterface $event,
        string $eventName,
        EventDispatcherInterface $dispatcher
    ) : void;

    /**
     * Configure query for item.
     *
     * This method configure the query builder to load a specific item.
     *
     * @param QueryBuildingEventInterface $event      The query building event
     * @param string                      $eventName  The current event name
     * @param EventDispatcherInterface    $dispatcher The current event dispatcher
     *
     * @return void
     */
    abstract protected function configureQueryForItem(
        QueryBuildingEventInterface $event,
        string $eventName,
        EventDispatcherInterface $dispatcher
    ) : void;

    /**
     * Instanciate query builder.
     *
     * Create a new instance of query builder and inject it inside the QueryBuildingEvent instance.
     *
     * @param QueryBuildingEventInterface $event      The query building event
     * @param string                      $eventName  The current event name
     * @param EventDispatcherInterface    $dispatcher The current event dispatcher
     *
     * @return void
     */
    abstract protected function instanciateQueryBuilder(
        QueryBuildingEventInterface $event,
        string $eventName,
        EventDispatcherInterface $dispatcher
    ) : void;

    /**
     * Get query building event.
     *
     * Return a new instance of QueryBuildingEvent to be used during the workflow.
     *
     * @param ProcessEventInterface $originalEvent The original event
     *
     * @return QueryBuildingEventInterface
     */
    abstract protected function getQueryBuildingEvent(
        ProcessEventInterface $originalEvent
    ) : QueryBuildingEventInterface;
}
