<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use AbhishekDas\ShutdownScheduler\ShutdownScheduler;

/**
 * ShutdownSchedulerTest
 *
 * Unit tests for the ShutdownScheduler class
 * 
 * @package Tests
 * @author Abhishek Das <abhishek@virtueplanet.com>
 * @license GPL-3.0
 */
class ShutdownSchedulerTest extends TestCase
{
    /**
     * Test constructor initializes properly
     */    public function testConstructor()
    {
        $scheduler = new ShutdownScheduler();
        $this->assertInstanceOf(ShutdownScheduler::class, $scheduler);
    }

    /**
     * Test registering a valid shutdown event
     */    public function testRegisterShutdownEvent()
    {
        $scheduler = new ShutdownScheduler();
        $result = $scheduler->registerShutdownEvent('test_event', function() {
            return true;
        });
        $this->assertTrue($result);
          // Use reflection to check if the callback was registered
        $reflection = new \ReflectionClass(ShutdownScheduler::class);
        $property = $reflection->getProperty('callbacks');
        $property->setAccessible(true);
        
        $callbacks = $property->getValue($scheduler);
        $this->assertArrayHasKey('test_event', $callbacks);
    }

    /**
     * Test registering an event with invalid callback
     */    public function testRegisterShutdownEventInvalidCallback()
    {
        $scheduler = new ShutdownScheduler();
        
        // We expect an E_USER_ERROR to be triggered
        $this->expectException(\PHPUnit\Framework\Error\Error::class);
        
        $scheduler->registerShutdownEvent('test_event', 'not_a_callable');
    }

    /**
     * Test registering an event with invalid event name
     */    public function testRegisterShutdownEventInvalidEventName()
    {
        $scheduler = new ShutdownScheduler();
        
        // We expect an E_USER_ERROR to be triggered
        $this->expectException(\PHPUnit\Framework\Error\Error::class);
        
        $scheduler->registerShutdownEvent(123, function() {});
    }

    /**
     * Test unregistering a shutdown event
     */    public function testUnregisterShutdownEvent()
    {
        $scheduler = new ShutdownScheduler();
        
        // First register an event
        $scheduler->registerShutdownEvent('test_event', function() {
            return true;
        });
        
        // Then unregister it
        $scheduler->unregisterShutdownEvent('test_event');
          // Use reflection to verify it was removed
        $reflection = new \ReflectionClass(ShutdownScheduler::class);
        $property = $reflection->getProperty('callbacks');
        $property->setAccessible(true);
        
        $callbacks = $property->getValue($scheduler);
        $this->assertArrayNotHasKey('test_event', $callbacks);
    }

    /**
     * Test the callRegisteredShutdown method
     */    public function testCallRegisteredShutdown()
    {
        $scheduler = new ShutdownScheduler();
        
        // Create a test variable to track callback execution
        $testValue = 0;
        
        // Register a callback that modifies the test variable
        $scheduler->registerShutdownEvent('test_event', function() use (&$testValue) {
            $testValue = 42;
        });
        
        // Call the shutdown function manually
        $scheduler->callRegisteredShutdown();
        
        // Verify the callback was executed
        $this->assertEquals(42, $testValue);
    }

    /**
     * Test registering multiple shutdown events
     */    public function testRegisterMultipleShutdownEvents()
    {
        $scheduler = new ShutdownScheduler();
        
        // Register multiple events
        $scheduler->registerShutdownEvent('event1', function() {
            return 1;
        });
        
        $scheduler->registerShutdownEvent('event2', function() {
            return 2;
        });
          // Use reflection to verify they were registered
        $reflection = new \ReflectionClass(ShutdownScheduler::class);
        $property = $reflection->getProperty('callbacks');
        $property->setAccessible(true);
        
        $callbacks = $property->getValue($scheduler);
        $this->assertCount(2, $callbacks);
        $this->assertArrayHasKey('event1', $callbacks);
        $this->assertArrayHasKey('event2', $callbacks);
    }

    /**
     * Test registering an event with parameters
     */    public function testRegisterShutdownEventWithParameters()
    {
        $scheduler = new ShutdownScheduler();
        
        // Create a test variable
        $result = '';
        
        // Register a callback with parameters
        $scheduler->registerShutdownEvent('parameterized_event', function($param1, $param2) use (&$result) {
            $result = $param1 . $param2;
        }, 'Hello, ', 'World!');
        
        // Call the shutdown function manually
        $scheduler->callRegisteredShutdown();
        
        // Verify the parameters were correctly passed and used
        $this->assertEquals('Hello, World!', $result);
    }
}
