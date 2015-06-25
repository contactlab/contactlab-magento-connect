<?php

/**
 * Simplexmlextended, CDATA method.
 */
class Contactlab_Template_Model_SimpleXMLExtended extends SimpleXMLElement {
    public function addCData($cdata_text) {
        $node = dom_import_simplexml($this);
        $no   = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdata_text));
    }
}
