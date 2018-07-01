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

use KairosProject\ApiController\Event\ProcessEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * ApiLoader interface
 *
 * This interface define the basic methods of the ApiLoader.
 *
 * @category Api_Loader_Loader
 * @package  Kairos_Project
 * @author   matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license  MIT <https://opensource.org/licenses/MIT>
 * @link     http://cscfa.fr
 */
interface ApiLoaderInterface
{
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
    ) : void;

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
    ) : void;
}
