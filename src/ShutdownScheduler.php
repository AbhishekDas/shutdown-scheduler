<?php

/**
 * ShutdownScheduler
 *
 * A shutdown scheduler that allows you to register shutdown events with callbacks 
 * that will be executed when the script execution ends.
 * It provides methods to register and unregister events.
 *
 * @package ShutdownScheduler
 * @author  Abhishek Das <abhishek@virtueplanet.com>
 * @license GPL-3.0
 */

namespace AbhishekDas\ShutdownScheduler;

/**
 * ShutdownScheduler class
 *
 * This class allows you to register shutdown events with callbacks that will be executed
 * when the script execution ends. It provides methods to register and unregister events.
 */
class ShutdownScheduler
{
    /**
     * @var array $callbacks
     *
     * An associative array to store registered shutdown callbacks.
     * The keys are event names, and the values are arrays containing the callback and its parameters.
     */
    private $callbacks;

    /**
     * Constructor for the ShutdownScheduler class.
     *
     * Initializes the callbacks array and registers the shutdown function.
     */
    public function __construct()
    {
        $this->callbacks = [];

        register_shutdown_function([$this, 'callRegisteredShutdown']);
    }

    /**
     * Registers a shutdown event with a callback.
     *
     * @param  string   $eventName The name of the event.
     * @param  callable $callback  The callback function to be executed on shutdown.
     * @return bool Returns true on success, false on failure.
     */
    public function registerShutdownEvent()
    {
        $arguments = func_get_args();

        if (empty($arguments)) {
            trigger_error('No arguments passed to ' . __FUNCTION__ . ' method', E_USER_ERROR);

            return false;
        }

        if (!is_callable($arguments[1])) {
            trigger_error('Invalid callback passed to the ' . __FUNCTION__ . ' method', E_USER_ERROR);

            return false;
        }

        if (!is_string($arguments[0])) {
            trigger_error('Invalid event name passed to the ' . __FUNCTION__ . ' method', E_USER_ERROR);

            return false;
        }

        $eventName = $arguments[0];

        unset($arguments[0]);

        $this->callbacks[$eventName] = $arguments;

        return true;
    }

    /**
     * Unregisters a shutdown event by its name.
     *
     * @param string $eventName The name of the event to unregister.
     */
    public function unregisterShutdownEvent(string $eventName)
    {
        unset($this->callbacks[$eventName]);
    }

    /**
     * Calls all registered shutdown callbacks.
     *
     * This method is automatically registered as a shutdown function and will be called
     * when the script execution ends.
     */
    public function callRegisteredShutdown()
    {
        foreach ($this->callbacks as $arguments) {
            $callback = array_shift($arguments);

            call_user_func_array($callback, $arguments);
        }
    }
}
