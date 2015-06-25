<?php

class Message
{

  /**
   * 
   * @var charset $charset
   * @access public
   */
  public $charset;

  /**
   * 
   * @var int $modelIdentifier
   * @access public
   */
  public $modelIdentifier;

  /**
   * 
   * @var Recipients $recipients
   * @access public
   */
  public $recipients;

  /**
   * 
   * @var Sender $sender
   * @access public
   */
  public $sender;

  /**
   * 
   * @var int $prefAttachmentCount
   * @access public
   */
  public $prefAttachmentCount;

  /**
   * 
   * @var int $minAttachmentCount
   * @access public
   */
  public $minAttachmentCount;

  /**
   * 
   * @var int $maxAttachmentCount
   * @access public
   */
  public $maxAttachmentCount;

  /**
   * 
   * @var int $communicationCategoryIdentifier
   * @access public
   */
  public $communicationCategoryIdentifier;

}
