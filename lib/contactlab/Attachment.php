<?php

class Attachment
{

  /**
   * 
   * @var int $identifier
   * @access public
   */
  public $identifier;

  /**
   * 
   * @var int $campaignIdentifier
   * @access public
   */
  public $campaignIdentifier;

  /**
   * 
   * @var string $name
   * @access public
   */
  public $name;

  /**
   * 
   * @var MimeType $mimeType
   * @access public
   */
  public $mimeType;

  /**
   * 
   * @var base64Binary $content
   * @access public
   */
  public $content;

}
