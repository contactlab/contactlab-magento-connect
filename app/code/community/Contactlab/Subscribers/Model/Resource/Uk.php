<?php

/**
 * Uk resource.
 */
class Contactlab_Subscribers_Model_Resource_Uk extends Mage_Core_Model_Mysql4_Abstract {
    private $_task;
    private $_hasNotices = false;

    /**
     * Constructor.
     */
    public function _construct() {
        $this->_init("contactlab_subscribers/uk", "entity_id");
    }

    /** Remove null null records. */
    public function purge($doit = true) {
        $session = Mage::getSingleton('adminhtml/session');
        return $this->_purge($this->_getWriteAdapter(), $doit, false, $session);
    }

    /** Truncate table. */
    public function truncate() {
        $session = Mage::getSingleton('adminhtml/session');
        return $this->_truncate($this->_getWriteAdapter(), $session);
    }

    /** Update keys. */
    public function update($doit = false) {
        $wr = $this->_getWriteAdapter();
        $session = Mage::getSingleton('adminhtml/session');
        $this->_purge($wr, $doit, true, $session);
        $this->_insertExistingRecords($wr, $doit, $session);
        return $this;
    }

    /** Insert existing records. */
    private function _insertExistingRecords($adapter, $doit, $session) {
        $this->_makeCouples($adapter, $doit, $session);
        $this->_deleteDuplicatedSubscribers($adapter, $doit, $session);

        $this->_insertFromNewsletterSubscriber($adapter, $doit, $session);
        $this->_updateSubscriberId($adapter, $doit, $session);
        $this->_insertFromCustomers($adapter, $doit, $session);
        $this->_updateCustomerId($adapter, $doit, $session);
        return $this;
    }

    /** Delete duplicated subscribers. */
    private function _deleteDuplicatedSubscribers($adapter, $doit, $session) {
        // FIXME: SURE!?
        $subscribers = "newsletter/subscriber";
        $subscribersTable = $this->getTable($subscribers);
        $select = $adapter
                ->select()->from(array('s' => $subscribersTable), array('customer_id'))
                ->where("customer_id is not null and customer_id != 0")
                ->group('customer_id')
                ->having('count(1) > 1');

        $count = $this->_getCount($adapter, $select);
        if ($count === 0) {
            $this->addSuccess("No duplicated subscribers found", $session);
            return $this;
        }

        if ($doit) {
            foreach ($adapter->fetchAll($select) as $id) {
                $select = $adapter
                        ->select()->from(array('s' => $subscribersTable), array('subscriber_id'))
                        ->where("customer_id = " . $id['customer_id'])
                        ->order("subscriber_status desc");
                Mage::log($select->assemble());
                $first = true;
                foreach ($adapter->fetchAll($select) as $id) {
                    if (!$first) {
                        Mage::log("Will delete " . $id['subscriber_id']);
                        if ($doit) {
                            $select = $adapter->query("delete from $subscribersTable where subscriber_id = " . $id['subscriber_id']);
                        }
                    }
                    $first = false;
                }
            }
            $this->addError("$count duplicated subscribers removed", $session);
            return $this;
        } else {
            $this->addNotice("Would remove $count duplicated subscribers", $session);
            return $this;
        }
    }

    /**
     * Insert all record from newsletter_subscriber
     * where customer_id and subscriber_id are not in uk table
     */
    private function _insertFromNewsletterSubscriber($adapter, $doit, $session) {
        $ukTable = $this->getMainTable();
        $subscribers = "newsletter/subscriber";
        $subscribersTable = $this->getTable($subscribers);
        $select = $adapter
                ->select()->from(array('s' => $subscribersTable), array(
                    'subscriber_id' => 'subscriber_id',
                    'customer_id' => 'if(customer_id = 0, NULL, customer_id)')
                )
                ->where("customer_id not in (select customer_id from $ukTable where customer_id is not null) and subscriber_id not in (select subscriber_id from $ukTable where subscriber_id is not null)");

        $count = $this->_getCount($adapter, $select);
        if ($count === 0) {
            $this->addSuccess("No newsletter subscribers to insert", $session);
            return $this;
        }
        if ($doit) {
            // backward compatibility
            if ($this->_useCoreInsertFrom()) {
                $sql = $adapter->insertFromSelect($select, $ukTable, array('subscriber_id', 'customer_id'));
            } else {
                $sql = $this->_insertFromSelect($adapter, $select, $ukTable, array('subscriber_id', 'customer_id'));
            }
            Mage::log($sql);
            $this->addError("$count missing subscribers inserted", $session);
            return $adapter->query($sql);
        } else {
            $this->addNotice("Would insert $count missing subscribers", $session);
            return $this;
        }
    }

