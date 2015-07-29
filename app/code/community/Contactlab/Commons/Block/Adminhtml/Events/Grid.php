<?php

/**
 * Events grid.
 */
class Contactlab_Commons_Block_Adminhtml_Events_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Construct the block.
     */
    public function __construct($attributes = array()) {
        parent::__construct($attributes);
        $this->setId('task_event_id');
        $this->setDefaultSort('task_event_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Setting collection to show
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection() {
        $taskId = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('contactlab_commons/task_event')
            ->getCollection()
                ->addFieldToSelect('*')
                ->join('contactlab_commons/task',
                    "`contactlab_commons/task`.task_id = main_table.task_id",
                    'description as task_description');
        $collection->getSelect()->joinLeft(array(
            'user' => $collection->getTable('admin/user')
                ), "user.user_id = main_table.user_id",
                'if(firstname is null, \'Cron\', concat(firstname, \' \', lastname)) as user_description');
        if (is_numeric($taskId)) {
            $collection->addFieldToFilter('main_table.task_id', $taskId);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('task_event_id', array(
            'header' => $this->__('ID'),
            'align' => 'left',
            'index' => 'task_event_id',
            'width' => 1,
            'type' => 'range'
        ));
        $this->addColumn('task_description', array(
            'header' => $this->__('Task'),
            'align' => 'left',
            'index' => 'task_description',
            'sortable' => false,
            'width' => 300
        ));
        $this->addColumn('created_at', array(
            'header' => $this->__('Created'),
            'align' => 'left',
            'index' => 'created_at',
            'width' => 1
        ));
        $this->addColumn('user_description', array(
            'header' => $this->__('User'),
            'align' => 'left',
            'index' => 'user_description',
            'width' => 1
        ));
        $this->addColumn('description', array(
            'header' => $this->__('Task event description'),
            'align' => 'left',
            'index' => 'description',
            'width' => 400
        ));
        $this->addColumn('task_status', array(
            'header' => $this->__('Task status'),
            'align' => 'left',
            'index' => 'task_status',
            'width' => 1,
            'renderer' => 'contactlab_commons/adminhtml_events_renderer_status'
        ));
        $this->addColumn('send_alert', array(
            'header' => $this->__('Show as alert'),
            'align' => 'left',
            'index' => 'send_alert',
            'width' => 1,
            'renderer' => 'contactlab_commons/adminhtml_events_renderer_alert'
        ));
    }

    /**
     * Grid url.
     */
    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array(
                    '_current' => true
                ));
    }

    /**
     * Row url.
     */
    public function getRowUrl($item) {
        return null;
    }

}
