<?php

/**
 * Task model.
 *
 * @method int getTaskId()
 * @method int getStoreId()
 * @method Contactlab_Commons_Model_Task setStoreId($value)
 * @method Contactlab_Commons_Model_Task setTaskCode($value)
 * @method int getMaxRetries()
 * @method int getNumberOfRetries()
 * @method getTaskData()
 * @method setAutoDelete(bool $value)
 * @method setSuppressNotification(bool $value)
 *
 * @method Contactlab_Commons_Model_Task setTaskData($value)
 *
 * @method Contactlab_Commons_Model_Task setDescription($value)
 * @method Contactlab_Commons_Model_Task setPlannedAt($value)
 *
 * @method bool getPreventDelete()
 * @method string getStatus()
 */
class Contactlab_Commons_Model_Task extends Mage_Core_Model_Abstract {

    /**
     * Events waiting to be saved (if the task has not an id yet).
     */
    private $_events = array();

    /**
     * Lazy loaded user id.
     */
    private static $_userId = - 1;

    /* New task */

    const STATUS_NEW = "new";

    /* Failed, waiting for a new retry */
    const STATUS_WAITING_FOR_RETRY = "waiting_for_retry";

    /* Running */
    const STATUS_RUNNING = "running";

    /* Success */
    const STATUS_CLOSED = "closed";

    /* Error occured after all retries */
    const STATUS_FAILED = "failed";

    /* User suspended execution */
    const STATUS_SUSPENDED = "suspended";

    /* User cancelled execution */
    const STATUS_CANCELLED = "cancelled";

    /* Hidden (foreign key) */
    const STATUS_HIDDEN = "hidden";

    /**
     * Statuses.
     */
    public static $statuses = array(
        self::STATUS_NEW => array(
            "description" => "New task",
            "color" => "#333333",
            "style" => "font-weight: bold"
        ),
        self::STATUS_WAITING_FOR_RETRY => array(
            "description" => "Failed, waiting for a new retry",
            "color" => "orange",
            "style" => "font-weight: bold"
        ),
        self::STATUS_RUNNING => array(
            "description" => "Running",
            "color" => "darkgreen",
            "style" => "font-weight: bold"
        ),
        self::STATUS_CLOSED => array(
            "description" => "Success",
            "color" => "lightgray",
            "style" => "font-weight: bold"
        ),
        self::STATUS_FAILED => array(
            "description" => "Error occured after all retries",
            "color" => "red",
            "style" => "font-weight: bold"
        ),
        self::STATUS_SUSPENDED => array(
            "description" => "User suspended execution",
            "color" => "orange",
            "style" => "font-weight: bold"
        ),
        self::STATUS_CANCELLED => array(
            "description" => "User cancelled execution",
            "color" => "peru",
            "style" => "font-weight: bold"
        ),
        self::STATUS_HIDDEN => array(
            "description" => "Closed / Hidden",
            "color" => "lightgrey",
            "style" => "font-weight: normal"
        )
    );

    /**
     * Statuses names.
     */
    public static $statusesNames = array();

    /**
     * Constructor.
     */
    public function _construct() {
        $this->_init("contactlab_commons/task");
    }

    /**
     * Init static vars.
     */
    public static function initStaticVars() {
        foreach (self::$statuses as $key => &$value) {
            $value ['description'] = Mage::helper("contactlab_commons")->__($value ['description']);
            self::$statusesNames [$key] = $value ['description'];
        }
    }

    /**
     * Add an event.
     * 
     * @return Contactlab_Commons_Task_Event
     */
    public function addEvent($description = NULL, $sendAlert = 0) {
        Mage::log(sprintf("%-10s [task-%s] %-6s - %s", "TaskEvent", $this->getTaskId(), $sendAlert
                ? "Alert" : "None", $description), null, "contactlab.log", true);
        $event = Mage::getModel("contactlab_commons/task_event");
        $event->setCreatedAt(Mage::getModel("core/date")->date());

        if (!empty($description)) {
            $event->setDescription($description);
        }
        $event->setTaskStatus($this->getStatus());
        if ($sendAlert) {
            $event->setSendAlert(1);
            if (!$this->getSuppressNotification()) {
                // Changed for backward compatibility to 1.6
                Mage::helper('contactlab_commons/tasks')
                    ->addCriticalMessage(sprintf("[Task %s]: %s", $this->getTaskId(), $description));
            } else {
                $this->setSuppressNotification(false);
            }
        }
        if (self::_getBackendUserId()) {
            $event->setUserId(self::_getBackendUserId());
        }

        if (!$this->hasTaskId()) {
            $this->_events [] = $event;
        } else {
            $event->setTaskId($this->getTaskId());
            $event->save();
        }

        return $event;
    }

