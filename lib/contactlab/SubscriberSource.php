<?php

class SubscriberSource
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
   * @var string $description
   * @access public
   */
  public $description;

  /**
   * 
   * @var string $subscriberIdentifierFieldName
   * @access public
   */
  public $subscriberIdentifierFieldName;

  /**
   * 
   * @var SubscriberSourceField $fields
   * @access public
   */
  public $fields;

  /**
   * 
   * @var charset $charset
   * @access public
   */
  public $charset;

  /**
   * 
   * @var boolean $locked
   * @access public
   */
  public $locked;

  /**
   * 
   * @var boolean $readOnly
   * @access public
   */
  public $readOnly;

}
