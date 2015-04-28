<?php
/**
 * Copyright (c) 2015, Marcel Hauri
 * All rights reserved.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright Copyright 2015, Marcel Hauri (https://github.com/mhauri/magento-hipchat/)
 *
 * @category Notification
 * @package mhauri-hipchat
 * @author Marcel Hauri <marcel@hauri.me>
 */

class Mhauri_HipChat_Model_Observer
{
    private $_notificationModel = null;

    private $_helper = null;

    public function __construct()
    {
        $this->_notificationModel   = Mage::getSingleton('mhauri_hipchat/notification');
        $this->_helper              = Mage::helper('mhauri_hipchat');
    }

    /**
     * Send a notification when a new order was placed
     * @param $observer
     */
    public function notifyNewOrder($observer)
    {
        if($this->_getConfig(Mhauri_HipChat_Model_Notification::NEW_ORDER_PATH)) {
            $message = $this->_helper->__('<strong>A new order has been placed.</strong><br /> <strong>Order ID:</strong> %s, <strong>Name:</strong> %s %s, <strong>Amount:</strong> %s %s',
                $observer->getOrder()->getIncrementId(),
                $observer->getOrder()->getCustomer()->getFirstname(),
                $observer->getOrder()->getCustomer()->getLastname(),
                $observer->getOrder()->getQuoteBaseGrandTotal(),
                $observer->getOrder()->getOrderCurrencyCode()
                );

            $this->_notificationModel
                ->setMessage($message)
                ->setColor(Mhauri_HipChat_Model_Notification::COLOR_GREEN)
                ->send();
        }
    }

    /**
     * Send a notification when a new customer account is created
     * @param $observer
     */
    public function notifyNewCustomer($observer)
    {
        if($this->_getConfig(Mhauri_HipChat_Model_Notification::NEW_CUSTOMER_ACCOUNT_PATH)) {
            $this->_notificationModel
                ->setMessage($this->_helper->__('A new customer account was created'))
                ->setColor(Mhauri_HipChat_Model_Notification::COLOR_YELLOW)
                ->send();
        }
    }

    /**
     * Send a notification when admin user login failed
     * @param $observer
     */
    public function notifyAdminUserLoginFailed($observer)
    {
        if($this->_getConfig(Mhauri_HipChat_Model_Notification::ADMIN_USER_LOGIN_FAILED_PATH)) {
            $this->_notificationModel
                ->setMessage($this->_helper->__('Admin user login failed with username: %s', $observer->getUserName()))
                ->setColor(Mhauri_HipChat_Model_Notification::COLOR_RED)
                ->send();
        }
    }

    private function _getConfig($path)
    {
        return Mage::getStoreConfig($path, 0);
    }
}
