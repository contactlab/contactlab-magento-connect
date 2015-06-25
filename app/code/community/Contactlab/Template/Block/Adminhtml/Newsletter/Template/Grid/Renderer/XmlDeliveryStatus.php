<?php

/**
 * Configure link renderer.
 */
class Contactlab_Template_Block_Adminhtml_Newsletter_Template_Grid_Renderer_XmlDeliveryStatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Renders grid column
     *
     * @param Varien_Object $row        	
     * @return string
     */
    public function render(Varien_Object $row) {
        return $this->renderTasks($row->getTemplateId());
    }

    public function renderTasks($templateId) {
        $rv = "";
        /* @var $queueCollection Mage_Newsletter_Model_Resource_Queue_Collection */
        $queueCollection = Mage::getResourceModel("newsletter/queue_collection");
        $queueCollection->addFieldToFilter('template_id', $templateId);
        $queueCollection->join(array('task' => 'contactlab_commons/task'),
                'main_table.task_id = task.task_id and task.status != \'hidden\'',
                array(
                    'status' => 'status',
                    'task_created_at' => 'created_at',
                    'task_planned_at' => 'planned_at'
                ));
        $queueCollection->addOrder('task_id', Varien_Data_Collection::SORT_ORDER_DESC);
        if ($queueCollection->count() == 0) {
            return $rv;
        }

        /* @var $helper Contactlab_Template_Helper_Data */
        $helper = Mage::helper('contactlab_commons');

        $rv .= "<table class=\"data\" cellspacing=\"0\">";
        $rv .= "<thead>";
        $rv .= "<tr class=\"headings\">";
        $rv .= "<th>" . $helper->__('Created at') ."</th>";
        $rv .= "<th>" . $helper->__('Planned at') ."</th>";
        $rv .= "<th>" . $helper->__('Status') ."</th>";
        $rv .= "</tr>";
        $rv .= "</thead>";
        $rv .= "<tbody>";
        
        $found = false;
        $nr = 0;
        foreach ($queueCollection as $queue) {
            if ($queue->getStatus() === 'closed') {
                $nr = $nr + 1;
            }
            $a = sprintf('<a href="%s" title="%s">',
                    Mage::helper("adminhtml")->getUrl("contactlab_commons/adminhtml_events",
                        array('id' => $queue->getTaskId())), $helper->__('Task events'));
            $rv .= "<tr";
            if ($queue->getStatus() === 'closed' && $nr > 1) {
                $rv .= ' style="display: none" class="template-' . $templateId . '-row"';
                $found = true;
            }
            $rv .= ">";
            $rv .= "<td>" . $a . $queue->getTaskCreatedAt() . "</a></td>";
            $rv .= "<td>" . $a . ($queue->hasTaskPlannedAt() ? $queue->getTaskPlannedAt() : '&mdash;') . "</a></td>";
            $rv .= "<td>" . $a . Contactlab_Commons_Block_Adminhtml_Events_Renderer_Status::renderTask($queue) . "</a></td>";
            $rv .= "</tr>";
        }

        $a = "<strong><big>&#8595;</big> " . $helper->__('Show all') . "</strong>";
        $b = "<strong><big>&#8593;</big> " . $helper->__('Hide') . "</strong>";
        if ($found) {
            $rv .= "<tr>";
            $rv .= "<td colspan=\"3\">";
            $rv .= "<a id=\"toggle-" . $templateId . "-row\" href=\"#\" onclick=\"$$('.template-" . $templateId . "-row').each(Element.toggle); $('toggle-" . $templateId . "-row').innerHTML = $('toggle-" . $templateId . "-row').innerHTML == '$a' ? '$b' : '$a'; return false;\">$a</a>";
            $rv .= "</td>";
            $rv .= "</tr>";
        }
        $rv .= "</tbody>";
        $rv .= "</table>";
        return $rv;
    }
}
