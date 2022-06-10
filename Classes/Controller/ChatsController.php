<?php
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
     * @inject
     */
    protected $messagesRepository;

    /**
     * Display content of a chat took place before
     *
     * @access public
     * @return void
     */
    public function displayAction()
    {
        $chatPids = $this->messagesRepository->findAllChatPids();
        $firstPid = (end($chatPids))["chat_pid"];
        $lastPid = $chatPids[0]["chat_pid"];

        $this->request = GeneralUtility::_GP('supportchatstats');
        $currentPid = (isset($this->request['currentPid']))
            ? filter_var($this->request['currentPid'], FILTER_SANITIZE_NUMBER_INT) : $lastPid;

        $messages = $this->messagesRepository->findMessagesByChatPid($currentPid);
        var_dump($messages); exit;




        // Previous Last Navigation

        // Show Chat Content
    }
}