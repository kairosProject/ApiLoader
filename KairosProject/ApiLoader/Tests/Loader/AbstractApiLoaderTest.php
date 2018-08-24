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
 * @category Api_Loader_Loader_Test
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
namespace KairosProject\ApiLoader\Tests\Loader;

use KairosProject\Tests\AbstractTestClass;
use KairosProject\ApiLoader\Loader\AbstractApiLoader;
use KairosProject\ApiController\Event\ProcessEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use KairosProject\ApiLoader\Event\QueryBuildingEvent;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * AbstractApiLoader test
 *
 * This class is used to validate the AbstractApiLoader instance.
 *
 * @category Api_Loader_Loader_Test
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
class AbstractApiLoaderTest extends AbstractTestClass
{
    /**
     * Configuration provider.
     *
     * This method return a set of AbstractApiLoader configuration.
     *
     * @return array
     */
    public function configurationProvider()
    {
        return [
            [
                AbstractApiLoader::COLLECTION_EVENT_NAME,
                AbstractApiLoader::ITEM_EVENT_NAME,
                AbstractApiLoader::EVENT_KEY_STORAGE
            ],
            [
                'collection_event',
                'item_event',
                'store'
            ]
        ];
    }

    /**
     * Test loadCollection.
     *
     * This method validate the KairosProject\ApiLoader\Loader\AbstractApiLoader::loadCollection method.
     *
     * @param string $collectionEventName A collection event name
     * @param string $itemEventName       An item event name
     * @param string $eventKeyStorage     An event key storage
     *
     * @return       void
     * @dataProvider configurationProvider
     */
    public function testLoadCollection(string $collectionEventName, string $itemEventName, string $eventKeyStorage)
    {
        $logger = $this->createMock(LoggerInterface::class);
        $processEvent = $this->createMock(ProcessEventInterface::class);
        $eventName = 'get_collection';
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $queryEvent = $this->createMock(QueryBuildingEvent::class);
        $queryResult = $this->createMock(\stdClass::class);

        $instance = $this->getMockForAbstractClass(
            $this->getTestedClass(),
            [
                $logger,
                $collectionEventName,
                $itemEventName,
                $eventKeyStorage
            ]
        );

        $this->configureInstance(
            $instance,
            $processEvent,
            $queryEvent,
            $dispatcher,
            $queryResult,
            $eventName,
            'configureQueryForCollection',
            'executeCollectionQuery'
        );

        $this->getInvocationBuilder($dispatcher, $this->once(), 'dispatch')
            ->with(
                $this->equalTo($collectionEventName),
                $this->identicalTo($queryEvent)
            );

        $this->getInvocationBuilder($processEvent, $this->once(), 'setParameter')
            ->with(
                $this->equalTo($eventKeyStorage),
                $this->identicalTo($queryResult)
            );

        $instance->loadCollection($processEvent, $eventName, $dispatcher);
    }

    /**
     * Test loadItem.
     *
     * This method validate the KairosProject\ApiLoader\Loader\AbstractApiLoader::loadItem method.
     *
     * @param string $collectionEventName A collection event name
     * @param string $itemEventName       An item event name
     * @param string $eventKeyStorage     An event key storage
     *
     * @return       void
     * @dataProvider configurationProvider
     */
    public function testLoadItem(string $collectionEventName, string $itemEventName, string $eventKeyStorage)
    {
        $logger = $this->createMock(LoggerInterface::class);
        $processEvent = $this->createMock(ProcessEventInterface::class);
        $eventName = 'get_item';
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $queryEvent = $this->createMock(QueryBuildingEvent::class);
        $queryResult = $this->createMock(\stdClass::class);

        $instance = $this->getMockForAbstractClass(
            $this->getTestedClass(),
            [
                $logger,
                $collectionEventName,
                $itemEventName,
                $eventKeyStorage
            ]
        );

        $this->configureInstance(
            $instance,
            $processEvent,
            $queryEvent,
            $dispatcher,
            $queryResult,
            $eventName,
            'configureQueryForItem',
            'executeItemQuery'
        );

        $this->getInvocationBuilder($dispatcher, $this->once(), 'dispatch')
            ->with(
                $this->equalTo($itemEventName),
                $this->identicalTo($queryEvent)
            );

        $this->getInvocationBuilder($processEvent, $this->once(), 'setParameter')
            ->with(
                $this->equalTo($eventKeyStorage),
                $this->identicalTo($queryResult)
            );

        $instance->loadItem($processEvent, $eventName, $dispatcher);
    }

