<?php
/**
 * ext_emconf.php
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

$EM_CONF[$_EXTKEY] = [
    'title' => 'Supportchat Stats',
    'description' => 'Supportchat statistics for Typo3 Supportchat extension',
    'category' => 'misc',
    'shy' => 0,
    'version' => '0.1.2',
    'dependencies' => '',
    'conflicts' => '',
    'priority' => '',
    'loadOrder' => '',
    'state' => 'beta',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearcacheonload' => 1,
    'lockType' => '',
    'author' => 'Leipzig University Library',
    'author_email' => 'info@ub.uni-leipzig.de',
    'author_company' => 'www.ub.uni-leipzig.de',
    'CGLcompliance' => '',
    'CGLcompliance_note' => '',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99',
            'supportchat' => '2.6.0',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' =>
    [
        'psr-4' =>
            [
                'Ubl\\SupportchatStats\\' => 'Classes',
            ]
    ],
    'autoload-dev' =>
    [
        'psr-4' =>
            [
                'Ubl\\SupportchatStats\\Tests' => 'Tests',
            ]
    ]
];

