<?php

/** Adminhtml type grid. */
class Contactlab_Template_Block_Adminhtml_Newsletter_Template_Tasks_List_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Construct the block.
     *
     * @param array $attributes = array()
     */
    public function __construct($attributes = array()) {
        parent::__construct($attributes);
        $this->setId('task_id');
        $this->setDefaultSort('task_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);
    }

    /**
     * Setting collection to show.
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection() {
        /* @var $queueCollection Contactlab_Template_Model_Resource_Newsletter_Queue_Collection */
        $queueCollection = Mage::getResourceModel("newsletter/queue_collection");
        $queueCollection->getQueueByTemplateId(Mage::registry('template_id'))->addTaskData();
        $this->setCollection($queueCollection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid.
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('task_id', array(
            'header' => $this->__('ID'),
            'align' => 'left',
            'index' => 'task_id',
            'width' => 1,
            'filter' => false,
            'order' => false,
            'type' => 'range'
        ));
        $this->addColumn('task_description', array(
            'header' => $this->__('Description'),
            'align' => 'left',
            'index' => 'task_description',
            'filter' => false,
            'order' => false,
            'type' => 'range',
            'renderer' => 'contactlab_commons/adminhtml_tasks_renderer_task'
        ));
        $this->addColumn('queue_status', array(
            'header' => $this->__('Queue status'),
            'align' => 'left',
            'index' => 'queue_status',
            'filter' => false,
            'order' => false,
            'type' => 'range',
            'renderer' => 'contactlab_template/adminhtml_newsletter_template_tasks_renderer_queueStatus'
        ));
        $this->addColumn('status', array(
            'header' => $this->__('Status'),
            'align' => 'left',
            'index' => 'status',
            'width' => 200,
            'type' => 'range',
            'filter' => false,
            'order' => false,
            'renderer' => 'contactlab_commons/adminhtml_events_renderer_status'
        ));
        $this->addColumn('task_created_at', array(
            'header' => $this->__('Created'),
            'align' => 'left',
            'index' => 'task_created_at',
            'width' => 200,
            'filter' => false,
            'order' => false,
            'type' => 'range'
        ));
        $this->addColumn('task_planned_at', array(
            'header' => $this->__('Planned'),
            'align' => 'left',
            'index' => 'task_planned_at',
            'width' => 200,
            'filter' => false,
            'order' => false,
            'type' => 'range'
        ));
    }

    /**
     * Get grid url.
     *
     * @return string
     */
    public function getGridUrl() {
        return false;
    }

    /**
     * Get row url.
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getRowUrl($row) {
        return false;
    }
}
