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

use Http\Exception\UnexpectedValueException;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

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
     * Get variables
     *
     * @var array|null $getVars
     * @access protected
     */
    private $getVars = null;

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
        $this->getVars = GeneralUtility::_GP($this->extensionNamespace . '_' . strtolower(GeneralUtility::_GP('M')));
        $classNames = get_class_methods($this);
        foreach ($classNames as $name) {
            if (0 < preg_match('/^chats\w*$/', $name)) {
                $statsMethods[] = $name;
           }
        }
        $stats = $this->getVars['statsSelect']
            ? filter_var($this->getVars['statsSelect'], FILTER_SANITIZE_STRING)
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
                    options: { ' . ($ret['options'] ?: '')  . '}
                };
        ', true, true);

        $this->view->assignMultiple([
            'data' => $ret['result'],
            'statsSelect' => $stats,
            'statsOptions' => $this->getStatsOptions($statsMethods),
            'periodParameter' => $this->getSelectPeriodParameter(),
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
        $periodParams = $this->getSelectPeriodParameter();
        $chatsPerHour = (false !== $periodParams)
            ? $this->chatsRepository->countChatsPerHour(
                $this->getTimestamp($periodParams['start']),
                $this->getTimestamp($periodParams['end'])
            ) : $this->chatsRepository->countChatsPerHour();
        $data = $labels = [];
        foreach ($chatsPerHour as $chat) {
            $data[] = ($chat["cnt"]) ?: "0";
            $labels[] = $chat['hour'];
        }
        return [
            'chart' => 'bar',
            'labels' => $labels,
            'data' => $data,
            'result' => $chatsPerHour
        ];
    }

    /**
     * Display chats per month
     *
     * @access public
     * @return void
     */
    protected function chatsPerMonth()
    {
        $periodParams = $this->getSelectPeriodParameter();
        $chatsPerMonth = (false !== $periodParams)
            ? $this->chatsRepository->countChatsPerMonth(
                $this->getTimestamp($periodParams['start']),
                $this->getTimestamp($periodParams['end'])
            ) : $this->chatsRepository->countChatsPerMonth();
        $data = $labels = [];
        foreach ($chatsPerMonth as $chat) {
            $data[] = ($chat["cnt"]) ?: "0";
            $labels[] = '"' . $this->translate('module.stats.month' . $chat['month']) . '"' ;
        }
        return [
            'chart' => 'bar',
            'labels' => $labels,
            'data' => $data,
            'result' => $chatsPerMonth
        ];
    }

    /**
     * Display chat per weekday
     *
     * @access public
     * @return void
     */
    protected function chatsPerWeekday(): array
    {
        $periodParams = $this->getSelectPeriodParameter();
        $chatsPerWeekday = (false !== $periodParams)
            ? $this->chatsRepository->countChatsPerWeekday(
                $this->getTimestamp($periodParams['start']),
                $this->getTimestamp($periodParams['end'])
            ) : $this->chatsRepository->countChatsPerWeekday();
        $data = $labels = [];
        foreach ($chatsPerWeekday as $chat) {
            $data[] = ($chat["cnt"]) ?: "0";
            $labels[] = '"' . $this->translate('module.stats.weekday' . $chat["weekday"]) . '"' ;
        }
        return [
            'chart' => 'bar',
            'labels' => $labels,
            'data' => $data,
            'result' => $chatsPerWeekday
        ];
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

    /**
     * Get timestamps
     *
     * @return bool|array    Return timestamps, if no set false
     * @access private
     */
    private function getSelectPeriodParameter()
    {
        if ($this->getVars == null) {
            throw new UnexpectedValueException('GET/POST parameters have to be evaluated by extension.');
        }
        $startDate = ($this->getVars["constraint"]["dateStart"]) ?: null;
        $stopDate = ($this->getVars["constraint"]["dateStop"])
            ? date("Y-m-d", strtotime($this->getVars["constraint"]["dateStop"])) . "T23:59:59Z" : null;
        // Evaluate if select with where on timestamps should be taken.
        if (!$startDate && !$stopDate) {
            return false;
        } else if ($startDate && !$stopDate) {
            if ($this->isTypo3VersionCompatible("9.5.0")) {
                $context = GeneralUtility::makeInstance(Context::class);
                $timezone = $context->getPropertyFromAspect('date', 'timezone');
            } else {
                $timezone = ($GLOBALS['TYPO3_CONF_VARS']['SYS']['phpTimeZone']) ?: '';
            }
            $d = new \DateTime("now", new \DateTimeZone($timezone));
            $stopDate = $d->format('Y-m-d\TH:i:s\Z');
        } else if (!$startDate && $stopDate) {
            $startDate = "1970-01-01T00:00:00Z";
        }
        return [
          'start' => $startDate,
          'end' => $stopDate,
        ];
    }

    /**
     * Converts datetime format to timestamp
     *
     * @param string $date
     *
     * @return int
     * @access private
     */
    private function getTimestamp(string $date): int
    {
        $d = new \DateTime($date);
        return (int) $d->format("U");
    }

    /**
     * Check required version of typo3
     *
     * @param string $version A valid typo3 version string for evaluation
     *
     * @return bool
     * @access private
     */
    private static function isTypo3VersionCompatible($version): bool
    {
        if (false === preg_match('/^\d{1,3}\.\d{1,3}(\.\d{1,3})?$/', $version)) {
            throw \InvalidArgumentException('No correct format of typo3 version number');
        }
        return VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version)
            >= VersionNumberUtility::convertVersionNumberToInteger($version);
    }
}