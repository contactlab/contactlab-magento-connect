<?php

/**
 * Task runner for sending a single email.
 */
class Contactlab_Transactional_Model_Task_SendEmailRunner extends Contactlab_Commons_Model_Task_Abstract {
    /**
     * Run the task (calls the helper).
     */
    protected function _runTask() {
        $email = unserialize($this->getTask()->getTaskData());
        if (is_object($email)) {
            try {
                if ($email instanceof Contactlab_Transactional_Model_Zend_Mail) {
                    $email->reallySend($this->getTask());
                } else {
                    Mage::helper("contactlab_commons")->logCrit(
                        sprintf("Email is of type '%s'", get_class($email)));
                    throw new Zend_Exception(sprintf("Email is of type '%s'", get_class($email)));
                }
            } catch (Exception $e) {
                Mage::helper("contactlab_commons")->logCrit(
                    sprintf("Problems during sending mail with serialized '%s'", $this->getTask()->getTaskData()));
                throw $e;
            }
        } else {
            Mage::helper("contactlab_commons")->logCrit(
                sprintf("Can't unserialize data '%s'", $this->getTask()->getTaskData()));
            throw new Zend_Exception("Can't unserialize data, result is " . gettype($email));
        }
        return $this;
    }

    /**
     * Get the name.
     */
    public function getName() {
        return "Send a transactional email";
    }
}
