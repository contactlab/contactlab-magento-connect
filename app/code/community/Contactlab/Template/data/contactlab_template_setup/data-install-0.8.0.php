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

$generic = Mage::getModel("contactlab_template/type");
$generic->setName("Generic template");
$generic->setTemplateTypeCode("GENERIC");
$generic->setIsSystem(1);
$generic->setIsCronEnabled(0);
$generic->save();
