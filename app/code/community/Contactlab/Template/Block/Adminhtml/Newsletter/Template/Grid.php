<?php

/**
 * Adminhtml Newsletter Template Grid Form Block
 */
class Contactlab_Template_Block_Adminhtml_Newsletter_Template_Grid extends Mage_Adminhtml_Block_Newsletter_Template_Grid {
    /**
     * Prepare columns
     * @return \Contactlab_Template_Block_Adminhtml_Newsletter_Template_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('template_code',
            array('header'=>Mage::helper('newsletter')->__('ID'), 'align'=>'center', 'index'=>'template_id',
                'width' => 1));
        $this->addColumn('code',
            array(
                'header'=>Mage::helper('newsletter')->__('Template Name'),
                'index'=>'template_code',
                'width' => 1
        ));
        $this->addColumn('store_id',
            array(
                'header'=>Mage::helper('newsletter')->__('Store View'),
                'index'=>'store_id',
                'width' => 1,
                'type' => 'store',
                'renderer' => 'contactlab_commons/adminhtml_tasks_renderer_store'
            ));

        $this->addColumn('template_type_name',
            array(
                'header'=>Mage::helper('newsletter')->__('Template Type'),
                'index'=>'template_type_name',
                'width' => 1
        ));

        $this->addColumn('added_at',
            array(
                'header'=>Mage::helper('newsletter')->__('Date Added'),
                'index'=>'added_at',
                'gmtoffset' => true,
                'type'=>'datetime'
        ));

        $this->addColumn('modified_at',
            array(
                'header'=>Mage::helper('newsletter')->__('Date Updated'),
                'index'=>'modified_at',
                'gmtoffset' => true,
                'type'=>'datetime'
        ));

        $this->addColumn('subject',
            array(
                'header'=>Mage::helper('newsletter')->__('Subject'),
                'index'=>'template_subject',
                'width' => 1
        ));

        $this->addColumn('sender',
            array(
                'header'=>Mage::helper('newsletter')->__('Sender'),
                'index'=>'template_sender_email',
                'renderer' => 'adminhtml/newsletter_template_grid_renderer_sender',
                'width' => 1
        ));

        /* @var $formats Contactlab_Template_Model_System_Config_Source_Template_Format */
        $formats = Mage::getModel('contactlab_template/system_config_source_template_format');
        $this->addColumn('flg_html_txt',
            array(
                'header'=>Mage::helper('newsletter')->__('Content Type'),
                'index'=>'flg_html_txt',
                'type' => 'options',
                'options' => $formats->toArray(),
                'width' => 1,
        ));

        $this->addColumn('priority',
            array(
                'header'=>Mage::helper('newsletter')->__('Priority'),
                'index'=>'priority',
                'width' => 1
        ));

        $this->addColumn('tasks',
            array(
                'header' => Mage::helper('newsletter')->__('XML Delivery Status'),
                'renderer' => 'contactlab_template/adminhtml_newsletter_template_grid_renderer_xmlDeliveryStatus'
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('newsletter')->__('Action'),
                'index'     =>'template_id',
                'sortable' =>false,
                'filter'   => false,
                'no_link' => true,
                'width'	   => '170px',
                'renderer' => 'adminhtml/newsletter_template_grid_renderer_action'
        ));

        return $this;
    }

    /**
     * Prepare collection for the grid
     * @return \Contactlab_Template_Block_Adminhtml_Newsletter_Template_Grid
     */
    protected function _prepareCollection() {
        /* @var $collection Mage_Newsletter_Model_Resource_Template_Collection */
        $collection = Mage::getResourceSingleton('newsletter/template_collection')
            ->useOnlyActual();
        $collection->getSelect()
            ->joinLeft(array('template_types' => $collection->getTable('contactlab_template/type')),
                "main_table.template_type_id = template_types.entity_id",
                array('template_type_name' => 'name'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 }
