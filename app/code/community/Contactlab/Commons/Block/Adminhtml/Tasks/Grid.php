<?php

/**
 * Task grid.
 */
class Contactlab_Commons_Block_Adminhtml_Tasks_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Construct the block.
     * @param array $attributes
     */
    public function __construct($attributes = array()) {
        parent::__construct($attributes);
        $this->setId('task_id');
        $this->setDefaultSort('task_id');
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
        $collection = Mage::getModel('contactlab_commons/task')
                ->getCollection()
                ->loadVisibleTasks()
                ->addExpressionFieldToSelect("retries", "concat(number_of_retries, '/', max_retries)", array());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('task_id', array(
            'header' => $this->__('ID'),
            'align' => 'left',
            'index' => 'task_id',
            'width' => 1
        ));
        $this->addColumn('task_code', array(
            'header' => $this->__('Code'),
            'align' => 'left',
            'index' => 'task_code',
            'width' => 1
        ));
        $this->addColumn('created_at', array(
            'header' => $this->__('Created'),
            'align' => 'left',
            'index' => 'created_at',
            'width' => 1,
            'type' => 'datetime'
        ));
        $this->addColumn('planned_at', array(
            'header' => $this->__('Planned'),
            'align' => 'left',
            'index' => 'planned_at',
            'width' => 1,
            'type' => 'datetime'
        ));
        $this->addColumn('store_id', array(
            'header' => $this->__('Store'),
            'align' => 'left',
            'index' => 'store_id',
            'renderer' => 'contactlab_commons/adminhtml_tasks_renderer_store'
        ));
        $this->addColumn('description', array(
            'header' => $this->__('Description'),
            'align' => 'left',
            'index' => 'description'
        ));
        $this->addColumn('retries', array(
            'header' => $this->__('Retries'),
            'align' => 'left',
            'index' => 'retries',
            'sortable' => false,
            'filter' => false,
            'width' => 1
        ));
        $this->addColumn('model_name', array(
            'header' => $this->__('Task'),
            'align' => 'left',
            'index' => 'model_name',
            'renderer' => 'contactlab_commons/adminhtml_tasks_renderer_model'
        ));
        $this->addColumn('status', array(
            'header' => $this->__('Status'),
            'align' => 'left',
            'index' => 'status',
            'width' => 100,
            'type' => 'options',
            'options' => Contactlab_Commons_Model_Task::$statusesNames,
            'renderer' => 'contactlab_commons/adminhtml_events_renderer_status'
        ));
        $this->addColumn('actions_buttons', array(
            'header' => $this->__('Actions'),
            'align' => 'left',
            'index' => 'task_id',
            'width' => 135,
            'renderer' => 'contactlab_commons/adminhtml_tasks_renderer_actions'
        ));
        /*$actions = array();
		$this->_addActionToArray('cancel', "Cancel task", $actions);
		$this->_addActionToArray('suspend', "Suspend task", $actions);
		$this->_addActionToArray('unsuspend', "Unsuspend task", $actions);
		$this->_addActionToArray('retry', "Retry", $actions);
		$this->_addActionToArray('run', "Run now", $actions);
        $this->addColumn('actions', array(
            'header' => $this->__('Actions'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getTaskId',
            'actions' => $actions,
            'filter' => false,
            'sortable' => false
        ));*/
    }

	/**
	 * @deprecated
	 */
    private function _addActionToArray($code, $label, array &$array) {
    	if (!$this->_isAllowed($code)) {
    		return;
    	}
    	$array[] = array(
            'caption' => $this->__($label),
            'url' => array(
                'base' => '*/*/' . $code
            ),
            'field' => 'task_id'
        );
    }

    /** Is action allowed? */
    private function _isAllowed($value) {
    	return Mage::helper('contactlab_commons')->isAllowed('tasks', $value);
    }

    /**
     * 	Grid mass action.
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('task_id');
        $this->getMassactionBlock()->setFormFieldName('task_id');
		if (!$this->_isAllowed('cancel') && !$this->_isAllowed('suspend') && !$this->_isAllowed('unsuspend')
        	&& !$this->_isAllowed('retry') && !$this->_isAllowed('run')) { 
				return;
		}
		if ($this->_isAllowed('cancel')) {
	        $this->getMassactionBlock()->addItem('cancelTask', array(
	            'label' => $this->__('Cancel task'),
	            'url' => $this->getUrl('*/*/setStatus', array('task_status' => 1)),
	            'confirm' => $this->__('Are you sure?')
	        ));
		}
		if ($this->_isAllowed('delete')) {
	        $this->getMassactionBlock()->addItem('deleteTask', array(
	            'label' => $this->__('Delete task'),
	            'url' => $this->getUrl('*/*/massDelete'),
	            'confirm' => $this->__('Are you sure?')
	        ));
		}
	    if ($this->_isAllowed('suspend')) {
	        $this->getMassactionBlock()->addItem('suspendTask', array(
	            'label' => $this->__('Suspend task'),
	            'url' => $this->getUrl('*/*/setStatus', array('task_status' => 2)),
	            'confirm' => $this->__('Are you sure?')
	        ));
	    }
        if ($this->_isAllowed('unsuspend')) {
	        $this->getMassactionBlock()->addItem('unsuspendTask', array(
	            'label' => $this->__('Unsuspend task'),
	            'url' => $this->getUrl('*/*/setStatus', array('task_status' => 3)),
	            'confirm' => $this->__('Are you sure?')
	        ));
        }
        if ($this->_isAllowed('retry')) {
	        $this->getMassactionBlock()->addItem('retry', array(
	            'label' => $this->__('Retry'),
	            'url' => $this->getUrl('*/*/setStatus', array('task_status' => 4)),
	            'confirm' => $this->__('Are you sure?')
	        ));
        }
        if ($this->_isAllowed('run')) {
	        $this->getMassactionBlock()->addItem('runNow', array(
	            'label' => $this->__('Run task'),
	            'url' => $this->getUrl('*/*/setStatus', array('task_status' => 5)),
	            'confirm' => $this->__('Are you sure?')
	        ));
		}
        return $this;
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
     * @param $item
     * @return string
     */
    public function getRowUrl($item) {
        return $this->getUrl('*/contactlab_commons_events/', array(
                    'id' => $item->getId()
        ));
    }
}
