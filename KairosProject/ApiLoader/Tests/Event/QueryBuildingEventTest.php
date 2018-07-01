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
 * @category Api_Loader_Event_Test
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
namespace KairosProject\ApiLoader\Tests\Event;

use KairosProject\Tests\AbstractTestClass;
use KairosProject\ApiLoader\Event\QueryBuildingEvent;
use KairosProject\ApiController\Event\ProcessEventInterface;

/**
 * QueryBuilding event test
 *
 * This class is used to validate the QueryBuildingEvent instance.
 *
 * @category Api_Loader_Event_Test
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
class QueryBuildingEventTest extends AbstractTestClass
{
    /**
     * Test constructor.
     *
     * This method validate the KairosProject\ApiLoader\Event\QueryBuildingEvent::_construct method.
     *
     * @return void
     */
    public function testConstructor()
    {
        $this->assertConstructor(
            [
                'same:processEvent' => $this->createMock(ProcessEventInterface::class)
            ]
        );
    }

    /**
     * Test getProcessEvent.
     *
     * This method validate the KairosProject\ApiLoader\Event\QueryBuildingEvent::getProcessEvent method.
     *
     * @return void
     */
    public function testGetProcessEvent()
    {
        $this->assertPublicMethod('getProcessEvent');

        $this->assertIsSimpleGetter(
            'processEvent',
            'getProcessEvent',
            $this->createMock(ProcessEventInterface::class)
        );
    }

    /**
     * Test query accessor.
     *
     * This method validate the KairosProject\ApiLoader\Event\QueryBuildingEvent query accessor.
     *
     * @return void
     */
    public function testQueryAccessor() : void
    {
        $this->assertPublicMethod('getQuery');
        $this->assertPublicMethod('setQuery');

        $this->assertHasSimpleAccessor('query', $this->createMock(\stdClass::class));
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
        return QueryBuildingEvent::class;
    }
}
