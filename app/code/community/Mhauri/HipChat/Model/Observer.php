<?php

class Mhauri_HipChat_Model_Notification
{
    private $_notificationModel = null;

    public function __construct()
    {
        $this->_notificationModel = Mage::getSingleton('mhauri_hipchat/notification');
    }

    public function notifyNewOrder($observer)
    {
        $test = $observer;
        $this->_notificationModel->setMessage('test')->send();
    }

    public function notifyUserLogin($observer)
    {
        $test = $observer;
        $this->_notificationModel->setMessage('test')->send();
    }
}