    /**
     * Test item error.
     *
     * This method validate the KairosProject\ApiLoader\Loader\AbstractApiLoader::loadItem method in case of empty
     * result.
     *
     * @return void
     */
    public function testItemError()
    {
        $processEvent = $this->createMock(ProcessEventInterface::class);
        $eventName = 'get_collection';
        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $instance = $this->getEmptyItemLoader();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('Item not found from loader');
        $instance->loadItem($processEvent, $eventName, $dispatcher);
    }

    /**
     * Test empty item.
     *
     * This method validate the KairosProject\ApiLoader\Loader\AbstractApiLoader::loadItem method in case of empty
     * result.
     *
     * @return void
     */
    public function testEmptyItem()
    {
        $processEvent = $this->createMock(ProcessEventInterface::class);
        $eventName = 'get_collection';
        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $instance = $this->getEmptyItemLoader();

        $exceptionProperty = $this->getClassProperty('noItemException', true);
        $exceptionProperty->setValue($instance, false);

        $instance->loadItem($processEvent, $eventName, $dispatcher);
    }

    /**
     * Get empty item loader
     *
     * This method return an empty item loader to validate the loadItem logic in case of unloaded item.
     *
     * @return MockObject
     */
    private function getEmptyItemLoader()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $queryEvent = $this->createMock(QueryBuildingEvent::class);

        $instance = $this->getMockForAbstractClass(
            $this->getTestedClass(),
            [
                $logger,
                AbstractApiLoader::COLLECTION_EVENT_NAME,
                AbstractApiLoader::ITEM_EVENT_NAME,
                AbstractApiLoader::EVENT_KEY_STORAGE
            ]
        );
        $this->getInvocationBuilder($instance, $this->once(), 'getQueryBuildingEvent')
            ->with(
                $this->anything()
            )->willReturn(
                $queryEvent
            );
        $this->getInvocationBuilder($instance, $this->once(), 'executeItemQuery')
            ->willReturn(
                null
            );

        return $instance;
    }

    /**
     * Configure instance.
     *
     * This method configure the mocked instance.
     *
     * @param MockObject $instance            The tested instance.
     * @param MockObject $processEvent        The mocked process event.
     * @param MockObject $queryEvent          The mocked query event.
     * @param MockObject $dispatcher          The mocked dispatcher.
     * @param MockObject $queryResult         The mocked query result.
     * @param string     $eventName           The dispatched event name.
     * @param string     $configurationMethod The used configuration method.
     * @param string     $executionMethod     The used execution method.
     *
     * @return void
     */
    private function configureInstance(
        MockObject $instance,
        MockObject $processEvent,
        MockObject $queryEvent,
        MockObject $dispatcher,
        MockObject $queryResult,
        string $eventName,
        string $configurationMethod,
        string $executionMethod
    ) : void {
        $this->getInvocationBuilder($instance, $this->once(), 'getQueryBuildingEvent')
            ->with(
                $this->identicalTo($processEvent)
            )->willReturn(
                $queryEvent
            );

        $this->getInvocationBuilder($instance, $this->once(), 'instanciateQueryBuilder')
            ->with(
                $this->identicalTo($queryEvent),
                $this->equalTo($eventName),
                $this->identicalTo($dispatcher)
            );
        $this->getInvocationBuilder($instance, $this->once(), $configurationMethod)
            ->with(
                $this->identicalTo($queryEvent),
                $this->equalTo($eventName),
                $this->identicalTo($dispatcher)
            );
        $this->getInvocationBuilder($instance, $this->once(), $executionMethod)
            ->with(
                $this->identicalTo($queryEvent),
                $this->equalTo($eventName),
                $this->identicalTo($dispatcher)
            )->willReturn(
                $queryResult
            );
    }

    /**
     * Get tested class
     *
     * Return the tested class name
     *
     * @return string
     */
    protected function getTestedClass() : string
    {
        return AbstractApiLoader::class;
    }
}