    /** For Mage 1.7 or newer. */
    private function _useCoreAddCritical() {
        return Mage::helper("contactlab_commons")->isMageSameOrNewerOf(1, 7);
    }

    /**
     * Add new message (Backporting)
     *
     * @param string $title
     * @param string $description
     * @return $this
     */
    private function _addCritical($title, $description) {
        $t = Mage::getModel('adminnotification/inbox');
        $date = date('Y-m-d H:i:s');
        $t->parse(array(array(
            'severity'    => Mage::helper('adminnotification')->__('critical'),
            'date_added'  => $date,
            'title'       => $title,
            'description' => $description,
            'url'         => "",
            'internal'    => true
        )));
        return $this;
    }


    /**
     * On set model name, reset interval and retries.
     * @param $model
     * @return Contactlab_Commons_Model_Task
     */
    public function setModelName($model) {
        $rv = parent::setModelName($model);
        $this->_resetIntervalAndMaxRetries();
        return $rv;
    }

	/** Reset interval and retries. */
    private function _resetIntervalAndMaxRetries() {
        $inst = $this->_getRunnerInstance();
        if ($inst) {
            if ($inst->getDefaultRetriesInterval() > 0) {
                $this->setRetriesInterval($inst->getDefaultRetriesInterval());
            }
            if ($inst->getDefaultMaxRetries() > 0) {
                $this->setMaxRetries($inst->getDefaultMaxRetries());
            }
        }
    }

    /**
     * Get backend user id.
     */
    private static function _getBackendUserId() {
        if (self::$_userId < 0) {
            $user = Mage::getSingleton('admin/session')->getUser();
            if ($user && $user->getId()) {
                self::$_userId = $user->getId();
            } else {
                return NULL;
            }
        }
        return self::$_userId;
    }

    /**
     * Before task save, validate.
     */
    public function _beforeSave() {
        $date = Mage::getModel('core/date')->gmtDate();
        if ($this->isObjectNew() && !$this->getCreatedAt()) {
            $this->setCreatedAt($date);
        } else {
            $this->setUpdatedAt($date);
        }

        parent::_beforeSave();
        if (!$this->hasTaskCode()) {
            throw new Zend_Exception("Must specify a task code");
        }
        if (!$this->hasModelName()) {
            throw new Zend_Exception("Must specify a model name");
        }
        if (!$this->hasStatus()) {
            $this->addEvent(sprintf("Task created: \"%s\"",
                    $this->getDescription()))->setTaskStatus(self::STATUS_NEW);
            $this->setStatus(self::STATUS_NEW, false);
        }
    }

    /**
     * Speaking to string method.
     */
    public function __toString() {
        return sprintf("[%d] %s, created in %s (%d/%d) with status %s",
            $this->getTaskId(),
            $this->getDescription(),
            $this->getCreatedAt(),
            $this->getProgressValue(),
            $this->getMaxValue(),
            $this->getStatus());
    }

    /**
     * View task events url.
     */
    public function getEventsUrl() {
        return Mage::helper('adminhtml')->getUrl('adminhtml/contactlab_commons_events/', array(
                    'id' => $this->getTaskId()
        ));
    }

    /**
     * Set status and add an event.
     */
    public function setStatus($value, $createEvent = true) {
        parent::setStatus($value);
        if ($createEvent) {
            $this->addEvent("Status changed to $value");
        }
        return $this;
    }

    /**
     * After task save, set id and save all events.
     */
    public function _afterSave() {
        parent::_afterSave();
        foreach ($this->_events as $event) {
            if (!$event->getTaskId()) {
                $event->setTaskId($this->getTaskId());
                $event->save();
            }
        }
        $this->_events = array();
    }

