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

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

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
     * messagesRepository
     *
     * @var \Ubl\SupportchatStats\Domain\Repository\Chats
     * @inject
     */
    protected $chatsRepository;


    /**
     * Set up the doc header properly here
     *
     * @param ViewInterface $view
     */
    protected function initializeView($view)
    {
        parent::initializeView($view);
        $pathToJsLibrary = GeneralUtility::getIndpEnv('TYPO3_SITE_URL')
            . ExtensionManagementUtility::siteRelPath('supportchat-stats') . 'Resources/Public/JavaScript/';
        $pageRenderer = $this->view->getModuleTemplate()->getPageRenderer();
        $pageRenderer->addJsFile($pathToJsLibrary . 'chart-js/chart.min.js');
        $pageRenderer->addJSInlineCode('supportchat-stats', '
            const scChart = new Chart(
                document.getElementById("sc-stats-chart"),
                config            
            )
        ');
    }

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
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $chatsPerYear = $this->chatsRepository->countChatsPerYear();
        $data = $labels = "";
        foreach ($chatsPerYear AS $chat) {
            $data .= $data . ', ' . $chat["cnt"];
            $labels .= $labels .', ' . $chat["year"];
        }
        $pageRenderer->addJSInlineCode('supportchat-stats','
            <script>    
                const labels = [' . rtrim($labels, ", ") . '];
                const data = {
                  labels: labels,
                  datasets: [{
                    label: "My First dataset",
                    backgroundColor: "rgb(255, 99, 132)",
                    borderColor: "rgb(255, 99, 132)",
                    data: [' . rtrim($data, ", ") . '],
                }]  
                };
                
                const config = {
                    type: "line",
                    data: data,
                    options: {}
                };
            </script>
        ');

        // Calendar picker / clickable /
            // Get MIN month and year
            // SELECT MONTH(FROM_UNIXTIME(crdate)),YEAR(FROM_UNIXTIME(crdate)), count(*) FROM tx_supportchat_chats GROUP BY 1,2;
            // Get current

        // Counted chat over all years
        // SELECT MONTH(FROM_UNIXTIME(crdate)),YEAR(FROM_UNIXTIME(crdate)), count(*) FROM tx_supportchat_chats GROUP BY 1,2;
    }

}