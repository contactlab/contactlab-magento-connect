<?php

include_once('AuthToken.php');
include_once('CommunicationCategory.php');
include_once('SubscriberSourceFilter.php');
include_once('LookupPreferences.php');
include_once('CampaignLookupPreferences.php');
include_once('XMLDeliveryInfos.php');
include_once('SlicedDataSet.php');
include_once('Campaigns.php');
include_once('Campaign.php');
include_once('Message.php');
include_once('Recipients.php');
include_once('Sender.php');
include_once('TextMessage.php');
include_once('EmailMessage.php');
include_once('FaxMessage.php');
include_once('SubscriberSources.php');
include_once('SubscriberSource.php');
include_once('SubscriberSourceField.php');
include_once('MessageModels.php');
include_once('SubscriberSourceFilters.php');
include_once('CampaignNotes.php');
include_once('CampaignNote.php');
include_once('TrackedLinks.php');
include_once('TrackedLink.php');
include_once('Subscribers.php');
include_once('Subscriber.php');
include_once('SubscriberAttribute.php');
include_once('Attachment.php');
include_once('CampaignFeedback.php');
include_once('BounceDetail.php');
include_once('keepaliveToken.php');
include_once('keepaliveTokenResponse.php');
include_once('triggerDeliveryByAlias.php');
include_once('triggerDeliveryByAliasResponse.php');
include_once('getAvailableCommunicationCategories.php');
include_once('getAvailableCommunicationCategoriesResponse.php');
include_once('addSubscriberSourceFilter.php');
include_once('addSubscriberSourceFilterResponse.php');
include_once('getArchivedSubscriberSourceFilter.php');
include_once('getArchivedSubscriberSourceFilterResponse.php');
include_once('getXMLDeliveries.php');
include_once('getXMLDeliveriesResponse.php');
include_once('xmlDeliveryInfo.php');
include_once('sendCampaign.php');
include_once('sendCampaignResponse.php');
include_once('findCampaignsSentBetween.php');
include_once('findCampaignsSentBetweenResponse.php');
include_once('countSubscribers.php');
include_once('countSubscribersResponse.php');
include_once('sendImmediateMessageSdataCAlCA.php');
include_once('sendImmediateMessageSdataCAlCAResponse.php');
include_once('getXMLDeliveryTransitions.php');
include_once('getXMLDeliveryTransitionsResponse.php');
include_once('xmlDeliveryTransitionInfo.php');
include_once('findCampaignsByNameOrSubject.php');
include_once('findCampaignsByNameOrSubjectResponse.php');
include_once('sendImmediateMessageSidCAlCA.php');
include_once('sendImmediateMessageSidCAlCAResponse.php');
include_once('addCampaignNote.php');
include_once('addCampaignNoteResponse.php');
include_once('triggerDeliveryById.php');
include_once('triggerDeliveryByIdResponse.php');
include_once('getRequestStatus.php');
include_once('getRequestStatusResponse.php');
include_once('findCampaigns.php');
include_once('findCampaignsResponse.php');
include_once('updateSubscriber.php');
include_once('updateSubscriberResponse.php');
include_once('getCryptoKey.php');
include_once('getCryptoKeyResponse.php');
include_once('archiveSubscriberSourceFilter.php');
include_once('archiveSubscriberSourceFilterResponse.php');
include_once('cloneAndSendCampaign.php');
include_once('cloneAndSendCampaignResponse.php');
include_once('uploadMediaContent.php');
include_once('uploadMediaContentResponse.php');
include_once('findMessageModels.php');
include_once('findMessageModelsResponse.php');
include_once('sendImmediateMessageSDataCDataCA.php');
include_once('sendImmediateMessageSDataCDataCAResponse.php');
include_once('getCampaign.php');
include_once('getCampaignResponse.php');
include_once('addSubscribers.php');
include_once('addSubscribersResponse.php');
include_once('sendImmediateMessageSidCid.php');
include_once('sendImmediateMessageSidCidResponse.php');
include_once('sendImmediateMessageSDataCIdCA.php');
include_once('sendImmediateMessageSDataCIdCAResponse.php');
include_once('getSubscriberSourceFilter.php');
include_once('getSubscriberSourceFilterResponse.php');
include_once('findArchivedFiltersBySubscriberSource.php');
include_once('findArchivedFiltersBySubscriberSourceResponse.php');
include_once('getMessageModelById.php');
include_once('getMessageModelByIdResponse.php');
include_once('getAttachmentByCampaignId.php');
include_once('getAttachmentByCampaignIdResponse.php');
include_once('getSubscriber.php');
include_once('getSubscriberResponse.php');
include_once('findPeriodicCampaigns.php');
include_once('findPeriodicCampaignsResponse.php');
include_once('modifySubscriberSubscriptionStatus.php');
include_once('modifySubscriberSubscriptionStatusResponse.php');
include_once('borrowToken.php');
include_once('borrowTokenResponse.php');
include_once('findNotesByCampaign.php');
include_once('findNotesByCampaignResponse.php');
include_once('modifySubscriberSubscriptionStatusByMailqId.php');
include_once('modifySubscriberSubscriptionStatusByMailqIdResponse.php');
include_once('startSubscriberDataExchange.php');
include_once('startSubscriberDataExchangeResponse.php');
include_once('getCampaignDeliveryStatus.php');
include_once('getCampaignDeliveryStatusResponse.php');
include_once('removeSubscriber.php');
include_once('removeSubscriberResponse.php');
include_once('addAttachment.php');
include_once('addAttachmentResponse.php');
include_once('setCampaignRecurrency.php');
include_once('setCampaignRecurrencyResponse.php');
include_once('findFiltersBySubscriberSource.php');
include_once('findFiltersBySubscriberSourceResponse.php');
include_once('countSubscribersIncludedInFilter.php');
include_once('countSubscribersIncludedInFilterResponse.php');
include_once('sendImmediateMessageSidCidCA.php');
include_once('sendImmediateMessageSidCidCAResponse.php');
include_once('findSubscriberSources.php');
include_once('findSubscriberSourcesResponse.php');
include_once('addSubscriberSource.php');
include_once('addSubscriberSourceResponse.php');
include_once('findMessageModelsBySubscriberSource.php');
include_once('findMessageModelsBySubscriberSourceResponse.php');
include_once('sendImmediateMessageSdataCAl.php');
include_once('sendImmediateMessageSdataCAlResponse.php');
include_once('findCampaignsByModel.php');
include_once('findCampaignsByModelResponse.php');
include_once('sendImmediateMessageSDataCData.php');
include_once('sendImmediateMessageSDataCDataResponse.php');
include_once('getSubscriberDataExchangeStatus.php');
include_once('getSubscriberDataExchangeStatusResponse.php');
include_once('sendImmediateMessageSIdCDataCA.php');
include_once('sendImmediateMessageSIdCDataCAResponse.php');
include_once('sendImmediateMessage.php');
include_once('sendImmediateMessageResponse.php');
include_once('sendImmediateMessageSIdCData.php');
include_once('sendImmediateMessageSIdCDataResponse.php');
include_once('sendImmediateMessageSidCAl.php');
include_once('sendImmediateMessageSidCAlResponse.php');
include_once('findCampaignsByStatus.php');
include_once('findCampaignsByStatusResponse.php');
include_once('getSubscriberSource.php');
include_once('getSubscriberSourceResponse.php');
include_once('findSubscribers.php');
include_once('findSubscribersResponse.php');
include_once('findTriggerableCampaigns.php');
include_once('findTriggerableCampaignsResponse.php');
include_once('findCampaignsByNote.php');
include_once('findCampaignsByNoteResponse.php');
include_once('publishOnWeb.php');
include_once('publishOnWebResponse.php');
include_once('invalidateToken.php');
include_once('invalidateTokenResponse.php');
include_once('reuseSubscriberSourceFilter.php');
include_once('reuseSubscriberSourceFilterResponse.php');
include_once('createCampaign.php');
include_once('createCampaignResponse.php');
include_once('addSubscriber.php');
include_once('addSubscriberResponse.php');
include_once('getTrackedLinks.php');
include_once('getTrackedLinksResponse.php');
include_once('getCampaignFeedback.php');
include_once('getCampaignFeedbackResponse.php');
include_once('requestCampaignFeedbackReport.php');
include_once('requestCampaignFeedbackReportResponse.php');
include_once('createMessageModel.php');
include_once('createMessageModelResponse.php');
include_once('cancelCampaign.php');
include_once('cancelCampaignResponse.php');
include_once('findSubscribersIncludedInFilter.php');
include_once('findSubscribersIncludedInFilterResponse.php');


