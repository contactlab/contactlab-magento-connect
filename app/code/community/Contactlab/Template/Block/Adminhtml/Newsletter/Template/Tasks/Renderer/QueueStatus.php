<?php

class Contactlab_Template_Block_Adminhtml_Newsletter_Template_Tasks_Renderer_QueueStatus
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    private $_helper;

    public function __construct()
    {
        $this->_helper = Mage::helper('contactlab_template');
    }

    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $count = $this->_getQueueRecipients($row->getQueueId());
        if (!$count) {
            return '<em>None</em>';
        } else if ($count === 1) {
            $label = 'recipient';
        } else {
            $label = 'recipients';
        }
        return sprintf('<a title="%s" href="%s">%s [%s %s]</a>',
            $this->_helper->__('Queue detail'),
            $this->_getQueueDetailUrl($row->getQueueId()),
            $this->_getQueueStatus($row->getQueueStatus()),
            $count, $label);
    }

    /**
     * Get queue status.
     * @param $status
     */
    private function _getQueueStatus($status)
    {
        switch ($status) {
            case Mage_Newsletter_Model_Queue::STATUS_NEVER:
                return $this->_helper->__('Never');
            case Mage_Newsletter_Model_Queue::STATUS_SENDING:
                return $this->_helper->__('Sending');
            case Mage_Newsletter_Model_Queue::STATUS_CANCEL:
                return $this->_helper->__('Cancel');
            case Mage_Newsletter_Model_Queue::STATUS_SENT:
                return $this->_helper->__('Sent');
            case Mage_Newsletter_Model_Queue::STATUS_PAUSE:
                return $this->_helper->__('Pause');
        }
    }

    /**
     * Get Recipient information
     * @param $queueId
     * @return array
     */
    private function _getQueueRecipients($queueId)
    {
        $resource = Mage::getSingleton('core/resource');
        $adapter = $resource->getConnection('core_read');
        $select = $adapter->select();
        $select
            ->from($resource->getTableName('newsletter/queue_link'),
                new Zend_Db_Expr('count(1)'))
            ->where('queue_id = ?', $queueId);

        return $adapter->fetchOne($select);
    }

    /**
     * @param $queueId
     * @return string
     */
    private function _getQueueDetailUrl($queueId)
    {
        return Mage::helper('adminhtml')
            ->getUrl('contactlab_template/adminhtml_newsletter_template_tasks/detail',
                array('queue_id' => $queueId));
    }
}