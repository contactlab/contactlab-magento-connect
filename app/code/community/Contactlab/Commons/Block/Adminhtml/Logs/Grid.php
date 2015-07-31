<?php

/**
 * Log grid.
 */
class Contactlab_Commons_Block_Adminhtml_Logs_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Construct the block.
     */
    public function __construct($attributes = array()) {
        parent::__construct($attributes);
        $this->setId('log_id');
        $this->setDefaultSort('log_id');
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
        $collection = Mage::getModel('contactlab_commons/log')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('log_id', array(
            'header' => $this->__('ID'),
            'align' => 'left',
            'index' => 'log_id',
            'width' => 1,
            'type' => 'range',
            'renderer' => 'contactlab_commons/adminhtml_logs_renderer_default'
        ));
        $this->addColumn('created_at', array(
            'header' => $this->__('Created'),
            'align' => 'left',
            'index' => 'created_at',
            'width' => 1,
            'type' => 'datetime',
            'renderer' => 'contactlab_commons/adminhtml_logs_renderer_datetime'
        ));
        $this->addColumn('log_level', array(
            'header' => $this->__('Log level'),
            'align' => 'left',
            'index' => 'log_level',
            'width' => 1,
            'type' => 'options',
            'options' => Contactlab_Commons_Helper_Data::$LEVELS,
            'renderer' => 'contactlab_commons/adminhtml_logs_renderer_level'
                )
        );
        $this->addColumn('description', array(
            'header' => $this->__('Description'),
            'align' => 'left',
            'index' => 'description',
            'renderer' => 'contactlab_commons/adminhtml_logs_renderer_default'
        ));
    }

    /**
     * Get grid url.
     */
    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array(
                    '_current' => true
                ));
    }

    /**
     * No row url.
     */
    public function getRowUrl($item) {
        return null;
    }

}