/**
 * 
 */
class ClabService extends SoapClient
{

  /**
   * 
   * @var array $classmap The defined classes
   * @access private
   */
  private static $classmap = array(
    'AuthToken' => 'AuthToken',
    'CommunicationCategory' => 'CommunicationCategory',
    'SubscriberSourceFilter' => 'SubscriberSourceFilter',
    'LookupPreferences' => 'LookupPreferences',
    'CampaignLookupPreferences' => 'CampaignLookupPreferences',
    'XMLDeliveryInfos' => 'XMLDeliveryInfos',
    'SlicedDataSet' => 'SlicedDataSet',
    'Campaigns' => 'Campaigns',
    'Campaign' => 'Campaign',
    'Message' => 'Message',
    'Recipients' => 'Recipients',
    'Sender' => 'Sender',
    'TextMessage' => 'TextMessage',
    'EmailMessage' => 'EmailMessage',
    'FaxMessage' => 'FaxMessage',
    'SubscriberSources' => 'SubscriberSources',
    'SubscriberSource' => 'SubscriberSource',
    'SubscriberSourceField' => 'SubscriberSourceField',
    'MessageModels' => 'MessageModels',
    'SubscriberSourceFilters' => 'SubscriberSourceFilters',
    'CampaignNotes' => 'CampaignNotes',
    'CampaignNote' => 'CampaignNote',
    'TrackedLinks' => 'TrackedLinks',
    'TrackedLink' => 'TrackedLink',
    'Subscribers' => 'Subscribers',
    'Subscriber' => 'Subscriber',
    'SubscriberAttribute' => 'SubscriberAttribute',
    'Attachment' => 'Attachment',
    'CampaignFeedback' => 'CampaignFeedback',
    'BounceDetail' => 'BounceDetail',
    'keepaliveToken' => 'keepaliveToken',
    'keepaliveTokenResponse' => 'keepaliveTokenResponse',
    'triggerDeliveryByAlias' => 'triggerDeliveryByAlias',
    'triggerDeliveryByAliasResponse' => 'triggerDeliveryByAliasResponse',
    'getAvailableCommunicationCategories' => 'getAvailableCommunicationCategories',
    'getAvailableCommunicationCategoriesResponse' => 'getAvailableCommunicationCategoriesResponse',
    'addSubscriberSourceFilter' => 'addSubscriberSourceFilter',
    'addSubscriberSourceFilterResponse' => 'addSubscriberSourceFilterResponse',
    'getArchivedSubscriberSourceFilter' => 'getArchivedSubscriberSourceFilter',
    'getArchivedSubscriberSourceFilterResponse' => 'getArchivedSubscriberSourceFilterResponse',
    'getXMLDeliveries' => 'getXMLDeliveries',
    'getXMLDeliveriesResponse' => 'getXMLDeliveriesResponse',
    'xmlDeliveryInfo' => 'xmlDeliveryInfo',
    'sendCampaign' => 'sendCampaign',
    'sendCampaignResponse' => 'sendCampaignResponse',
    'findCampaignsSentBetween' => 'findCampaignsSentBetween',
    'findCampaignsSentBetweenResponse' => 'findCampaignsSentBetweenResponse',
    'countSubscribers' => 'countSubscribers',
    'countSubscribersResponse' => 'countSubscribersResponse',
    'sendImmediateMessageSdataCAlCA' => 'sendImmediateMessageSdataCAlCA',
    'sendImmediateMessageSdataCAlCAResponse' => 'sendImmediateMessageSdataCAlCAResponse',
    'getXMLDeliveryTransitions' => 'getXMLDeliveryTransitions',
    'getXMLDeliveryTransitionsResponse' => 'getXMLDeliveryTransitionsResponse',
    'xmlDeliveryTransitionInfo' => 'xmlDeliveryTransitionInfo',
    'findCampaignsByNameOrSubject' => 'findCampaignsByNameOrSubject',
    'findCampaignsByNameOrSubjectResponse' => 'findCampaignsByNameOrSubjectResponse',
    'sendImmediateMessageSidCAlCA' => 'sendImmediateMessageSidCAlCA',
    'sendImmediateMessageSidCAlCAResponse' => 'sendImmediateMessageSidCAlCAResponse',
    'addCampaignNote' => 'addCampaignNote',
    'addCampaignNoteResponse' => 'addCampaignNoteResponse',
    'triggerDeliveryById' => 'triggerDeliveryById',
    'triggerDeliveryByIdResponse' => 'triggerDeliveryByIdResponse',
    'getRequestStatus' => 'getRequestStatus',
    'getRequestStatusResponse' => 'getRequestStatusResponse',
    'findCampaigns' => 'findCampaigns',
    'findCampaignsResponse' => 'findCampaignsResponse',
    'updateSubscriber' => 'updateSubscriber',
    'updateSubscriberResponse' => 'updateSubscriberResponse',
    'getCryptoKey' => 'getCryptoKey',
    'getCryptoKeyResponse' => 'getCryptoKeyResponse',
    'archiveSubscriberSourceFilter' => 'archiveSubscriberSourceFilter',
    'archiveSubscriberSourceFilterResponse' => 'archiveSubscriberSourceFilterResponse',
    'cloneAndSendCampaign' => 'cloneAndSendCampaign',
    'cloneAndSendCampaignResponse' => 'cloneAndSendCampaignResponse',
    'uploadMediaContent' => 'uploadMediaContent',
    'uploadMediaContentResponse' => 'uploadMediaContentResponse',
    'findMessageModels' => 'findMessageModels',
    'findMessageModelsResponse' => 'findMessageModelsResponse',
    'sendImmediateMessageSDataCDataCA' => 'sendImmediateMessageSDataCDataCA',
    'sendImmediateMessageSDataCDataCAResponse' => 'sendImmediateMessageSDataCDataCAResponse',
    'getCampaign' => 'getCampaign',
    'getCampaignResponse' => 'getCampaignResponse',
    'addSubscribers' => 'addSubscribers',
    'addSubscribersResponse' => 'addSubscribersResponse',
    'sendImmediateMessageSidCid' => 'sendImmediateMessageSidCid',
    'sendImmediateMessageSidCidResponse' => 'sendImmediateMessageSidCidResponse',
    'sendImmediateMessageSDataCIdCA' => 'sendImmediateMessageSDataCIdCA',
    'sendImmediateMessageSDataCIdCAResponse' => 'sendImmediateMessageSDataCIdCAResponse',
    'getSubscriberSourceFilter' => 'getSubscriberSourceFilter',
    'getSubscriberSourceFilterResponse' => 'getSubscriberSourceFilterResponse',
    'findArchivedFiltersBySubscriberSource' => 'findArchivedFiltersBySubscriberSource',
    'findArchivedFiltersBySubscriberSourceResponse' => 'findArchivedFiltersBySubscriberSourceResponse',
    'getMessageModelById' => 'getMessageModelById',
    'getMessageModelByIdResponse' => 'getMessageModelByIdResponse',
    'getAttachmentByCampaignId' => 'getAttachmentByCampaignId',
    'getAttachmentByCampaignIdResponse' => 'getAttachmentByCampaignIdResponse',
    'getSubscriber' => 'getSubscriber',
    'getSubscriberResponse' => 'getSubscriberResponse',
    'findPeriodicCampaigns' => 'findPeriodicCampaigns',
    'findPeriodicCampaignsResponse' => 'findPeriodicCampaignsResponse',
    'modifySubscriberSubscriptionStatus' => 'modifySubscriberSubscriptionStatus',
    'modifySubscriberSubscriptionStatusResponse' => 'modifySubscriberSubscriptionStatusResponse',
    'borrowToken' => 'borrowToken',
    'borrowTokenResponse' => 'borrowTokenResponse',
    'findNotesByCampaign' => 'findNotesByCampaign',
    'findNotesByCampaignResponse' => 'findNotesByCampaignResponse',
    'modifySubscriberSubscriptionStatusByMailqId' => 'modifySubscriberSubscriptionStatusByMailqId',
    'modifySubscriberSubscriptionStatusByMailqIdResponse' => 'modifySubscriberSubscriptionStatusByMailqIdResponse',
    'startSubscriberDataExchange' => 'startSubscriberDataExchange',
    'startSubscriberDataExchangeResponse' => 'startSubscriberDataExchangeResponse',
    'getCampaignDeliveryStatus' => 'getCampaignDeliveryStatus',
    'getCampaignDeliveryStatusResponse' => 'getCampaignDeliveryStatusResponse',
    'removeSubscriber' => 'removeSubscriber',
    'removeSubscriberResponse' => 'removeSubscriberResponse',
    'addAttachment' => 'addAttachment',
    'addAttachmentResponse' => 'addAttachmentResponse',
    'setCampaignRecurrency' => 'setCampaignRecurrency',
    'setCampaignRecurrencyResponse' => 'setCampaignRecurrencyResponse',
    'findFiltersBySubscriberSource' => 'findFiltersBySubscriberSource',
    'findFiltersBySubscriberSourceResponse' => 'findFiltersBySubscriberSourceResponse',
    'countSubscribersIncludedInFilter' => 'countSubscribersIncludedInFilter',
    'countSubscribersIncludedInFilterResponse' => 'countSubscribersIncludedInFilterResponse',
    'sendImmediateMessageSidCidCA' => 'sendImmediateMessageSidCidCA',
    'sendImmediateMessageSidCidCAResponse' => 'sendImmediateMessageSidCidCAResponse',
    'findSubscriberSources' => 'findSubscriberSources',
    'findSubscriberSourcesResponse' => 'findSubscriberSourcesResponse',
    'addSubscriberSource' => 'addSubscriberSource',
    'addSubscriberSourceResponse' => 'addSubscriberSourceResponse',
    'findMessageModelsBySubscriberSource' => 'findMessageModelsBySubscriberSource',
    'findMessageModelsBySubscriberSourceResponse' => 'findMessageModelsBySubscriberSourceResponse',
    'sendImmediateMessageSdataCAl' => 'sendImmediateMessageSdataCAl',
    'sendImmediateMessageSdataCAlResponse' => 'sendImmediateMessageSdataCAlResponse',
    'findCampaignsByModel' => 'findCampaignsByModel',
    'findCampaignsByModelResponse' => 'findCampaignsByModelResponse',
    'sendImmediateMessageSDataCData' => 'sendImmediateMessageSDataCData',
    'sendImmediateMessageSDataCDataResponse' => 'sendImmediateMessageSDataCDataResponse',
    'getSubscriberDataExchangeStatus' => 'getSubscriberDataExchangeStatus',
    'getSubscriberDataExchangeStatusResponse' => 'getSubscriberDataExchangeStatusResponse',
    'sendImmediateMessageSIdCDataCA' => 'sendImmediateMessageSIdCDataCA',
    'sendImmediateMessageSIdCDataCAResponse' => 'sendImmediateMessageSIdCDataCAResponse',
    'sendImmediateMessage' => 'sendImmediateMessage',
    'sendImmediateMessageResponse' => 'sendImmediateMessageResponse',
    'sendImmediateMessageSIdCData' => 'sendImmediateMessageSIdCData',
    'sendImmediateMessageSIdCDataResponse' => 'sendImmediateMessageSIdCDataResponse',
    'sendImmediateMessageSidCAl' => 'sendImmediateMessageSidCAl',
    'sendImmediateMessageSidCAlResponse' => 'sendImmediateMessageSidCAlResponse',
    'findCampaignsByStatus' => 'findCampaignsByStatus',
    'findCampaignsByStatusResponse' => 'findCampaignsByStatusResponse',
    'getSubscriberSource' => 'getSubscriberSource',
    'getSubscriberSourceResponse' => 'getSubscriberSourceResponse',
    'findSubscribers' => 'findSubscribers',
    'findSubscribersResponse' => 'findSubscribersResponse',
    'findTriggerableCampaigns' => 'findTriggerableCampaigns',
    'findTriggerableCampaignsResponse' => 'findTriggerableCampaignsResponse',
    'findCampaignsByNote' => 'findCampaignsByNote',
    'findCampaignsByNoteResponse' => 'findCampaignsByNoteResponse',
    'publishOnWeb' => 'publishOnWeb',
    'publishOnWebResponse' => 'publishOnWebResponse',
    'invalidateToken' => 'invalidateToken',
    'invalidateTokenResponse' => 'invalidateTokenResponse',
    'reuseSubscriberSourceFilter' => 'reuseSubscriberSourceFilter',
    'reuseSubscriberSourceFilterResponse' => 'reuseSubscriberSourceFilterResponse',
    'createCampaign' => 'createCampaign',
    'createCampaignResponse' => 'createCampaignResponse',
    'addSubscriber' => 'addSubscriber',
    'addSubscriberResponse' => 'addSubscriberResponse',
    'getTrackedLinks' => 'getTrackedLinks',
    'getTrackedLinksResponse' => 'getTrackedLinksResponse',
    'getCampaignFeedback' => 'getCampaignFeedback',
    'getCampaignFeedbackResponse' => 'getCampaignFeedbackResponse',
    'requestCampaignFeedbackReport' => 'requestCampaignFeedbackReport',
    'requestCampaignFeedbackReportResponse' => 'requestCampaignFeedbackReportResponse',
    'createMessageModel' => 'createMessageModel',
    'createMessageModelResponse' => 'createMessageModelResponse',
    'cancelCampaign' => 'cancelCampaign',
    'cancelCampaignResponse' => 'cancelCampaignResponse',
    'findSubscribersIncludedInFilter' => 'findSubscribersIncludedInFilter',
    'findSubscribersIncludedInFilterResponse' => 'findSubscribersIncludedInFilterResponse');

