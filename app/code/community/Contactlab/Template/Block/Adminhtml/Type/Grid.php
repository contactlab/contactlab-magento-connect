<?php

/** Adminhtml type grid. */
class Contactlab_Template_Block_Adminhtml_Type_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Construct the block.
     *
     * @param array $attributes = array()
     */
    public function __construct($attributes = array()) {
        parent::__construct($attributes);
        $this->setId('entity_id');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Setting collection to show.
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('contactlab_template/type')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid.
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => $this->__('Id'),
            'align' => 'left',
            'index' => 'entity_id',
            'width' => 1,
            'type' => 'range'
        ));

        $this->addColumn('name', array(
            'header' => $this->__('Template type name'),
            'index' => 'name',
            'type' => 'text'
        ));
        $this->addColumn('template_type_code', array(
            'header' => $this->__('Code of template type'),
            'index' => 'template_type_code',
            'width' => 1,
            'type' => 'text'
        ));
        $this->addColumn('is_system', array(
            'header' => $this->__('Is a system defined type'),
            'index' => 'is_system',
            'type' => 'options',
            'width' => 1,
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
        ));
        $this->addColumn('dnd_period', array(
            'header' => $this->__('Dnd period length'),
            'index' => 'dnd_period',
            'width' => 1,
            'type' => 'text'
        ));
        $this->addColumn('dnd_mail_number', array(
            'header' => $this->__('Dnd max mail number'),
            'index' => 'dnd_mail_number',
            'width' => 1,
            'type' => 'text'
        ));


        $this->addColumn('is_cron_enabled', array(
            'header' => $this->__('Enable for cron execution'),
            'index' => 'is_cron_enabled',
            'type' => 'options',
            'width' => 1,
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
        ));

        $this->addColumn('configure', array(
            'header' => $this->__('Configure default values'),
            'type' => 'actions',
            'width' => 1,
            'renderer' => 'contactlab_template/adminhtml_type_grid_renderer_configureLink'
        ));
    }

    /**
     * Get grid url.
     *
     * @return string
     */
    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Get row url.
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
