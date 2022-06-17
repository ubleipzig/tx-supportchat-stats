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
     * Initial controller
     *
     * @var string $initController
     * @access protected
     */
    protected $initController = "chatsPerYear";

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
        /**  @todo Integrate datalabels to chart
        $pageRenderer->addRequireJsConfiguration([
            'paths' => [
                'datalabels' => $pathToJsLibrary . 'libs/chartjs-plugin-datalabels.min.js'
            ],
            'shim' => [
                'datalabels' => ['exports' => 'ChartDataLabels']
            ]
        ]);*/
        $pageRenderer->addJSInlineCode('supportchat-stats-chart', '
            require(["' . $pathToJsLibrary . 'libs/chartjs.min.js"], function(Chart){
                Chart.defaults.font.size = 16;
                let scChart = new Chart(
                    document.getElementById("sc-stats-chart").getContext("2d"),
                    config            
                );
            });
        ');
    }

    /**
     * Initializes the current action
     *
     * @return void
     */
    public function indexAction()
    {
        $classNames = get_class_methods($this);
        foreach ($classNames as $name) {
            if (0 < preg_match('/^chats\w*$/', $name)) {
                $statsMethods[] = $name;
           }
        }
        $stats = GeneralUtility::_GP('statsSelect')
            ? filter_var(GeneralUtility::_GP('statsSelect'), FILTER_SANITIZE_STRING)
            : $this->initController;
        if (false === in_array($stats, $statsMethods)) {
            throw new \InvalidArgumentException('Method: '. $stats .' is not registered as stats view');
        }
        $ret = $this->{$stats}();
        $statsTitle = 'module.stats.' . strtolower($stats) . '.title';

        $pageRenderer = $this->view->getModuleTemplate()->getPageRenderer();
        $pageRenderer->addJSInlineCode('supportchat-stats-data','
                const labels = [' . implode(', ', $ret['labels']) . '];
                const data = {
                  labels: labels,
                  datasets: [{
                    label: "' . $this->translate($statsTitle) . '",
                    backgroundColor: "rgb(237, 118, 2)",
                    borderColor: "rgb(237, 118, 2)",
                    data: [' . implode(', ', $ret['data']) . ']
                    }]  
                };
                const config = {
                    type: "' . $ret['chart'] . '",
                    data: data,
                    plugins: [],
                    options: {}
                };
        ', true, true);

        $this->view->assignMultiple([
            'data' => $ret['result'],
            'statsSelect' => $stats,
            'statsOptions' => $this->getStatsOptions($statsMethods)
        ]);
    }

    /**
     * Display chats per hours
     *
     * @access public
     * @return void
     */
    protected function chatsPerHour()
    {
    }

    /**
     * Display chats per month
     *
     * @access public
     * @return void
     */
    protected function chatsPerMonth()
    {
    }

    /**
     * Display chat per weekday
     *
     * @access public
     * @return void
     */
    protected function chatsPerWeekday()
    {
    }

    /**
     * Display content of a chat took place before
     *
     * @access protected
     * @return array
     */
    protected function chatsPerYear(): array
    {
        $chatsPerYear = $this->chatsRepository->countChatsPerYear();
        $data = $labels = [];
        foreach ($chatsPerYear as $chat) {
            $data[] = ($chat["cnt"]) ?: "0";
            $labels[] = ($chat["year"]) ?: "";
        }
        return [
            'chart' => 'line',
            'labels' => $labels,
            'data' => $data,
            'result' => $chatsPerYear
        ];

    }

    /**
     * Get named and translated options for select box
     *
     * @param array $options
     *
     * @return array
     * @access private
     */
    private function getStatsOptions(array $options): array
    {
        $o = [];
        foreach ($options as $opt) {
            $o[$opt] = $this->translate('module.stats.' . strtolower($opt) . '.title');
        }
        return $o;
    }

}