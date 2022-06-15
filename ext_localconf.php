<?php
defined('TYPO3_MODE') || die('Access denied.');

TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
    'module.tx_supportchatstats_system_supportchatstatssupportchatstats {
        view {
            templateRootPaths {
                0 = EXT:supportchat-stats/Resources/Private/Backend/Templates/
                1 = {$module.tx_supportchatstats.view.templateRootPath}
            }
            partialRootPaths {
                0 = EXT:supportchat-stats/Resources/Private/Backend/Partials/
                1 = {$module.tx_supportchatstats.view.partialRootPath}
            }
            layoutRootPaths {
                0 = EXT:supportchat-stats/Resources/Private/Backend/Layouts/
                1 = {$module.tx_supportchatstats.view.layoutRootPath}
            }
        }
        persistence < plugin.tx_supportchatstats.persistence
    }'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants(
    'module.tx_supportchatstats_system_supportchatstatssupportchatstats {
        view {
            # cat=module.tx_supportchatstats/file; type=string; label=Path to template root (BE)
            templateRootPath = EXT:supportchat-stats/Resources/Private/Backend/Templates/
            # cat=module.tx_supportchatstats/file; type=string; label=Path to template partials (BE)
            partialRootPath = EXT:supportchat-stats/Resources/Private/Backend/Partials/
            # cat=module.tx_supportchatstats/file; type=string; label=Path to template layouts (BE)
            layoutRootPath = EXT:supportchat-stats/Resources/Private/Backend/Layouts/
        }
        persistence {
            # cat=module.tx_supportchatstats//a; type=string; label=Default storage PID
            storagePid =
        }
    }'
);