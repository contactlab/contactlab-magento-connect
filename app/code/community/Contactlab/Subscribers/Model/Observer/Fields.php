<?php

/**
 * Observer for manage uk table.
 */
class Contactlab_Subscribers_Model_Observer_Fields extends Mage_Core_Model_Abstract {
    
    private function fillModel($model,$parameters) {
       //get model data from query
        $model->addData($parameters);
        //parameter "email" has another name: we can't change the original or newsletter observer
        //won't work
        $model->setSubscriberEmail($parameters['email']);
        //privacy needs post-elaboration bcause it's a checkbox
        if($model->hasData('privacy'))
            $model->setPrivacyAccepted($parameters['privacy'] == 'on');
        else
            $model->setPrivacyAccepted(false);
        /**
         * Date of birth needs post-elaboration too: use locale date format
         * to translate? 
         * Also, is it kosher to use php date functions?
         */
        $dateformat = Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        //Translate date according to locale date format
        if($model->hasData('dob')){
            if(mb_strlen($model->getDob()) < 6){
                Mage::helper('contactlab_commons')->logInfo('Invalid Dob: nulling');
                $model->unsDob();
            }
            else{
            Mage::helper('contactlab_commons')->logInfo(print_r($model->getData('dob'),true));
            $datearr = strptime($model->getDob(), $dateformat);
            Mage::helper('contactlab_commons')->logInfo(print_r($datearr,true));
            $model->setDob(($datearr['tm_year'] + 1900) . '-' . ($datearr['tm_mon'] + 1) . '-' . $datearr['tm_mday']);
            Mage::helper('contactlab_commons')->logInfo(print_r($model->getDob(),true));
            }
        }
        return $model;
    }

    /**
     * Controller action pre dispatch, queues additional fields 
     * triggered by new subscriber action
     */
    public function controllerActionPreDispatch($observer) {
        $action = $observer->getEvent()->getControllerAction();
        Mage::helper('contactlab_commons')->logInfo('PREDISPATCH:' . print_r($action->getFullActionName(),true));
        $request = $action->getRequest();
        if(!$request->isPost()) return; //probably unnecessary
        if(!($action->getFullActionName() === 'newsletter_subscriber_new')) return;
        
        Mage::helper('contactlab_commons')->logInfo('PREDISPATCH 2:' . print_r($action->getFullActionName(),true));
        $parameters = $request->getParams();
        
        $newsubs = Mage::getModel("contactlab_subscribers/fields");
        
        //get model data from query
        $newsubs->setData($parameters);
        //parameter "email" has another name: we can't change the original or newsletter observer
        //won't work
        $newsubs->setSubscriberEmail($parameters['email']);
        //privacy needs post-elaboration bcause it's a checkbox
        if($newsubs->hasData('privacy'))
            $newsubs->setPrivacyAccepted($parameters['privacy'] == 'on');
        else
            $newsubs->setPrivacyAccepted(false);
        /**
         * Date of birth needs post-elaboration too: use locale date format
         * to translate? 
         * Also, is it kosher to use php date functions?
         */
        $dateformat = Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        //Translate date according to locale date format
        if($newsubs->hasData('dob')){
            if(mb_strlen($newsubs->getDob()) < 6){
                Mage::helper('contactlab_commons')->logInfo('Invalid Dob: nulling');
                $newsubs->unsDob();
            }
            else{
            Mage::helper('contactlab_commons')->logInfo(print_r($newsubs->getData('dob'),true));
            $datearr = strptime($newsubs->getDob(), $dateformat);
            Mage::helper('contactlab_commons')->logInfo(print_r($datearr,true));
            $newsubs->setDob(($datearr['tm_year'] + 1900) . '-' . ($datearr['tm_mon'] + 1) . '-' . $datearr['tm_mday']);
            Mage::helper('contactlab_commons')->logInfo(print_r($newsubs->getDob(),true));
            }
        }
        /**
         * TODO: Server-side data validation
         */
        Mage::helper('contactlab_commons')->logInfo('SAVING FIELDS FROM PREDISPATCH:');
        $newsubs->save();
    }
    
    public function afterSubscriberSaved($observer){
        Mage::helper('contactlab_commons')->logInfo('AFTERSUBSAVED:' . print_r($observer->getEvent()->getDataObject(),true));
        $email = $observer->getEvent()->getDataObject()->getSubscriberEmail();
        $subs = Mage::getModel("contactlab_subscribers/fields")->load($email,'subscriber_email');
        if($subs->hasData()){
            /**
             * If the subscriber was already present in our table, this means that
             * the record was saved through a form submission
             */
            /*
             * Set subscriberId only if not set yet
             */
            if(!$subs->hasSubscriberId()){
                Mage::helper('contactlab_commons')->logInfo('SAVING FIELDS FROM afterSubscriberSaved 1:');
                $subs->setSubscriberId($observer->getDataObject()->getSubscriberId())->save();
            };
        }else{
            /*
             * Otherwise, subscriber was already a customer, so we require customer id
             */
            Mage::helper('contactlab_commons')->logInfo('empty data : ' . print_r($observer->getDataObject()->getData(),true));
            if(!$observer->getDataObject()->hasCustomerId())
                return;
                //Mage::throwException($this->__('Subscriber is neither guest or customer'));
            
            $custid = $observer->getDataObject()->getCustomerId();
            /**
             * Transfer all fields from customer data
             */
            $custmodel = Mage::getModel("customer/customer")->load($custid);
            $subs->setFirstName($custmodel->getFirstname());
            $subs->setLastName($custmodel->getLastname());
            $subs->setDob($custmodel->getDob());
            $subs->setGender($custmodel->getGender());
            /*
             *  Manage info saved in address attribute
             *  Using default billing address
             */
            $address = Mage::getModel('customer/address')->load($custmodel->getDefaultBilling());
            
            $subs->setCity($address->getCity());
            $subs->setCompany($address->getCompany());
            $subs->setPhone($address->getTelephone());
            $subs->setAddress(implode(',',$address->getStreet()));
            $subs->setZipCode($address->getPostcode());
            $name = Mage::getModel('directory/country')->load($address->getCountryId())->getName();
            $subs->setCountry($name);
            $subs->setSubscriberEmail($observer->getDataObject()->getSubscriberEmail());
            Mage::helper('contactlab_commons')->logInfo('SAVING FIELDS FROM afterSubscriberSaved 2:');
            $subs->setSubscriberId($observer->getDataObject()->getSubscriberId())->save();
        }
        
            
    }

    public function beforeSubscriberDeleted($observer){
        //just remove record
        Mage::helper('contactlab_commons')->logInfo('BEFOREDELETE:' . print_r($observer->getEvent()->getDataObject(),true));
        $id = $observer->getEvent()->getDataObject()->getSubscriberId();
        Mage::getModel("contactlab_subscribers/fields")->load($id,'subscriber_id')->delete();
    }
    
    public function updateFields($params) {
        Mage::helper('contactlab_commons')->logInfo('UPDATEFIELDS:' . print_r($params,true));
        $fm = Mage::getModel('contactlab_subscribers/fields')->load($params->getEmail(),'subscriber_email');
        if($fm->hasData())
            $this->fillModel($fm, $params->getData())->save();
    }
    
}
