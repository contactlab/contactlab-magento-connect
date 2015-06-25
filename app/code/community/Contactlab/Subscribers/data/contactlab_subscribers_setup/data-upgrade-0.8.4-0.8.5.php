<?php

/* @var $helper Contactlab_Subscribers_Helper_Uk */
$helper = Mage::helper('contactlab_subscribers/uk');
$helper->addUpdateUkTask();

/* @var $helper2 Contactlab_Subscribers_Helper_Data */
$helper2 = Mage::helper('contactlab_subscribers');
$helper2->addCalcStatsQueue();