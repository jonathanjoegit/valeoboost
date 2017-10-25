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

namespace theme_eadumboost\output;

use coding_exception;
use html_writer;
use tabobject;
use tabtree;
use custom_menu_item;
use custom_menu;
use block_contents;
use navigation_node;
use action_link;
use stdClass;
use moodle_url;
use preferences_groups;
use action_menu;
use help_icon;
use single_button;
use single_select;
use paging_bar;
use url_select;
use context_system;
use context_course;
use pix_icon;

defined('MOODLE_INTERNAL') || die;

/**
 * Theme EAD UM core renderers.
 *
 * @package    theme_eadumboost
 * @copyright  2017 Jonathan J.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class core_renderer extends \theme_boost\output\core_renderer {
    /*
    * Overriding the custom_menu function ensures the custom menu is
    * always shown, even if no menu items are configured in the global
    * theme settings page.
    */
    public function custom_menu($custommenuitems = '')     {
        global $CFG, $PAGE;

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }
        $custommenu = new custom_menu($custommenuitems, current_language());

        // Custom :
        // si on est sur une page du site (connecté).
        if ($PAGE->pagelayout != 'frontpage' && $PAGE->pagelayout != 'login') {
            // TDB + listes des cours.
            $branchtitle = $branchlabel = get_string('myhome');
            $branchurl = new moodle_url('');
            $branchsort = 70000;

            $branch = $custommenu->add($branchlabel, $branchurl, $branchtitle, $branchsort);

            $hometext = get_string('myhome');
            $homelabel = html_writer::tag('i', '', array('class' => 'fa fa-home')).html_writer::tag('span', ' '.$hometext);
            $branch->add($homelabel, new moodle_url('/my/index.php'), $hometext);

            // Get 'My courses' sort preference from admin config.
            if (!$sortorder = $CFG->navsortmycoursessort) {
                $sortorder = 'sortorder';
            }

            // Retrieve courses and add them to the menu when they are visible.
            $numcourses = 0;
            if ($courses = enrol_get_my_courses(null, $sortorder . ' ASC')) {
                foreach ($courses as $course) {
                    if ($course->visible) {
                        $branch->add(
                '<span class="fa fa-graduation-cap"></span>'.format_string($course->fullname),
              new moodle_url('/course/view.php?id=' . $course->id),
                format_string($course->shortname)
            );
                        $numcourses += 1;
                    } elseif (has_capability('moodle/course:viewhiddencourses', context_course::instance($course->id))) {
                        $branchtitle = format_string($course->shortname);
                        $branchlabel = '<span class="dimmed_text">'.format_string($course->fullname) . '</span>';
                        $branchurl = new moodle_url('/course/view.php', array('id' => $course->id));
                        $branch->add($branchlabel, $branchurl, $branchtitle);
                        $numcourses += 1;
                    }
                }
            }
            if ($numcourses == 0 || empty($courses)) {
                $noenrolments = get_string('noenrolments', 'theme_eadumboost');
                $branch->add('<em>' . $noenrolments . '</em>', new moodle_url(''), $noenrolments);
            }

            // Si admin ou manager : afficher liste des cours.
            // Si l'utilisateur à accès à tous les cours et vois les cours cachés.
            if (has_capability('moodle/course:view', context_course::instance($course->id))
      && has_capability('moodle/course:viewhiddencourses', context_course::instance($course->id))) {
                $branchtitle = $branchlabel = "Tous les cours";
                $branchurl = new moodle_url('/course/index.php');
                $branchsort = 60000;
                $custommenu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
            }

            // Mail.
            $branchtitle = $branchlabel = get_string('mail', 'theme_eadumboost');
            $branchurl = new moodle_url('http://webmail.univ-lemans.fr/');
            $branchsort = 50000;
            $custommenu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
        }
        // Aide.
        $branchtitle = $branchlabel = get_string('support', 'theme_eadumboost');
        $branchurl = new moodle_url('');
        $branchsort = 40000;
        $branch = $custommenu->add($branchlabel, $branchurl, $branchtitle, $branchsort);

        // Sub branches.
        $sbranchtitle = $sbranchlabel = get_string('assistanceEtu', 'theme_eadumboost');
        $sbranchurl = new moodle_url('/um_apps/faq/faq-connexion.html');
        $branch->add($sbranchlabel, $sbranchurl, $sbranchtitle);

        $sbranchtitle = $sbranchlabel = get_string('serviceUni', 'theme_eadumboost');
        $sbranchurl = new moodle_url('/course/view.php?id=591&section=6');
        $branch->add($sbranchlabel, $sbranchurl, $sbranchtitle);

        $sbranchtitle = $sbranchlabel = get_string('methodologie', 'theme_eadumboost');
        $sbranchurl = new moodle_url('/course/view.php?id=591&section=2');
        $branch->add($sbranchlabel, $sbranchurl, $sbranchtitle);

        return parent::render_custom_menu($custommenu);
    }
}
