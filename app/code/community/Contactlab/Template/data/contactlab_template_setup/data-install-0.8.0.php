<?php

$cart = Mage::getModel("contactlab_template/type");
$cart->setName("Abandoned cart");
$cart->setTemplateTypeCode("CART");
$cart->setIsSystem(1);
$cart->setIsCronEnabled(1);
$cart->save();

$wish = Mage::getModel("contactlab_template/type");
$wish->setName("Wishlist remind");
$wish->setTemplateTypeCode("WISHLIST");
$wish->setIsSystem(1);
$wish->setIsCronEnabled(1);
$wish->save();


$wish = Mage::getModel("contactlab_template/type");
$wish->setName("Ceneric template");
$wish->setTemplateTypeCode("GENERIC");
$wish->setIsSystem(1);
$wish->setIsCronEnabled(0);
$wish->save();


