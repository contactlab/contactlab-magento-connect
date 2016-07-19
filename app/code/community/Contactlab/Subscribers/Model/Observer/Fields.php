<?php

/**
 * Observer for manage uk table.
 */
class Contactlab_Subscribers_Model_Observer_Fields extends Mage_Core_Model_Abstract
{

    /**
     * Fill Model.
     * @param Contactlab_Subscribers_Model_Fields $model
     * @param array $parameters
     * @return mixed
     */
    private function fillModel(Contactlab_Subscribers_Model_Fields $model, array $parameters)
    {
        $model->addData($parameters);
        // Parameter "email" has another name: we can't change the original or
        // newsletter observer won't work
        $model->setSubscriberEmail($parameters['email']);

        // Privacy needs post-elaboration bcause it's a checkbox
        if ($model->hasData('privacy')) {
            $model->setPrivacyAccepted($parameters['privacy'] == 'on');
        } else {
            $model->setPrivacyAccepted(false);
        }

        /**
         * Date of birth needs post-elaboration too: use locale date format
         * to translate?
         * Also, is it kosher to use php date functions?
         */
        $dateFormat = Mage::app()->getLocale()
            ->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        // Translate date according to locale date format
        if ($model->hasData('dob')) {
            if (mb_strlen($model->getDob()) < 6) {
                $model->unsDob();
            } else {
                $dateArray = strptime($model->getDob(), $dateFormat);
                $model->setDob(($dateArray['tm_year'] + 1900) . '-' . ($dateArray['tm_mon'] + 1) . '-' . $dateArray['tm_mday']);
            }
        }
        return $model;
    }

    /**
     * Controller action pre dispatch, queues additional fields
     * triggered by new subscriber action
     * @param $observer
     * @throws Exception
     */
    public function controllerActionPreDispatch($observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        $request = $action->getRequest();
        if (!$request->isPost()) {
            // Probably unnecessary
            return;
        }
        if (!($action->getFullActionName() === 'newsletter_subscriber_new')) {
            return;
        }

        $parameters = $request->getParams();

        /** @var $fields Contactlab_Subscribers_Model_Fields */
        $fields = Mage::getModel("contactlab_subscribers/fields");
        $fields->setData($parameters);
        // Parameter "email" has another name: we can't change the original or newsletter observer won't work
        $fields->setSubscriberEmail($parameters['email']);
        //privacy needs post-elaboration because it's a checkbox
        if ($fields->hasData('privacy')) {
            $fields->setPrivacyAccepted($parameters['privacy'] == 'on');
        } else {
            $fields->setPrivacyAccepted(false);
        }
        /**
         * Date of birth needs post-elaboration too: use locale date format
         * to translate?
         * Also, is it kosher to use php date functions?
         */
        $dateFormat = Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        // Translate date according to locale date format
        if ($fields->hasData('dob')) {
            if (mb_strlen($fields->getDob()) < 6) {
                $fields->unsDob();
            } else {
                $dateArray = strptime($fields->getDob(), $dateFormat);
                $fields->setDob(($dateArray['tm_year'] + 1900) . '-' . ($dateArray['tm_mon'] + 1) . '-' . $dateArray['tm_mday']);
            }
        }
        /**
         * TODO: Server-side data validation
         */
        $fields->save();
    }

    public function afterSubscriberSaved($observer)
    {
        Mage::helper('contactlab_commons')->logInfo('AFTERSUBSAVED:' . print_r($observer->getEvent()->getDataObject(), true));
        $email = $observer->getEvent()->getDataObject()->getSubscriberEmail();
        $subs = Mage::getModel("contactlab_subscribers/fields")->load($email, 'subscriber_email');
        if ($subs->hasData('entity_id')) {
            /**
             * If the subscriber was already present in our table, this means that
             * the record was saved through a form submission
             */
            /*
             * Set subscriberId only if not set yet
             */
            if (!$subs->hasSubscriberId()) {
                Mage::helper('contactlab_commons')->logInfo('SAVING FIELDS FROM afterSubscriberSaved 1:');
                $subs->setSubscriberId($observer->getDataObject()->getSubscriberId())->save();
            }
        } else {
            /*
             * Otherwise, subscriber was already a customer, so we require customer id
             */
            Mage::helper('contactlab_commons')->logInfo('empty data : ' . print_r($observer->getDataObject()->getData(), true));
            if (!$observer->getDataObject()->hasCustomerId()) {
                return;
            }

            $customerId = $observer->getDataObject()->getCustomerId();
            /**
             * Transfer all fields from customer data
             */
            $customer = Mage::getModel("customer/customer")->load($customerId);
            $subs->setFirstName($customer->getFirstname());
            $subs->setLastName($customer->getLastname());
            $subs->setDob($customer->getDob());
            $subs->setGender($customer->getGender());
            /*
             *  Manage info saved in address attribute
             *  Using default billing address
             */
            $address = Mage::getModel('customer/address')->load($customer->getDefaultBilling());

            $subs->setCity($address->getCity());
            $subs->setCompany($address->getCompany());
            $subs->setPhone($address->getTelephone());
            $subs->setAddress(implode(',', $address->getStreet()));
            $subs->setZipCode($address->getPostcode());
            $name = Mage::getModel('directory/country')->load($address->getCountryId())->getName();
            $subs->setCountry($name);
            $subs->setSubscriberEmail($observer->getDataObject()->getSubscriberEmail());
            $subs->setSubscriberId($observer->getDataObject()->getSubscriberId())->save();
        }


    }

    /**
     * Before subscriber deleted.
     * @param $observer
     * @throws Exception
     * @deprecated uses cascade
     */
    public function beforeSubscriberDeleted($observer)
    {
        /*$id = $observer->getEvent()->getDataObject()->getSubscriberId();
        Mage::getModel("contactlab_subscribers/fields")->load($id, 'subscriber_id')->delete();*/
    }

    /**
     * @param Varien_Event_Observer $params
     */
    public function updateFields(Varien_Event_Observer $params)
    {
        /** @var $fields Contactlab_Subscribers_Model_Fields */
        /*
         $fields = Mage::getModel('contactlab_subscribers/fields')
            ->load($params->getData('email'), 'subscriber_email');
        */
        $fields = $this->checkEditParams($params);

        if ($fields && $fields->hasData()) {
            $this->fillModel($fields, $params->getData())->save();
        }
    }

    private function checkEditParams($params) {
        //check params
        if (!$params->getData('chkhash')
            || !$params->getData('chkid')) {
            return null;
        }

        // Load the subscriber and the additional fields entity

        $subs = Mage::getModel('newsletter/subscriber')->load($params->getData('chkid'));
        if (!$subs->hasData('subscriber_id')) {
            return null;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        if (!$subs->hasSubscriberConfirmCode()) {
            return null;
        }

        if ($subs->getSubscriberConfirmCode() != $params->getData('chkhash')) {
            return null;
        }

        return  Mage::getModel('contactlab_subscribers/fields')->load($subs->getSubscriberId(), 'subscriber_id');

    }
}