  /**
   * 
   * @param array $config A array of config values
   * @param string $wsdl The wsdl file to use
   * @access public
   */
  public function __construct(array $options = array('soap_version' => SOAP_1_2,'connection_timeout' => 30,'trace'=>true,'keep_alive' => true), $wsdl = 'https://soap.contactlab.it/soap/services?wsdl')
  {
    foreach(self::$classmap as $key => $value)
          {
          if(!isset($options['classmap'][$key]))
          {
          $options['classmap'][$key] = $value;
      }
      }
      
              if (isset($options['features']) == false)
              {
              $options['features'] = 2 | 30 | 1 | 1;
          }
  
      parent::__construct($wsdl, $options);
  }

  /**
   * 
   * @param getAvailableCommunicationCategories $parameters
   * @access public
   */
  public function getAvailableCommunicationCategories(getAvailableCommunicationCategories $parameters)
  {
    return $this->__soapCall('getAvailableCommunicationCategories', array($parameters));
  }

  /**
   * 
   * @param getCampaignDeliveryStatus $parameters
   * @access public
   */
  public function getCampaignDeliveryStatus(getCampaignDeliveryStatus $parameters)
  {
    return $this->__soapCall('getCampaignDeliveryStatus', array($parameters));
  }

  /**
   * 
   * @param publishOnWeb $parameters
   * @access public
   */
  public function publishOnWeb(publishOnWeb $parameters)
  {
    return $this->__soapCall('publishOnWeb', array($parameters));
  }

