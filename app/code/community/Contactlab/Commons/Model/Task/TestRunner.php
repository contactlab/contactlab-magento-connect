<?php

/**
 * Test executer, for debug.
 */
class Contactlab_Commons_Model_Task_TestRunner extends Contactlab_Commons_Model_Task_Abstract {

    /**
     * Run the test task.
     */
    protected function _runTask() {
        // $args = $this->getArguments();
        // $doIt = count($args) > 0 && $args[0];
        // if (!$doIt) {
        // throw new Zend_Exception("Errore nel test");
        // }
        sleep(1);
        return "Ok";
    }

    /**
     * The name of the task.
     */
    public function getName() {
        return "Test task";
    }

}