    /**
     * Suspend the task.
     */
    public function suspend() {
        if (!$this->canSuspend()) {
            $this->addEvent("Tried to suspend a {$this->getStatus()} task", true);
            throw new Exception("Tried to suspend a {$this->getStatus()} task");
        }
        $this->setStatus(self::STATUS_SUSPENDED);
        return $this;
    }

    /** Can suspend? */
	public function canSuspend() {
		return !$this->isClosed() && !$this->isSuspended() && !$this->isRunning();
	}

    /** Can delete? */
	public function canDelete() {
		return $this->isNew() || $this->isClosed() || $this->isCancelled() || $this->isSuspended() || $this->isFailed();
	}


    /**
     * Cancel the task.
     */
    public function cancel() {
        if (!$this->canCancel()) {
            throw new Exception("Can't cancel a {$this->getStatus()} task");
            return $this;
        }
        $this->setStatus(self::STATUS_CANCELLED);
        return $this;
    }

	public function canCancel() {
		return !$this->isCancelled() && !$this->isClosed() && !$this->isFailed();
	}

    /**
     * Reset the task.
     */
    public function reset() {
        $this->setStatus(self::STATUS_NEW);
		$this->_resetIntervalAndMaxRetries();
        $this->setNumberOfRetries(0);
		if ($this->getProgressValue() > 0) {
			$this->setProgressValue(0);
		}
        $this->setPlannedAt(NULL);
        return $this;
    }

    /**
     * Unsuspend the task.
     */
    public function unsuspend() {
        if (!$this->canUnsuspend()) {
            $this->addEvent("Tried to unsuspend a {$this->getStatus()} task", true);
            throw new Exception("Tried to unsuspend a {$this->getStatus()} task");
        }
        return $this->setStatus(self::STATUS_NEW);
    }

    /**
     * Hide the task.
     */
    private function _hide() {
        return $this->setStatus(self::STATUS_HIDDEN);
    }

    /**
     * Return true if can unsuspend
     */
	public function canUnsuspend() {
		return $this->isSuspended();
	}

    /**
     * Run the task
     */
    public function runTask(array &$args = array()) {
        try {
            $this->setReturnValue($this->_runTask($args));
            $this->save();

            if ($this->isClosed() && $this->getAutoDelete()) {
                $this->delete();
            }
            return $this;
        } catch (Exception $e) {
            $this->addEvent("Got an error: " . $e->getMessage());
            $this->save();
            throw $e;
        }
    }

	public function canRun() {
		return !$this->isCancelled() && !$this->isClosed() && !$this->isSuspended() && !$this->isRunning() && !$this->isFailed();
	}

    /**
     * Run the task
     */
    private function _runTask(array &$args) {
        if ($this->isCancelled()) {
            $this->addEvent("Tried to run a cancelled task", true);
            return false;
        }
        if ($this->isClosed()) {
            $this->addEvent("Tried to run a closed task", true);
            return false;
        }
        if ($this->isSuspended()) {
            $this->addEvent("Tried to run a suspended task", true);
            return false;
        }
        if ($this->isRunning()) {
            $this->addEvent("Tried to run a running task", true);
            return false;
        }
        if ($this->isFailed()) {
            $this->addEvent("Tried to run a failed task", true);
            return false;
        }

        $this->setStatus(self::STATUS_RUNNING, false);
        $this->addEvent("Start task " . $this->getModelName());
        $this->save();
        $runner = $this->_getRunnerInstance();
        try {
            $runner->setTask($this);
            $runner->setArguments($args);

            $memoryLimit = ini_get("memory_limit");
            $ml = $runner->getMemoryLimit();
            if (!empty($ml)) {
                ini_set("memory_limit", $ml);
                $this->addEvent(sprintf("Memory limit settet to %s (%s). Was %s",
                        $runner->getMemoryLimit(), ini_get("memory_limit"), $memoryLimit));
            }
            $rv = $runner->run();
            if (!empty($ml)) {
                gc_collect_cycles();
                ini_set("memory_limit", $memoryLimit);
            }

            if (is_object($rv)) {
                $rv = get_class($rv);
            }

            // Prevent close?
            if (!$this->getPreventClose()) {
                $this->addEvent("Task " . $this->getModelName() . " ended with status $rv");
                $this->setNumberOfRetries($this->getNumberOfRetries() + 1);
                $this->setStatus(self::STATUS_CLOSED, false);
            } else {
                $this->addEvent("Task " . $this->getModelName() . " ended with status $rv, not closed");
            }
        } catch (Exception $e) {
            $this->addEvent("Task got an error \"{$e->getMessage()}\"", true);
            $this->retryOrFail();
            return false;
        }
        return $rv;
    }

