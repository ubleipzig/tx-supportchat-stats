<?php
/**
 * Class chats repository
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
use Ubl\Supportchat\Domain\Repository\ChatsRepository;

/**
 * Class Chats
 *
 * Extends the booking repository of the booking extension
 *
 * @package Ubl\SupportchatStats\Domain\Repository
 */
class Chats extends ChatsRepository
{
    /**
     * Get amount of chats per year
     *
     * @return array
     * @access public
     */
    public function countChatsPerYear()
    {
        $queryBuilder = $this->getConnectionForTable('tx_supportchat_chats');
        return $queryBuilder
            ->addSelectLiteral(
                'YEAR(FROM_UNIXTIME(crdate)) AS year',
                $queryBuilder->expr()->count('*', 'cnt')
            )
            ->from('tx_supportchat_chats')
            ->groupBy('year')
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