  /**
   * 
   * @param updateSubscriber $parameters
   * @access public
   */
  public function updateSubscriber(updateSubscriber $parameters)
  {
    return $this->__soapCall('updateSubscriber', array($parameters));
  }

  /**
   * 
   * @param modifySubscriberSubscriptionStatus $parameters
   * @access public
   */
  public function modifySubscriberSubscriptionStatus(modifySubscriberSubscriptionStatus $parameters)
  {
    return $this->__soapCall('modifySubscriberSubscriptionStatus', array($parameters));
  }

  /**
   * 
   * @param countSubscribers $parameters
   * @access public
   */
  public function countSubscribers(countSubscribers $parameters)
  {
    return $this->__soapCall('countSubscribers', array($parameters));
  }

  /**
   * 
   * @param addCampaignNote $parameters
   * @access public
   */
  public function addCampaignNote(addCampaignNote $parameters)
  {
    return $this->__soapCall('addCampaignNote', array($parameters));
  }

  /**
   * 
   * @param findNotesByCampaign $parameters
   * @access public
   */
  public function findNotesByCampaign(findNotesByCampaign $parameters)
  {
    return $this->__soapCall('findNotesByCampaign', array($parameters));
  }

  /**
   * 
   * @param addAttachment $parameters
   * @access public
   */
  public function addAttachment(addAttachment $parameters)
  {
    return $this->__soapCall('addAttachment', array($parameters));
  }

