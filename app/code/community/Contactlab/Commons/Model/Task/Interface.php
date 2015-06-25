<?php

/**
 * Task interface.
 */
interface Contactlab_Commons_Model_Task_Interface {

    /**
     * Run the task.
     */
    function run();

    /**
     * Get task description.
     */
    function getName();

    /**
     * Get default retry after.
     */
    function getDefaultRetriesInterval();

    /** Default retries interval. */
    function getDefaultMaxRetries();

    /** Mermory limit. */
    function getMemoryLimit();
}
