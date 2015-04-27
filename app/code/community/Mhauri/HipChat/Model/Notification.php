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

    const ENABLE_PATH                   = 'hipchat/general/enable';
    const LOG_PATH                      = 'hipchat/general/log';
    const TOKEN_PATH                    = 'hipchat/general/token';
    const ROOM_ID_PATH                  = 'hipchat/general/room_id';
    const FROM_NAME_PATH                = 'hipchat/general/from_name';

    const NEW_ORDER_PATH                = 'hipchat/notification/new_order';
    const ADMIN_USER_LOGIN_FAILED_PATH  = 'hipchat/notification/admin_user_login_failed';

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
        return Mage::getStoreConfig(self::ENABLE_PATH, 0);
    }

    /**
     * send message to room
     */
    public function send()
    {
        try {
            $this->_hipchat->message_room($this->getRoomId(), $this->getFromName(), $this->getMessage());
            if(Mage::getStoreConfig(self::LOG_PATH, 0)) {
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
