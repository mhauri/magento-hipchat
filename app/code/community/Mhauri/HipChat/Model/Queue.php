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

class Mhauri_HipChat_Model_Queue extends Mhauri_HipChat_Model_Abstract
{
    const MESSAGES_LIMIT_PER_CRON_RUN = 30;

    protected function _construct()
    {
        $this->_init('mhauri_hipchat/queue');
        parent::_construct();
    }

    /**
     * @param $params
     */
    public function addMessageToQueue($params)
    {
        $this->setMessageParams(serialize($params));

        try {
            $this->save();
        } catch(Exception $e) {
            Mage::log($e->getMessage(), Zend_log::ERR, Mhauri_HipChat_Model_Abstract::LOG_FILE);
        }
    }

    /**
     * Send all queued Messages
     */
    public function sendQueuedMessages()
    {
        $collection = Mage::getModel('mhauri_hipchat/queue')->getCollection()
            ->addOnlyForSendingFilter()
            ->setPageSize(self::MESSAGES_LIMIT_PER_CRON_RUN)
            ->setCurPage(1)
            ->load();

        foreach($collection as $message) {
            try {
                $this->sendMessage(unserialize($message->getMessageParams()));
                $message->setProcessedAt(Varien_Date::formatDate(true));
                $message->save();
            }
            catch (Exception $e) {
                Mage::log($e->getMessage(), Zend_Log::ERR, Mhauri_HipChat_Model_Abstract::LOG_FILE);
            }
            sleep(1);
        }
    }

    /**
     * Remove sent messages
     * @return $this
     */
    public function cleanQueue()
    {
        $this->_getResource()->removeSentMessages();
        return $this;
    }
}
