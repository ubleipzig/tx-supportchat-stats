<?php
declare(strict_types=1);

/**
 * Class BaseAdministrationController
 *
 * Copyright (C) Leipzig University Library 2022 <info@ub.uni-leipzig.de>
 *
 * @author  Frank Morgner <morgnerf@ub.uni-leipzig.de>
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
namespace Ubl\SupportchatStats\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;

/**
 * Class ChatsController
 *
 * Provides commandline interface to cleanup past bookings
 *
 * @package Ubl\SupportchatStats\Controller
 */
class ChatsController extends BaseAbstractController
{
    /**
     * messagesRepository
     *
     * @var \Ubl\SupportchatStats\Domain\Repository\Messages
     * @Extbase\Inject
     */
    protected $messagesRepository;

    /**
     * Display content of a chat took place before
     *
     * @param int chatId
     *
     * @access public
     * @return void
     */
    public function displayAction(int $chatId = null)
    {
        $chatPids = array_column($this->messagesRepository->findAllChatPids(), 'chat_pid');
        $currentPid = ($chatId != null)
            ? (int)filter_var($chatId, FILTER_SANITIZE_NUMBER_INT) : (int)$chatPids[0];
        $currentKey = array_search($currentPid, $chatPids);

        $messages = $this->messagesRepository->findMessagesByChatPid($currentPid);
        $this->view->assign('messages', $messages);
        $this->view->assign('currentId', $currentPid);
        $this->view->assign('subsequentId', $chatPids[$currentKey-1]);
        $this->view->assign('previousId', $chatPids[$currentKey+1]);
        $this->view->assign('subsequentTenIds', $chatPids[$currentKey-10]);
        $this->view->assign('previousTenIds', $chatPids[$currentKey+10]);
        $this->view->assign(
            'listAllChats',
            $this->getChatsByDayAndPid($this->messagesRepository->listAllChats())
        );
    }

    /**
     * Grouped list of chats by date and chat pid
     *
     * @param $listOfChats
     *
     * @return array $listOfChatsPerDay
     * @access private
     */
    private function getChatsByDayAndPid($listOfChats): array
    {
        if (count($listOfChats) == 0) {
            $this->addFlashMessageHelper(
                'notice.no.data',
                \TYPO3\CMS\Core\Messaging\FlashMessage::INFO
            );
        }
        $listOfChatsPerDay = [];
        foreach ($listOfChats as $chat)
        {
            $listOfChatsPerDay[$chat['day']][$chat['chat_pid']][] =
                ($chat['name'] !== "" XOR $chat['name'] === "Gast")
                    ? $chat['name'] : $this->translate("module.chats.client");
        }
        return $listOfChatsPerDay;
    }
}