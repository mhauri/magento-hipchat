<?php

class Mhauri_HipChat_Model_Observer
{
    private $_notificationModel = null;

    private $_helper = null;

    public function __construct()
    {
        $this->_notificationModel = Mage::getSingleton('mhauri_hipchat/notification');
        $this->_helper = Mage::helper('mhauri_hipchat');
    }

    /**
     * Send a notification when a new order was placed
     * @param $observer
     */
    public function notifyNewOrder($observer)
    {
        if($this->_getConfig(Mhauri_HipChat_Model_Notification::NEW_ORDER_PATH)) {
            $message = $this->_helper->__('<strong>A new order has been placed.</strong><br /><strong>Order Id:</strong> %s<br /><strong>Name:</strong> %s %s<br /><strong>Amount:</strong> %s',
                $observer->getOrder()->getIncrementId(),
                $observer->getOrder()->getCustomer()->getFirstname(),
                $observer->getOrder()->getCustomer()->getLastname(),
                $observer->getOrder()->getQuoteBaseGrandTotal());

            $this->_notificationModel->setMessage($message)->send();
        }
    }

    /**
     * Send a notification when admin user login failed
     * @param $observer
     */
    public function notifiyAdminUserLoginFailed($observer)
    {
        if($this->_getConfig(Mhauri_HipChat_Model_Notification::ADMIN_USER_LOGIN_FAILED_PATH)) {
            $this->_notificationModel->setMessage($this->_helper->__('Admin user login failed with username: %s', $observer->getUserName()))->send();
        }
    }

    private function _getConfig($path)
    {
        return Mage::getStoreConfig($path, 0);
    }
}
