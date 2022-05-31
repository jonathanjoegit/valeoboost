<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme config.
 *
 * @package    theme_valeoboost
 * @copyright  2020 Jonathan J. - Le Mans Université
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib.php');

// Modif jjupin.
$THEME->name = 'valeoboost';
$THEME->parents = ['boost'];
$THEME->scss = function($theme) {
    return theme_valeoboost_get_main_scss_content($theme);
};

$THEME->layouts = [
  // The site home page.
  'frontpage' => array(
      'file' => 'home.php',
      'regions' => array('side-pre'),
      'defaultregion' => 'side-pre',
      'options' => array('nonavbar' => true),
    ),
];
// END modif jjupin.



$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->editor_scss = ['editor'];
$THEME->usefallback = true;

$THEME->enable_dock = false;
$THEME->csstreepostprocessor = 'theme_boost_css_tree_post_processor';
$THEME->extrascsscallback = 'theme_boost_get_extra_scss';
$THEME->prescsscallback = 'theme_boost_get_pre_scss';
$THEME->precompiledcsscallback = 'theme_boost_get_precompiled_css';
$THEME->yuicssmodules = array();
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->requiredblocks = '';
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
$THEME->iconsystem = \core\output\icon_system::FONTAWESOME;