    /**
     * Retry or fail.
     *
     * @return $this
     */
    public function retryOrFail() {
        $this->setNumberOfRetries($this->getNumberOfRetries() + 1);
        if ($this->getNumberOfRetries() < $this->getMaxRetries()) {
            $this->setStatus(self::STATUS_WAITING_FOR_RETRY);
            if ($this->hasData('retries_interval') && $this->getRetriesInterval() > 0) {
                $date = Mage::getModel('core/date');
                $this->setPlannedAt($date->gmtTimestamp()
                        + ($this->getRetriesInterval() * 60))->save();
            }
        } else {
            $this->setStatus(self::STATUS_FAILED)->save();
        }
        return $this;
    }

    /**
     * Instance of runner model.
     * @return Contactlab_Commons_Model_Task_Interface
     */
    private function _getRunnerInstance() {
        /* @var $rv Contactlab_Commons_Model_Task_Interface */
        $rv = Mage::getModel($this->getModelName());
        $rv->setTask($this);
        return $rv;
    }

    /**
     * Is the task new?
     */
    public function isNew() {
        return !$this->hasStatus() || $this->getStatus() === self::STATUS_NEW;
    }

    /**
     * Is the task waiting for retry?
     */
    public function isWaitingForRetry() {
        return $this->getStatus() === self::STATUS_WAITING_FOR_RETRY;
    }

    /**
     * Is the task running?
     */
    public function isRunning() {
        return $this->getStatus() === self::STATUS_RUNNING;
    }

    /**
     * Is the task closed?
     */
    public function isClosed() {
        return $this->getStatus() === self::STATUS_CLOSED;
    }

    /**
     * Is the task failed?
     */
    public function isFailed() {
        return $this->getStatus() === self::STATUS_FAILED;
    }

    public function setFailed() {
        return $this->setStatus(self::STATUS_FAILED)->save();
    }

    /**
     * Is the task suspended?
     */
    public function isSuspended() {
        return $this->getStatus() === self::STATUS_SUSPENDED;
    }

    /**
     * Is the task hidden?
     */
    public function isHidden() {
        return $this->getStatus() === self::STATUS_HIDDEN;
    }

    /**
     * Is the task cancelled?
     */
    public function isCancelled() {
        return $this->getStatus() === self::STATUS_CANCELLED;
    }

    /** Set progress value. */
	public function setProgressValue($value) {
		$rv = parent::setProgressValue($value);
		$this->save();
		return $rv;
	}

    /**
     * Set max value.
     * @param string $value
     * @return Contactlab_Commons_Model_Task
     * @throws Exception
     */
	public function setMaxValue($value) {
		$rv = parent::setMaxValue($value);
		parent::setProgressValue(0);
		$this->save();
		return $rv;
	}

    /** Check if module is enabled. */
    public function isEnabled() {
        return $this->getConfigFlag("contactlab_commons/global/enabled");
    }

    /**
     * Get config for store id.
     * @param string $path
     * @return mixed
     */
    public function getConfig($path) {
        return Mage::getStoreConfig($path, $this->getStoreId());
    }

    /**
     * Get config for store id.
     * @param string $path
     * @return bool
     */
    public function getConfigFlag($path) {
        return Mage::getStoreConfigFlag($path, $this->getStoreId());
    }

    /** Delete (or hide) the task. */
    public function delete() {
        Mage::dispatchEvent('contactlab_commons_task_delete_before', array(
            'task' => $this));
        if ($this->getPreventDelete()) {
            return $this->_hide()->save();
        }
        return parent::delete();
    }

}

Contactlab_Commons_Model_Task::initStaticVars();