  /**
   * 
   * @param getCampaignFeedback $parameters
   * @access public
   */
  public function getCampaignFeedback(getCampaignFeedback $parameters)
  {
    return $this->__soapCall('getCampaignFeedback', array($parameters));
  }

  /**
   * 
   * @param requestCampaignFeedbackReport $parameters
   * @access public
   */
  public function requestCampaignFeedbackReport(requestCampaignFeedbackReport $parameters)
  {
    return $this->__soapCall('requestCampaignFeedbackReport', array($parameters));
  }

  /**
   * 
   * @param uploadMediaContent $parameters
   * @access public
   */
  public function uploadMediaContent(uploadMediaContent $parameters)
  {
    return $this->__soapCall('uploadMediaContent', array($parameters));
  }

  /**
   * 
   * @param getCampaign $parameters
   * @access public
   */
  public function getCampaign(getCampaign $parameters)
  {
    return $this->__soapCall('getCampaign', array($parameters));
  }

  /**
   * 
   * @param findCampaignsByNameOrSubject $parameters
   * @access public
   */
  public function findCampaignsByNameOrSubject(findCampaignsByNameOrSubject $parameters)
  {
    return $this->__soapCall('findCampaignsByNameOrSubject', array($parameters));
  }

  /**
   * 
   * @param findCampaignsByStatus $parameters
   * @access public
   */
  public function findCampaignsByStatus(findCampaignsByStatus $parameters)
  {
    return $this->__soapCall('findCampaignsByStatus', array($parameters));
  }

