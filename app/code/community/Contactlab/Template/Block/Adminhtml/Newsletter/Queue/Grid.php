<?php

/** Adminhtml newsletter queue grid block. */
class Contactlab_Template_Block_Adminhtml_Newsletter_Queue_Grid extends Mage_Adminhtml_Block_Newsletter_Queue_Grid {

    /**
     * Prepare columns, add task id column.
     * return $this;
     */
    protected function _prepareColumns() {
        $rv = parent::_prepareColumns();

        $this->addColumnAfter('task_id', array(
            'header'    => Mage::helper('contactlab_commons')->__('Task'),
            'index'		=> 'task_id',
            'type'      => 'range',
            'width'     => '200px',
            'renderer'  => 'contactlab_commons/adminhtml_tasks_renderer_task',
        ), 'subscribers_total');

        $this->sortColumnsByOrder();

        return $rv;
    }
}
