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

include_once 'HipChat/HipChat.php';

class Mhauri_HipChat_Model_Notification extends Mage_Core_Model_Abstract
{

    const LOG_FILE          = 'hipchat.log';

    const ENABLE_PATH       = 'hipchat/general/enable';
    const LOG_PATH          = 'hipchat/general/log';
    const TOKEN_PATH        = 'hipchat/general/token';
    const ROOM_ID_PATH      = 'hipchat/general/room_id';

    const NEW_ORDER_PATH    = 'hipchat/notification/new_order';

    private $_hipchat   = null;

    private $_message   = '';

    private $_from      = 'TEST';

    public function _construct()
    {
        parent::_construct();
        if($this->isEnabled()) {
            $this->_hipchat = new HipChat(Mage::getStoreConfig(self::TOKEN_PATH, 0));
        }
    }

    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        if(is_string($message)) {
            $this->_message = $message;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * send message
     */
    public function send()
    {
        if(Mage::getStoreConfig(self::LOG_PATH, 0)) {
            Mage::log($this->_message, Zend_Log::INFO, self::LOG_FILE, true);
        }

        $this->_hipchat->message_room($this->getRoomId(), $this->_from, $this->getMessage());
    }

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return Mage::getStoreConfig(self::ENABLE_PATH, 0);
    }

    /**
     * @return mixed
     */
    public function getRoomId()
    {
        return Mage::getStoreConfig(self::ROOM_ID_PATH, 0);
    }
}