  /**
   * 
   * @param findCampaignsSentBetween $parameters
   * @access public
   */
  public function findCampaignsSentBetween(findCampaignsSentBetween $parameters)
  {
    return $this->__soapCall('findCampaignsSentBetween', array($parameters));
  }

  /**
   * 
   * @param findCampaignsByNote $parameters
   * @access public
   */
  public function findCampaignsByNote(findCampaignsByNote $parameters)
  {
    return $this->__soapCall('findCampaignsByNote', array($parameters));
  }

  /**
   * 
   * @param findCampaignsByModel $parameters
   * @access public
   */
  public function findCampaignsByModel(findCampaignsByModel $parameters)
  {
    return $this->__soapCall('findCampaignsByModel', array($parameters));
  }

  /**
   * 
   * @param findTriggerableCampaigns $parameters
   * @access public
   */
  public function findTriggerableCampaigns(findTriggerableCampaigns $parameters)
  {
    return $this->__soapCall('findTriggerableCampaigns', array($parameters));
  }

  /**
   * 
   * @param findPeriodicCampaigns $parameters
   * @access public
   */
  public function findPeriodicCampaigns(findPeriodicCampaigns $parameters)
  {
    return $this->__soapCall('findPeriodicCampaigns', array($parameters));
  }

  /**
   * 
   * @param startSubscriberDataExchange $parameters
   * @access public
   */
  public function startSubscriberDataExchange(startSubscriberDataExchange $parameters)
  {
    return $this->__soapCall('startSubscriberDataExchange', array($parameters));
  }

  /**
   * 
   * @param getSubscriberDataExchangeStatus $parameters
   * @access public
   */
  public function getSubscriberDataExchangeStatus(getSubscriberDataExchangeStatus $parameters)
  {
    return $this->__soapCall('getSubscriberDataExchangeStatus', array($parameters));
  }

  /**
   * 
   * @param setCampaignRecurrency $parameters
   * @access public
   */
  public function setCampaignRecurrency(setCampaignRecurrency $parameters)
  {
    return $this->__soapCall('setCampaignRecurrency', array($parameters));
  }

  /**
   * 
   * @param sendCampaign $parameters
   * @access public
   */
  public function sendCampaign(sendCampaign $parameters)
  {
    return $this->__soapCall('sendCampaign', array($parameters));
  }

  /**
   * 
   * @param cloneAndSendCampaign $parameters
   * @access public
   */
  public function cloneAndSendCampaign(cloneAndSendCampaign $parameters)
  {
    return $this->__soapCall('cloneAndSendCampaign', array($parameters));
  }

  /**
   * 
   * @param getRequestStatus $parameters
   * @access public
   */
  public function getRequestStatus(getRequestStatus $parameters)
  {
    return $this->__soapCall('getRequestStatus', array($parameters));
  }

  /**
   * 
   * @param createCampaign $parameters
   * @access public
   */
  public function createCampaign(createCampaign $parameters)
  {
    return $this->__soapCall('createCampaign', array($parameters));
  }

  /**
   * 
   * @param getXMLDeliveries $parameters
   * @access public
   */
  public function getXMLDeliveries(getXMLDeliveries $parameters)
  {
    return $this->__soapCall('getXMLDeliveries', array($parameters));
  }

  /**
   * 
   * @param getXMLDeliveryTransitions $parameters
   * @access public
   */
  public function getXMLDeliveryTransitions(getXMLDeliveryTransitions $parameters)
  {
    return $this->__soapCall('getXMLDeliveryTransitions', array($parameters));
  }

  /**
   * 
   * @param cancelCampaign $parameters
   * @access public
   */
  public function cancelCampaign(cancelCampaign $parameters)
  {
    return $this->__soapCall('cancelCampaign', array($parameters));
  }

  /**
   * 
   * @param getMessageModelById $parameters
   * @access public
   */
  public function getMessageModelById(getMessageModelById $parameters)
  {
    return $this->__soapCall('getMessageModelById', array($parameters));
  }

  /**
   * 
   * @param createMessageModel $parameters
   * @access public
   */
  public function createMessageModel(createMessageModel $parameters)
  {
    return $this->__soapCall('createMessageModel', array($parameters));
  }

  /**
   * 
   * @param findMessageModels $parameters
   * @access public
   */
  public function findMessageModels(findMessageModels $parameters)
  {
    return $this->__soapCall('findMessageModels', array($parameters));
  }

  /**
   * 
   * @param findMessageModelsBySubscriberSource $parameters
   * @access public
   */
  public function findMessageModelsBySubscriberSource(findMessageModelsBySubscriberSource $parameters)
  {
    return $this->__soapCall('findMessageModelsBySubscriberSource', array($parameters));
  }

