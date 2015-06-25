<?php

include_once('Message.php');

class EmailMessage extends Message
{

  /**
   * 
   * @var string $subject
   * @access public
   */
  public $subject;

  /**
   * 
   * @var string $htmlContent
   * @access public
   */
  public $htmlContent;

  /**
   * 
   * @var string $textContent
   * @access public
   */
  public $textContent;

  /**
   * 
   * @var PreferredContent $preferredContent
   * @access public
   */
  public $preferredContent;

}
