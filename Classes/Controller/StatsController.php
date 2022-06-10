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

/**
 * Class ChatsController
 *
 * Provides commandline interface to cleanup past bookings
 *
 * @package Ubl\SupportchatStats\Controller
 */
class StatsController extends BaseAbstractController
{

    /**
     * Display content of a chat took place before
     *
     * @access public
     * @return void
     */
    public function selectedPeriodAction()
    {
        // Display statistic data of indicated period

        // Display chat how en somme

        // Display chat how many per month

        // Display on which weekdays

        // Display on which hours
    }


    /**
     * Display content of a chat took place before
     *
     * @access public
     * @return void
     */
    public function overviewAction()
    {
        // Calendar picker / clickable /
            // Get MIN month and year
            // SELECT MONTH(FROM_UNIXTIME(crdate)),YEAR(FROM_UNIXTIME(crdate)), count(*) FROM tx_supportchat_chats GROUP BY 1,2;
            // Get current

        // Counted chat over all years
        // SELECT MONTH(FROM_UNIXTIME(crdate)),YEAR(FROM_UNIXTIME(crdate)), count(*) FROM tx_supportchat_chats GROUP BY 1,2;
    }

}