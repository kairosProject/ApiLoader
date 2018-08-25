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
    public const EVENT_KEY_STORAGE = 'data';

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
     * Execute for collection
     *
     * This constant define the method name to execute a query in order to return a collection.
     *
     * @var string
     */
    private const EXECUTE_FOR_COLLECTION = 'executeCollectionQuery';

    /**
     * Execute for item
     *
     * This constant define the method name to execute a query in order to return an item.
     *
     * @var string
     */
    private const EXECUTE_FOR_ITEM = 'loadItemOrThrowException';

    /**
     * No item exception
     *
     * Define if an excpetion have to be throwned if the load item function cannot load an item  or return null.
     *
     * @var boolean
     */
    protected const NO_ITEM_EXCEPTION = true;

    /**
     * Logger.
     *
     * The application logger.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Collection event name.
     *
     * This property store the event name dispatched to configure the collection loading query.
     *
     * @var string
     */
    protected $collectionEventName = self::COLLECTION_EVENT_NAME;

    /**
     * Item event name.
     *
     * This property store the event name dispatched to configure the item loading query.
     *
     * @var string
     */
    protected $itemEventName = self::ITEM_EVENT_NAME;

    /**
     * Event key storage.
     *
     * This property store the key name where the query result will be stored. This element is applyed to the original
     * process event.
     *
     * @var string
     */
    protected $eventKeyStorage = self::EVENT_KEY_STORAGE;

    /**
     * No item exception
     *
     * This property store the exception throwing state in case of unloaded item. This property have
     * self::NO_ITEM_EXCEPTION as default value.
     *
     * @var boolean
     */
    private $noItemException = self::NO_ITEM_EXCEPTION;

    /**
     * AbstractApiLoader constructor.
     *
     * The default AbstractApiLoader constructor will store the logger and the configuration elements.
     *
     * @param LoggerInterface $logger              The application logger.
     * @param string          $collectionEventName The event name dispatched to configure the collection loading query.
     * @param string          $itemEventName       The event name dispatched to configure the item loading query.
     * @param string          $eventKeyStorage     The key name where the query result will be stored.
     * @param boolean         $noItemException     The exception throwing state in case of unloaded item.
     *
     * @return void
     */
    public function __construct(
        LoggerInterface $logger,
        string $collectionEventName = self::COLLECTION_EVENT_NAME,
        string $itemEventName = self::ITEM_EVENT_NAME,
        string $eventKeyStorage = self::EVENT_KEY_STORAGE,
        bool $noItemException = self::NO_ITEM_EXCEPTION
    ) {
        $this->logger = $logger;
        $this->collectionEventName = $collectionEventName;
        $this->itemEventName = $itemEventName;
        $this->eventKeyStorage = $eventKeyStorage;
        $this->noItemException = $noItemException;
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
        $this->load(
            $processEvent,
            $eventName,
            $dispatcher,
            self::CONFIGURE_FOR_ITEM,
            $this->itemEventName,
            self::EXECUTE_FOR_ITEM
        );
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
        $this->load(
            $processEvent,
            $eventName,
            $dispatcher,
            self::CONFIGURE_FOR_COLLETION,
            $this->collectionEventName,
            self::EXECUTE_FOR_COLLECTION
        );
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
     * @param string                   $executionType     The execution method to be used. Use
     *                                                    self::EXECUTE_FOR_* constants.
     *
     * @return void
     */
    private function load(
        ProcessEventInterface $processEvent,
        string $eventName,
        EventDispatcherInterface $dispatcher,
        string $configurationType,
        string $dispatchEvent,
        string $executionType
    ) : void {
        $queryBuildingEvent = $this->getQueryBuildingEvent($processEvent);
        $this->instanciateQueryBuilder($queryBuildingEvent, $eventName, $dispatcher);
        $this->{$configurationType}($queryBuildingEvent, $eventName, $dispatcher);

        $dispatcher->dispatch($dispatchEvent, $queryBuildingEvent);

        $processEvent->setParameter(
            $this->eventKeyStorage,
            $this->{$executionType}(
                $queryBuildingEvent,
                $eventName,
                $dispatcher
            )
        );
    }

    /**
     * Load item or throw exception
     *
     * Load an item by executing the executeQueryItem. In case of item cannot be resolved, this method will throw a
     * RuntimeException. The exception throwing can be disabled by the $noItemException constructor argument.
     *
     * @param ProcessEventInterface    $event      The original event.
     * @param string                   $eventName  The original event name.
     * @param EventDispatcherInterface $dispatcher The event dispatcher.
     *
     * @throws                                      \RuntimeException If the item cannot be loaded
     * @return                                      mixed
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function loadItemOrThrowException(
        QueryBuildingEventInterface $event,
        string $eventName,
        EventDispatcherInterface $dispatcher
    ) {
        $queryResult = $this->executeItemQuery($event, $eventName, $dispatcher);

        if (empty($queryResult) && $this->noItemException) {
            $this->logger->notice(
                'Item not found from loader',
                [
                    'loader class' => static::class,
                    'from event' => $eventName,
                    'dispatched event' => $this->itemEventName
                ]
            );

            throw new \RuntimeException('Item not found from loader', 404);
        }

        return $queryResult;
    }

    /**
     * Execute collection query.
     *
     * This method execute the query and return the result as a collection.
     *
     * @param QueryBuildingEventInterface $event      The query building event
     * @param string                      $eventName  The current event name
     * @param EventDispatcherInterface    $dispatcher The current event dispatcher
     *
     * @return mixed
     */
    abstract protected function executeCollectionQuery(
        QueryBuildingEventInterface $event,
        string $eventName,
        EventDispatcherInterface $dispatcher
    );

    /**
     * Execute item query.
     *
     * This method execute the query and return the result as a specific item.
     *
     * @param QueryBuildingEventInterface $event      The query building event
     * @param string                      $eventName  The current event name
     * @param EventDispatcherInterface    $dispatcher The current event dispatcher
     *
     * @return mixed
     */
    abstract protected function executeItemQuery(
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
