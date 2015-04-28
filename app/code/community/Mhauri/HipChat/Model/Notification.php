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

require_once(Mage::getBaseDir('lib') . '/Atlassian/HipChat/HipChat.php');

class Mhauri_HipChat_Model_Notification extends Mage_Core_Model_Abstract
{

    const LOG_FILE                      = 'hipchat.log';
    const DEFAULT_SENDER                = 'Magento HipChat';

    const ENABLE_NOTIFICATION_PATH      = 'hipchat/general/enable_notification';
    const ENABLE_LOG_PATH               = 'hipchat/general/enable_log';

    const TOKEN_PATH                    = 'hipchat/api/token';
    const ROOM_ID_PATH                  = 'hipchat/api/room_id';
    const FROM_NAME_PATH                = 'hipchat/api/from_name';

    const NEW_ORDER_PATH                = 'hipchat/notification/new_order';
    const NEW_CUSTOMER_ACCOUNT_PATH     = 'hipchat/notification/new_customer_account';
    const ADMIN_USER_LOGIN_FAILED_PATH  = 'hipchat/notification/admin_user_login_failed';

    const COLOR_YELLOW                  = 'yellow';
    const COLOR_RED                     = 'red';
    const COLOR_GRAY                    = 'gray';
    const COLOR_GREEN                   = 'green';
    const COLOR_PURPLE                  = 'purple';
    const COLOR_RANDOM                  = 'random';

    /**
     * Store the HipChat Model
     * @var null
     */
    private $_hipchat       = null;

    /**
     * Store the Message
     * @var string
     */
    private $_message       = '';

    /**
     * Store the from name
     * @var string
     */
    private $_fromName      = null;

    /**
     * Store room id
     * @var null
     */
    private $_roomId        = null;

    /**
     * @var string
     */
    private $_color         = self::COLOR_YELLOW;

    /**
     * @var bool
     */
    private $_notify        = false;

    /**
     * Store token
     * @var null
     */
    private $_token         = null;


    public function _construct()
    {
        $this->setToken(Mage::getStoreConfig(self::TOKEN_PATH, 0));
        $this->setFromName(Mage::getStoreConfig(self::FROM_NAME_PATH, 0));
        $this->setRoomId(Mage::getStoreConfig(self::ROOM_ID_PATH, 0));

        if($this->isEnabled()) {
            $this->_hipchat = new HipChat\HipChat($this->getToken());
        }
        parent::_construct();
    }

    /**
     * @param string $token
     * @return $this
     */
    public function setToken($token)
    {
        if(is_string($token)) {
            $this->_token = $token;
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setFromName($name)
    {
        if(is_string($name)) {
            $this->_fromName = $name;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        if($this->_fromName) {
            return $this->_fromName;
        }

        return '';
    }

    /**
     * @param $id
     * @return $this
     */
    public function setRoomId($id)
    {
        if(is_numeric($id)) {
            $this->_roomId = $id;
        }

        return $this;
    }

    /**
     * @return null
     */
    public function getRoomId()
    {
        return $this->_roomId;
    }

    /**
     * @param $color
     * @return $this
     */
    public function setColor($color)
    {
        $allowedColors = array(
            self::COLOR_GRAY,
            self::COLOR_GREEN,
            self::COLOR_PURPLE,
            self::COLOR_RED,
            self::COLOR_YELLOW,
            self::COLOR_RANDOM
        );

        if(is_string($color) && in_array($color, $allowedColors)) {
            $this->_color = $color;
        }
        return $this;
    }

    /**
     * @param bool $notify
     * @return $this
     */
    public function setNotify($notify = false)
    {
        if(is_bool($notify)) {
            $this->_notify = $notify;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function getNotify()
    {
        return $this->_notify;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->_color;
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
     * @return mixed
     */
    public function isEnabled()
    {
        return Mage::getStoreConfig(self::ENABLE_NOTIFICATION_PATH, 0);
    }

    /**
     * send message to room
     */
    public function send()
    {
        try {
            $this->_hipchat->message_room(
                $this->getRoomId(),
                $this->getFromName(),
                $this->getMessage(),
                $this->getNotify(),
                $this->getColor()
            );
            if(Mage::getStoreConfig(self::ENABLE_LOG_PATH, 0)) {
                Mage::log('Message sent: ' . $this->_message, Zend_Log::INFO, self::LOG_FILE, true);
            }
        } catch(Exception $e) {
            $params = array(
                'from:'     => $this->getFromName(),
                'message:'  => $this->getMessage(),
                'token:'    => $this->getToken()
            );

            Mage::log($params, Zend_Log::ERR, self::LOG_FILE, true);
            Mage::log($e->getMessage(), Zend_Log::ERR, self::LOG_FILE, true);
        }
    }
}