  /**
   * 
   * @param getSubscriberSourceFilter $parameters
   * @access public
   */
  public function getSubscriberSourceFilter(getSubscriberSourceFilter $parameters)
  {
    return $this->__soapCall('getSubscriberSourceFilter', array($parameters));
  }

  /**
   * 
   * @param addSubscriberSourceFilter $parameters
   * @access public
   */
  public function addSubscriberSourceFilter(addSubscriberSourceFilter $parameters)
  {
    return $this->__soapCall('addSubscriberSourceFilter', array($parameters));
  }

  /**
   * 
   * @param findFiltersBySubscriberSource $parameters
   * @access public
   */
  public function findFiltersBySubscriberSource(findFiltersBySubscriberSource $parameters)
  {
    return $this->__soapCall('findFiltersBySubscriberSource', array($parameters));
  }

  /**
   * 
   * @param getSubscriberSource $parameters
   * @access public
   */
  public function getSubscriberSource(getSubscriberSource $parameters)
  {
    return $this->__soapCall('getSubscriberSource', array($parameters));
  }

  /**
   * 
   * @param findSubscriberSources $parameters
   * @access public
   */
  public function findSubscriberSources(findSubscriberSources $parameters)
  {
    return $this->__soapCall('findSubscriberSources', array($parameters));
  }

  /**
   * 
   * @param getSubscriber $parameters
   * @access public
   */
  public function getSubscriber(getSubscriber $parameters)
  {
    return $this->__soapCall('getSubscriber', array($parameters));
  }

  /**
   * 
   * @param addSubscriber $parameters
   * @access public
   */
  public function addSubscriber(addSubscriber $parameters)
  {
    return $this->__soapCall('addSubscriber', array($parameters));
  }

  /**
   * 
   * @param addSubscribers $parameters
   * @access public
   */
  public function addSubscribers(addSubscribers $parameters)
  {
    return $this->__soapCall('addSubscribers', array($parameters));
  }

  /**
   * 
   * @param removeSubscriber $parameters
   * @access public
   */
  public function removeSubscriber(removeSubscriber $parameters)
  {
    return $this->__soapCall('removeSubscriber', array($parameters));
  }

  /**
   * 
   * @param findSubscribersIncludedInFilter $parameters
   * @access public
   */
  public function findSubscribersIncludedInFilter(findSubscribersIncludedInFilter $parameters)
  {
    return $this->__soapCall('findSubscribersIncludedInFilter', array($parameters));
  }

  /**
   * 
   * @param borrowToken $parameters
   * @access public
   */
  public function borrowToken(borrowToken $parameters)
  {
    return $this->__soapCall('borrowToken', array($parameters));
  }

  /**
   * 
   * @param keepaliveToken $parameters
   * @access public
   */
  public function keepaliveToken(keepaliveToken $parameters)
  {
    return $this->__soapCall('keepaliveToken', array($parameters));
  }

  /**
   * 
   * @param invalidateToken $parameters
   * @access public
   */
  public function invalidateToken(invalidateToken $parameters)
  {
    return $this->__soapCall('invalidateToken', array($parameters));
  }

  /**
   * 
   * @param findCampaigns $parameters
   * @access public
   */
  public function findCampaigns(findCampaigns $parameters)
  {
    return $this->__soapCall('findCampaigns', array($parameters));
  }

  /**
   * 
   * @param getAttachmentByCampaignId $parameters
   * @access public
   */
  public function getAttachmentByCampaignId(getAttachmentByCampaignId $parameters)
  {
    return $this->__soapCall('getAttachmentByCampaignId', array($parameters));
  }

  /**
   * 
   * @param getTrackedLinks $parameters
   * @access public
   */
  public function getTrackedLinks(getTrackedLinks $parameters)
  {
    return $this->__soapCall('getTrackedLinks', array($parameters));
  }

  /**
   * 
   * @param triggerDeliveryById $parameters
   * @access public
   */
  public function triggerDeliveryById(triggerDeliveryById $parameters)
  {
    return $this->__soapCall('triggerDeliveryById', array($parameters));
  }

  /**
   * 
   * @param triggerDeliveryByAlias $parameters
   * @access public
   */
  public function triggerDeliveryByAlias(triggerDeliveryByAlias $parameters)
  {
    return $this->__soapCall('triggerDeliveryByAlias', array($parameters));
  }

  /**
   * 
   * @param addSubscriberSource $parameters
   * @access public
   */
  public function addSubscriberSource(addSubscriberSource $parameters)
  {
    return $this->__soapCall('addSubscriberSource', array($parameters));
  }

  /**
   * 
   * @param countSubscribersIncludedInFilter $parameters
   * @access public
   */
  public function countSubscribersIncludedInFilter(countSubscribersIncludedInFilter $parameters)
  {
    return $this->__soapCall('countSubscribersIncludedInFilter', array($parameters));
  }

  /**
   * 
   * @param findSubscribers $parameters
   * @access public
   */
  public function findSubscribers(findSubscribers $parameters)
  {
    return $this->__soapCall('findSubscribers', array($parameters));
  }

  /**
   * 
   * @param getCryptoKey $parameters
   * @access public
   */
  public function getCryptoKey(getCryptoKey $parameters)
  {
    return $this->__soapCall('getCryptoKey', array($parameters));
  }

