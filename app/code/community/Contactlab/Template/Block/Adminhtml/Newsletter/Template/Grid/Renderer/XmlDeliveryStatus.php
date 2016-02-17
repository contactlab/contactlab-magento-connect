<?php

/**
 * XML Delivery status column renderer
 */
class Contactlab_Template_Block_Adminhtml_Newsletter_Template_Grid_Renderer_XmlDeliveryStatus
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->renderTasksCount($row->getTemplateId());
    }

    /**
     * Render tasks with status count.
     * @param $templateId
     * @return string
     */
    public function renderTasksCount($templateId)
    {
        /* @var $queueCollection Contactlab_Template_Model_Resource_Newsletter_Queue_Collection */
        $queueCollection = Mage::getResourceModel("newsletter/queue_collection");
        $queueCollection->getQueueByTemplateId($templateId)->addTaskData();

        // Hidden tasks shown as closed.
        $statusHiddenClosedExpr = new Zend_Db_Expr("if (task.status = 'hidden', 'closed', task.status)");
        $queueCollection->getSelect()
            ->group($statusHiddenClosedExpr)
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array('status' => $statusHiddenClosedExpr, 'queue_count' => new Zend_Db_Expr('count(1)')));
        $rv = array();
        while ($item = $queueCollection->fetchItem()) {
            if ($item->getStatus() === 'hidden') {
                $item->setStatus('closed');
            }
            $rv[] = sprintf('%s: %d',
                Contactlab_Commons_Block_Adminhtml_Events_Renderer_Status::renderTask($item),
                $item->getQueueCount());
        }
        if (empty($rv)) {
            return '';
        }
        /* @var $helper Contactlab_Template_Helper_Data */
        $helper = Mage::helper('contactlab_commons');
        $link = sprintf('<a title="%s" href="%s">%s</a>',
            $helper->__('Show full XML Delivery status information for this template'),
            $this->_getUrlForTemplateId($templateId),
            $helper->__('Full XML Delivery status'));

        return sprintf('%s<br>%s', implode('<br />', $rv), $link);
    }

    /**
     * Render tasks with status.
     * @param $templateId
     * @deprecated
     * @return string
     */
    public function renderTasks($templateId)
    {
        $rv = "";
        /* @var $queueCollection Contactlab_Template_Model_Resource_Newsletter_Queue_Collection */
        $queueCollection = Mage::getResourceModel("newsletter/queue_collection");
        $queueCollection->getQueueByTemplateId($templateId)->addTaskData();

        if ($queueCollection->count() == 0) {
            return $rv;
        }

        /* @var $helper Contactlab_Template_Helper_Data */
        $helper = Mage::helper('contactlab_commons');

        $rv .= "<table class=\"data\" cellspacing=\"0\">";
        $rv .= "<thead>";
        $rv .= "<tr class=\"headings\">";
        $rv .= "<th>" . $helper->__('Created') . "</th>";
        $rv .= "<th>" . $helper->__('Planned') . "</th>";
        $rv .= "<th>" . $helper->__('Status') . "</th>";
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
            if ($queue->getStatus() === 'hidden') {
                $queue->setStatus('closed');
            }
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

    /**
     * Get Url for template task list.
     * @param int $templateId
     * @return string
     */
    private function _getUrlForTemplateId($templateId)
    {
        return Mage::helper('adminhtml')
            ->getUrl('adminhtml/contactlab_template_tasks/list',
                array('template_id' => $templateId));
    }
}
