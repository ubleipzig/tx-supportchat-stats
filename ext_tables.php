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
            'Ubl.SupportchatStats',
            'system',
            'supportchatstats',
            'top',
            [
                'Chats' => 'display',
                'Stats' => 'overview'
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:supportchat-stats/Resources/Public/Icons/module-supportchat-stats.svg',
                'labels' => 'LLL:EXT:supportchat-stats/Resources/Private/Language/locallang_mod.xlf',
            ]
        );
    }

    /***************************************************************
     * TCA
     */

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_supportchatstats',
        'EXT:supportchat-stats/Resources/Private/Language/locallang.xlf'
    );

};

$boot();
unset($boot);