<?php

include_once('Message.php');

class FaxMessage extends Message
{

  /**
   * 
   * @var base64Binary $content
   * @access public
   */
  public $content;

  /**
   * 
   * @var string $fileName
   * @access public
   */
  public $fileName;

  /**
   * 
   * @var MimeType $mimeType
   * @access public
   */
  public $mimeType;

  /**
   * 
   * @var int $numberOfPages
   * @access public
   */
  public $numberOfPages;

}
