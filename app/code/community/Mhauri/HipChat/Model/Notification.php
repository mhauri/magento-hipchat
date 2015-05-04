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

class Mhauri_HipChat_Model_Notification extends Mhauri_HipChat_Model_Abstract
{

    /**
     * send message to room
     */
    public function send()
    {
        if(!$this->isEnabled()) {
            Mage::log('HipChat Notifications are not enabled!', Zend_Log::ERR, self::LOG_FILE, true);
            return false;
        }

        $params = array(
            'room_id'   => $this->getRoomId(),
            'from_name' => $this->getFromName(),
            'message'   => $this->getMessage(),
            'notify'    => $this->getNotify(),
            'color'     => $this->getColor()
        );

        if(Mage::getStoreConfig(Mhauri_HipChat_Model_Abstract::USE_QUEUE, 0)) {
            Mage::getModel('mhauri_hipchat/queue')->addMessageToQueue($params);
        } else {
            try {
                $this->sendMessage($params);
            } catch (Exception $e) {
                Mage::log($e->getMessage(), Zend_Log::ERR, Mhauri_HipChat_Model_Abstract::LOG_FILE);
            }
        }
        return true;
    }
}
