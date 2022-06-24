<?php
/**
 * Class messages repository
 *
 * Copyright (C) Leipzig University Library 2022 <info@ub.uni-leipzig.de>
 *
 * @author  Frank Morgner <morgnerf@ub.uni-leipzig.de>
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License
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

namespace Ubl\SupportchatStats\Domain\Repository;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use Ubl\Supportchat\Domain\Repository\MessagesRepository;

/**
 * Class Messages
 *
 * Extends the booking repository of the booking extension
 *
 * @package Ubl\SupportchatStats\Domain\Repository
 */
class Messages extends MessagesRepository
{
    /**
     * Messages repository name
     *
     * @var string $messagesRepository
     * @access protected
     */
    protected $messagesRepository = "tx_supportchat_domain_model_messages";

    /**
     * Find all chat pids in table
     *
     * @return array
     * @access public
     */
    public function findAllChatPids(): array
    {
        $queryBuilder = $this->getConnectionForTable($this->messagesRepository);
        return $queryBuilder
            ->select('chat_pid')
            ->from($this->messagesRepository)
            ->groupBy('chat_pid')
            ->orderBy('chat_pid', 'DESC')
            ->execute()
            ->fetchAll();
    }

    /**
     * Find all chat entires by a chat_pid
     *
     * @param int $chatPid
     *
     * @return array
     * @access public
     */
    public function findMessagesByChatPid(int $chatPid): array
    {
        $queryBuilder = $this->getConnectionForTable($this->messagesRepository);
        return $queryBuilder
            ->select('name', 'message', 'tstamp')
            ->from($this->messagesRepository)
            ->where(
                $queryBuilder->expr()->eq(
                    'chat_pid',
                    $queryBuilder->createNamedParameter($chatPid, \PDO::PARAM_INT)
            ))
            ->orderBy('tstamp', 'ASC')
            ->execute()
            ->fetchAll();
    }

    /**
     * List all chats grouped by day and participants
     *
     * @return array
     * @access public
     */
    public function listAllChats(): array
    {
        $queryBuilder = $this->getConnectionForTable($this->messagesRepository);
        return $queryBuilder
            ->addSelectLiteral(
                'FROM_UNIXTIME(tstamp,"%Y-%m-%d") as day',
                'chat_pid',
                'name'
            )
            ->from($this->messagesRepository)
            ->add('groupBy', 'day DESC, chat_pid DESC, name')
            ->execute()
            ->fetchAll();
    }

    /**
     * Get connection for table
     *
     * @param string $tbl	Table name
     *
     * @return TYPO3\CMS\Core\Database\Connection
     * @access protected
     */
    protected function getConnectionForTable($tbl)
    {
        /** @var ConnectionPool $connectionPool */
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tbl);
    }
}