  /**
   * 
   * @param archiveSubscriberSourceFilter $parameters
   * @access public
   */
  public function archiveSubscriberSourceFilter(archiveSubscriberSourceFilter $parameters)
  {
    return $this->__soapCall('archiveSubscriberSourceFilter', array($parameters));
  }

  /**
   * 
   * @param reuseSubscriberSourceFilter $parameters
   * @access public
   */
  public function reuseSubscriberSourceFilter(reuseSubscriberSourceFilter $parameters)
  {
    return $this->__soapCall('reuseSubscriberSourceFilter', array($parameters));
  }

  /**
   * 
   * @param findArchivedFiltersBySubscriberSource $parameters
   * @access public
   */
  public function findArchivedFiltersBySubscriberSource(findArchivedFiltersBySubscriberSource $parameters)
  {
    return $this->__soapCall('findArchivedFiltersBySubscriberSource', array($parameters));
  }

  /**
   * 
   * @param getArchivedSubscriberSourceFilter $parameters
   * @access public
   */
  public function getArchivedSubscriberSourceFilter(getArchivedSubscriberSourceFilter $parameters)
  {
    return $this->__soapCall('getArchivedSubscriberSourceFilter', array($parameters));
  }

  /**
   * 
   * @param modifySubscriberSubscriptionStatusByMailqId $parameters
   * @access public
   */
  public function modifySubscriberSubscriptionStatusByMailqId(modifySubscriberSubscriptionStatusByMailqId $parameters)
  {
    return $this->__soapCall('modifySubscriberSubscriptionStatusByMailqId', array($parameters));
  }

  /**
   * 
   * @param sendImmediateMessage $parameters
   * @access public
   */
  public function sendImmediateMessage(sendImmediateMessage $parameters)
  {
    return $this->__soapCall('sendImmediateMessage', array($parameters));
  }

  /**
   * 
   * @param sendImmediateMessageSidCid $parameters
   * @access public
   */
  public function sendImmediateMessageSidCid(sendImmediateMessageSidCid $parameters)
  {
    return $this->__soapCall('sendImmediateMessageSidCid', array($parameters));
  }

  /**
   * 
   * @param sendImmediateMessageSDataCData $parameters
   * @access public
   */
  public function sendImmediateMessageSDataCData(sendImmediateMessageSDataCData $parameters)
  {
    return $this->__soapCall('sendImmediateMessageSDataCData', array($parameters));
  }

  /**
   * 
   * @param sendImmediateMessageSIdCData $parameters
   * @access public
   */
  public function sendImmediateMessageSIdCData(sendImmediateMessageSIdCData $parameters)
  {
    return $this->__soapCall('sendImmediateMessageSIdCData', array($parameters));
  }

  /**
   * 
   * @param sendImmediateMessageSdataCAl $parameters
   * @access public
   */
  public function sendImmediateMessageSdataCAl(sendImmediateMessageSdataCAl $parameters)
  {
    return $this->__soapCall('sendImmediateMessageSdataCAl', array($parameters));
  }

  /**
   * 
   * @param sendImmediateMessageSidCAl $parameters
   * @access public
   */
  public function sendImmediateMessageSidCAl(sendImmediateMessageSidCAl $parameters)
  {
    return $this->__soapCall('sendImmediateMessageSidCAl', array($parameters));
  }

  /**
   * 
   * @param sendImmediateMessageSDataCIdCA $parameters
   * @access public
   */
  public function sendImmediateMessageSDataCIdCA(sendImmediateMessageSDataCIdCA $parameters)
  {
    return $this->__soapCall('sendImmediateMessageSDataCIdCA', array($parameters));
  }

  /**
   * 
   * @param sendImmediateMessageSidCidCA $parameters
   * @access public
   */
  public function sendImmediateMessageSidCidCA(sendImmediateMessageSidCidCA $parameters)
  {
    return $this->__soapCall('sendImmediateMessageSidCidCA', array($parameters));
  }

  /**
   * 
   * @param sendImmediateMessageSDataCDataCA $parameters
   * @access public
   */
  public function sendImmediateMessageSDataCDataCA(sendImmediateMessageSDataCDataCA $parameters)
  {
    return $this->__soapCall('sendImmediateMessageSDataCDataCA', array($parameters));
  }

  /**
   * 
   * @param sendImmediateMessageSIdCDataCA $parameters
   * @access public
   */
  public function sendImmediateMessageSIdCDataCA(sendImmediateMessageSIdCDataCA $parameters)
  {
    return $this->__soapCall('sendImmediateMessageSIdCDataCA', array($parameters));
  }

  /**
   * 
   * @param sendImmediateMessageSdataCAlCA $parameters
   * @access public
   */
  public function sendImmediateMessageSdataCAlCA(sendImmediateMessageSdataCAlCA $parameters)
  {
    return $this->__soapCall('sendImmediateMessageSdataCAlCA', array($parameters));
  }

  /**
   * 
   * @param sendImmediateMessageSidCAlCA $parameters
   * @access public
   */
  public function sendImmediateMessageSidCAlCA(sendImmediateMessageSidCAlCA $parameters)
  {
    return $this->__soapCall('sendImmediateMessageSidCAlCA', array($parameters));
  }

}