    /**
     * Insert all record from customers
     * where customer_id is no in uk table and it's not a newsletter subscriber
     */
    private function _insertFromCustomers($adapter, $doit, $session) {
        $ukTable = $this->getMainTable();
        $customers = "customer/entity";
        $customersTable = $this->getTable($customers);
        $subscribers = "newsletter/subscriber";
        $subscribersTable = $this->getTable($subscribers);
        $select = $adapter
                ->select()->from(array('c' => $customersTable), array('entity_id'))
                ->where("entity_id not in (select customer_id from $ukTable where customer_id is not null) and entity_id not in (select customer_id from $subscribersTable)");

        $count = $this->_getCount($adapter, $select);
        if ($count === 0) {
            $this->addSuccess("No customer to insert", $session);
            return $this;
        }
        if ($doit) {
            // backward compatibility
            if ($this->_useCoreInsertFrom()) {
                $sql = $adapter->insertFromSelect($select, $ukTable, array('customer_id'));
            } else {
                $sql = $this->_insertFromSelect($adapter, $select, $ukTable, array('customer_id'));
            }
            Mage::log($sql);
            $this->addError("$count missing customers inserted", $session);
            return $adapter->query($sql);
        } else {
            $this->addNotice("Would insert $count missing customers", $session);
            return $this;
        }
    }

    /**
     * Update all uk rows where subscriber_id is not set.
     */
    private function _updateSubscriberId($adapter, $doit, $session) {
        $ukTable = $this->getMainTable();
        $subscribers = "newsletter/subscriber";
        $subscribersTable = $this->getTable($subscribers);

        $select = $adapter
                ->select()->from(array('u' => $ukTable), array('entity_id'))
                ->join(array("s" => $subscribersTable), "s.customer_id = u.customer_id", array('subscriber_id'))
                ->where("s.subscriber_id != ifnull(u.subscriber_id, -1)");
        Mage::log($select->assemble());

        $count = $this->_getCount($adapter, $select);
        if ($count === 0) {
            $this->addSuccess("No subscriber_id to update", $session);
            return $this;
        }

        if ($doit) {
            foreach ($adapter->fetchAll($select) as $row) {
                $adapter->query("update $ukTable set subscriber_id = " .
                        $row['subscriber_id'] . " where entity_id = " . $row['entity_id']);
            }
            $this->addError("$count missing subscriber_id updated", $session);
        } else {
            $this->addNotice("Would update $count missing subscriber_id", $session);
        }
        return $this;
    }

    /**
     * Update all uk rows where customer_id is not set.
     */
    private function _updateCustomerId($adapter, $doit, $session) {
        $ukTable = $this->getMainTable();
        $subscribers = "newsletter/subscriber";
        $subscribersTable = $this->getTable($subscribers);

        $select = $adapter
                ->select()->from(array('u' => $ukTable), array('entity_id'))
                ->join(array("s" => $subscribersTable), "s.subscriber_id = u.subscriber_id", array('customer_id'))
                ->where("s.customer_id != ifnull(u.customer_id, -1) and s.customer_id != 0");

        $count = $this->_getCount($adapter, $select);
        if ($count === 0) {
            $this->addSuccess("No customer_id to update", $session);
            return $this;
        }

        if ($doit) {
            Mage::log($select->assemble());
            foreach ($adapter->fetchAll($select) as $row) {
                $adapter->query("update $ukTable set customer_id = " .
                        $row['customer_id'] . " where entity_id = " . $row['entity_id']);
            }
            $this->addError("$count missing customer_id updated", $session);
        } else {
            $this->addNotice("Would update $count missing customer_id", $session);
        }
        return $this;
    }

    /**
     * Couple rows discupled.
     */
    private function _makeCouples($adapter, $doit, $session) {
        $ukTable = $this->getMainTable();
        $subscribers = "newsletter/subscriber";
        $subscribersTable = $this->getTable($subscribers);

        $select = $adapter
                ->select()->from(array('s' => $subscribersTable), array('customer_id', 'subscriber_id'))
                ->join(array("uc" => $ukTable), "uc.customer_id = s.customer_id", array('customer_uk_id' => 'entity_id'))
                ->join(array("us" => $ukTable), "us.subscriber_id = s.subscriber_id", array('subscriber_uk_id' => 'entity_id'))
                ->where("uc.entity_id != us.entity_id");

        $count = $this->_getCount($adapter, $select);
        if ($count === 0) {
            $this->addSuccess("No customer_id/subscriber_id couple to update", $session);
            return $this;
        }

        if ($doit) {
            Mage::log($select->assemble());
            foreach ($adapter->fetchAll($select) as $row) {
                Mage::log($row);
                $adapter->query("delete from $ukTable where entity_id = " .
                        $row['customer_uk_id']);
                $adapter->query("update $ukTable set customer_id = " . $row['customer_id'] . " where entity_id = " .
                        $row['subscriber_uk_id']);
            }
            $this->addError("$count customer_id/subscriber_id couple to updated", $session);
        } else {
            $this->addNotice("Would update $count customer_id/subscriber_id couple", $session);
        }
        return $this;
    }

