<?php
declare(strict_types=1);
/**
 * Class StatsController
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
     * @access private
     */
    private $getVars = null;

    /**
     * Set up the doc header properly here
     *
     * @param ViewInterface $view
     *
     * @access protected
     */
    protected function initializeView($view)
    {
        parent::initializeView($view);
        $pathToJsLibrary = GeneralUtility::getIndpEnv('TYPO3_SITE_URL')
            . ExtensionManagementUtility::siteRelPath('supportchat-stats') . 'Resources/Public/JavaScript/';
        $pageRenderer = $this->view->getModuleTemplate()->getPageRenderer();
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
     * @access public
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
            'periodParameter' => $this->getPeriodParameter(),
        ]);
    }

    /**
     * Display chats per hours
     *
     * @return array
     * @access protected
     */
    protected function chatsPerHour(): array
    {
        $periodParams = $this->createPeriodParameterForSearch();
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
     * @return array
     * @access protected
     */
    protected function chatsPerMonth(): array
    {
        $periodParams = $this->createPeriodParameterForSearch();
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
     * @return array
     * @access protected
     * @throws \Exception
     */
    protected function chatsPerWeekday(): array
    {
        $periodParams = $this->createPeriodParameterForSearch();
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
     * @return array
     * @access protected
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
     * @return array    Returns array with variables
     * @access private
     * @throws \Http\Exception\UnexpectedValueException
     */
    private function getPeriodParameter(): array
    {
        if ($this->getVars == null) {
            throw new UnexpectedValueException('GET/POST parameters have to be evaluated by extension.');
        }
        $startDate = ($this->getVars["constraint"]["dateStart"]) ?: null;
        $stopDate = null;
        // Set last point of period always to end of day.
        if ($this->getVars["constraint"]["dateStop"]) {
            $de = new \DateTime($this->getVars["constraint"]["dateStop"], new \DateTimeZone('UTC'));
            $stopDate = $de->format('Y-m-d') . 'T23:59:59Z';
        }
        return [
            'start' => $startDate,
            'end' => $stopDate
        ];
    }

    /**
     * Creates and validate period parameters for search request.
     *
     * @return array|false
     * @access private
     * @throws \Exception
     */
    private function createPeriodParameterForSearch()
    {
        $period = $this->getPeriodParameter();
        // Evaluate if select with where on timestamps should be taken.
        if (!$period['start'] && !$period['end']) {
            return false;
        } else if ($period['start'] && !$period['end']) {
            if ($this->isTypo3VersionCompatible("9.5.0")) {
                $context = GeneralUtility::makeInstance(Context::class);
                $timezone = $context->getPropertyFromAspect('date', 'timezone');
            } else {
                $timezone = ($GLOBALS['TYPO3_CONF_VARS']['SYS']['phpTimeZone']) ?: '';
            }
            $d = new \DateTime("now", new \DateTimeZone($timezone));
            $period['end'] = $d->format('Y-m-d\TH:i:s\Z');
        } else if (!$period['start'] && $period['end']) {
            $period['start'] = "1970-01-01T00:00:00Z";
        } else {
            // Validate if selected period is logical
            if (false === $this->validatePeriodParameter(
                new \DateTime($period['start']),
                new \DateTime($period['end'])
            )) {
                $this->addFlashMessageHelper(
                    'date.inverted',
                    \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR
                );
            }
        }
        return [
          'start' => $period['start'],
          'end' => $period['end']
        ];
    }

    /**
     * Converts datetime format to timestamp
     *
     * @param string $date
     *
     * @return int
     * @access private
     * @throws \Exception
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
     * @static
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

    /**
     * Validate parameter of selected search period
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     *
     * @return bool
     * @access private
     */
    private function validatePeriodParameter(\DateTime $startDate, \DateTime $endDate): bool
    {
        return $startDate < $endDate;
    }
}