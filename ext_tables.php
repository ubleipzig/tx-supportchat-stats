<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$boot = function () {

    if (TYPO3_MODE === 'BE') {
        /* ===========================================================================
            Register BE-Modules
        =========================================================================== */

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'Ubl.' . $_EXTKEY,
            'system',
            'tx_supportchat_stats_m1',
            'top',
            [],
            [
                'access' => 'user,group',
                'icon' => 'EXT:supportchat-stats/Resources/Public/Icons/module-supportchat-stats.svg',
                'labels' => 'LLL:EXT:supportchat-stats/Resources/Private/Language/locallang_mod.xlf',
            ]
        );
    }
};

$boot();
unset($boot);