<?php

class Campaign
{

  /**
   * 
   * @var int $identifier
   * @access public
   */
  public $identifier;

  /**
   * 
   * @var string $name
   * @access public
   */
  public $name;

  /**
   * 
   * @var string $alias
   * @access public
   */
  public $alias;

  /**
   * 
   * @var Message $message
   * @access public
   */
  public $message;

  /**
   * 
   * @var CampaignType $modifier
   * @access public
   */
  public $modifier;

  /**
   * 
   * @var boolean $isDeferred
   * @access public
   */
  public $isDeferred;

  /**
   * 
   * @var dateTime $deferredTo
   * @access public
   */
  public $deferredTo;

  /**
   * 
   * @var dateTime $startDate
   * @access public
   */
  public $startDate;

  /**
   * 
   * @var dateTime $endDate
   * @access public
   */
  public $endDate;

  /**
   * 
   * @var boolean $executeTestRender
   * @access public
   */
  public $executeTestRender;

  /**
   * 
   * @var int $parentId
   * @access public
   */
  public $parentId;

  /**
   * 
   * @var deliveryRoleType $roleType
   * @access public
   */
  public $roleType;

}