    private function _truncate($adapter, $session) {
        $sql = "truncate table " . $this->getMainTable();
        $adapter->query($sql);
        $this->addSuccess("Table truncated", $session);
        return $this;
    }

    /**
     * Set task.
     * @param Contactlab_Commons_Model_Task $task
     */
    public function setTask($task) {
        $this->_task = $task;
    }
    
    /** Remove null null records. */
    private function _purge($adapter, $doit, $messages, $session) {
        $select = $adapter->select()
                ->from($this->getMainTable())
                ->where('subscriber_id is null and customer_id is null');
        $count = $this->_getCount($adapter, $select);
        if ($count === 0) {
            if ($messages) {
                $this->addSuccess("No null rows found", $session);
            }
            return $this;
        }
        if ($doit) {
            if ($this->_useCoreInsertFrom()) {
                $sql = $adapter->deleteFromSelect($select, $this->getMainTable());
            } else {
                $sql = Mage::helper('contactlab_commons')
                        ->deleteFromSelect($adapter, $select, $this->getMainTable());
            }
            $adapter->query($sql);
            if ($messages) {
                $this->addError("$count null rows removed", $session);
            }
        } else if ($messages) {
            $this->addNotice("Would remove $count null rows", $session);
        }
        return $this;
    }

    private function _getCount($adapter, Varien_Db_Select $select) {
        $countSelect = $adapter->select()
                ->from(
                array("t" => $select), array("c" => new Zend_Db_Expr('count(1)')));
        foreach ($adapter->fetchAll($countSelect) as $row) {
            return intval($row['c']);
        }
        return 0;
    }

    /**
     * For backward compatibility
     */
    private function _insertFromSelect($adapter, Varien_Db_Select $select, $table, array $fields = array(), $mode = false) {
        $query = 'INSERT';
        if ($mode == $adapter::INSERT_IGNORE) {
            $query .= ' IGNORE';
        }
        $query = sprintf('%s INTO %s', $query, $adapter->quoteIdentifier($table));
        if ($fields) {
            $columns = array_map(array($adapter, 'quoteIdentifier'), $fields);
            $query = sprintf('%s (%s)', $query, join(', ', $columns));
        }

        $query = sprintf('%s %s', $query, $select->assemble());

        if ($mode == self::INSERT_ON_DUPLICATE) {
            if (!$fields) {
                $describe = $adapter->describeTable($table);
                foreach ($describe as $column) {
                    if ($column['PRIMARY'] === false) {
                        $fields[] = $column['COLUMN_NAME'];
                    }
                }
            }
            $update = array();
            foreach ($fields as $field) {
                $update[] = sprintf('%1$s = VALUES(%1$s)', $adapter->quoteIdentifier($field));
            }

            if ($update) {
                $query = sprintf('%s ON DUPLICATE KEY UPDATE %s', $query, join(', ', $update));
            }
        }

        return $query;
    }

    /** For Mage 1.6 or newer. */
    private function _useCoreInsertFrom() {
        return Mage::helper("contactlab_commons")->isMageSameOrNewerOf(1, 6);
    }

    public function hasTask() {
        return !is_null($this->_task);
    }

    /**
     * The task.
     * @return Contactlab_Commons_Model_Task
     */
    public function getTask() {
        return $this->_task;
    }

    /**
     * Add success.
     * @param type $message
     * @param Mage_Adminhtml_Model_Session $session
     */
    public function addSuccess($message, $session) {
        if ($this->hasTask() && $this->getTask()->getSuppressSuccessUk()) {
            return;
        }
        if (!is_null($session) && $session instanceof Mage_Adminhtml_Model_Session) {
            $session->addSuccess($message);
        }
        if ($this->hasTask()) {
            $this->getTask()->addEvent($message);
        }
    }

    /**
     * Add error.
     * @param type $message
     * @param Mage_Adminhtml_Model_Session $session
     */
    public function addError($message, $session) {
        if (!is_null($session) && $session instanceof Mage_Adminhtml_Model_Session) {
            $session->addError($message);
        }
        if ($this->hasTask()) {
            $this->getTask()->addEvent($message);
        }
    }

    /**
     * Add notice.
     * @param type $message
     * @param Mage_Adminhtml_Model_Session $session
     */
    public function addNotice($message, $session) {
        $this->setHasNotices();
        if (!is_null($session) && $session instanceof Mage_Adminhtml_Model_Session) {
            $session->addNotice($message);
        }
        if ($this->hasTask()) {
            $this->getTask()->addEvent($message);
        }
    }

    public function setHasNotices() {
        $this->_hasNotices = true;
    }

    public function getHasNotices() {
        return $this->_hasNotices;
    }

}
