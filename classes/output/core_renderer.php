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

namespace theme_valeoboost\output;

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
use context_course;
use pix_icon;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_valeoboost
 * @copyright  2022 Jonathan J.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {


    /*
     * Overriding the custom_menu function ensures the custom menu is
     * always shown, even if no menu items are configured in the global
     * theme settings page.
     */
    public function umboost_custom_menu($custommenuitems = '') {
        global $CFG;

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }
        $custommenu = new custom_menu($custommenuitems, current_language());

        // Add dahsboard and my courses access.
        $this->umboost_get_dashboard_for_custom_menu($custommenu);

        // Add course list for manager and admin (if you have the good capability).
        if (has_capability('moodle/course:view', $this->page->context)
        && has_capability('moodle/course:viewhiddencourses', $this->page->context)) {
            $this->umboost_get_courselist_for_custom_menu($custommenu);
        }

        return $this->render_custom_menu($custommenu);
    }

    /**
     * OVERRIDE this render to not show the lang menu !
     */
    protected function render_custom_menu(custom_menu $menu) {

        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('core/custom_menu_item', $context);
        }
        return $content;
    }

    /**
     * Add dashboard and my courses access to custom menu (all users).
     */
    protected function umboost_get_dashboard_for_custom_menu(custom_menu $menu) {
        global $CFG;

        $mycourses = $this->page->navigation->get('mycourses');

        if (isloggedin() && $mycourses && $mycourses->has_children()) {
            $branchtitle = "dashboard"; // Title that we can use with CSS.
            $branchlabel = get_string('myhome');
            $branchurl   = new moodle_url('/course/index.php');
            $branchsort  = 1;

            $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);

            foreach ($mycourses->children as $coursenode) {
                $branch->add($coursenode->get_content(), $coursenode->action, $coursenode->get_title());
            }
        }
    }
    

    /**
     * add course list to custom menu (for admin).
     */
    protected function umboost_get_courselist_for_custom_menu( $custommenu) {
        // Fetch courses.
        $branchtitle = "courselist"; // Title that we can use with CSS.
        $branchlabel = get_string('courselist', 'theme_valeoboost');
        $branchurl = new moodle_url('/course/index.php');
        $branchsort = 2;

        $custommenu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
    }




    /**
     * Overriding: remove current langague (useless in footer and ugly).
     * -
     * We want to show the custom menus as a list of links in the footer on small screens.
     * Just return the menu object exported so we can render it differently.
     */
    public function custom_menu_flat() {
        global $CFG;
        $custommenuitems = '';

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }
        $custommenu = new custom_menu($custommenuitems, current_language());
        $langs = get_string_manager()->get_list_of_translations();
        $haslangmenu = $this->lang_menu() != '';

        if ($haslangmenu) {
            $strlang = get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            $this->language = $custommenu; /* ADD JJUPIN: remove current langague (useless in footer and ugly). */
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }

        return $custommenu->export_for_template($this);
    }


    /* -- -- -- COURSE CUMSTOMISATION :  -- -- -- */

    /** Overriding! (check moodle 3.8 ok)
     * Wrapper for header elements => QUEST CE QUON FAIT ?
     *
     * @todo: CLEAN OU Documenter la fonction + utiliser son parent.
     * @return string HTML to display the main header.
     */
    /* public function full_header() {
        global $PAGE;

        if ($PAGE->include_region_main_settings_in_header_actions() && !$PAGE->blocks->is_block_present('settings')) {
            // Only include the region main settings if the page has requested it and it doesn't already have
            // the settings block on it. The region main settings are included in the settings block and
            // duplicating the content causes behat failures.
            $PAGE->add_header_action(html_writer::div(
                $this->region_main_settings_menu(),
                'd-print-none',
                ['id' => 'region-main-settings-menu']
            ));
        }

        $header = new stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($PAGE->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        $header->headeractions = $PAGE->get_header_actions();

        // ADD JJUPIN: add "edit mode" in course. 
        //$header->editbutton = $this->umboost_edit_button();
        // ADD JJUPIN: valeoboost template ATTENTION TEMPLATE SUPP DE LA NOUVELLE VERSION !!! 
        return $this->render_from_template('theme_valeoboost/full_header', $header);
    }
	*/

    /**
     * Add editing button in a course
     *
     * @return string the editing button
     */
   /* public function umboost_edit_button() {
        global $PAGE, $COURSE;

        if (!$PAGE->user_allowed_editing() || $COURSE->id <= 1) {
            return '';
        }
        if ($PAGE->pagelayout == 'course') {
            $url = new moodle_url($PAGE->url);
            $url->param('sesskey', sesskey());
            if ($PAGE->user_is_editing()) {
                $url->param('edit', 'off');
                $btn = 'btn-danger editingbutton';
                $title = get_string('turneditingoff', 'core');
                $icon = 'fa-power-off';
            } else {
                $url->param('edit', 'on');
                $btn = 'btn-success editingbutton';
                $title = get_string('turneditingon', 'core');
                $icon = 'fa-edit';
            }
            return html_writer::tag('a', html_writer::start_tag('i', array(
                'class' => $icon . ' fa fa-fw'
            )) . html_writer::end_tag('i') . $title , array(
                'href' => $url,
                'class' => 'btn edit-btn ' . $btn,
                'data-tooltip' => "tooltip",
                'data-placement' => "bottom",
                'title' => $title,
            ));
        }
    }*/


    /**
     * OVERRIDE (check moodle 3.8 OK).
     * Add jjupin: searchcourses to custom menu (copy of build_action_menu_from_navigation).
     * @todo: use the parent function if possible.
     * Take a node in the nav tree and make an action menu out of it.
     * The links are injected in the action menu.
     *
     * @param action_menu $menu
     * @param navigation_node $node
     * @param boolean $indent
     * @param boolean $onlytopleafnodes
     * @return boolean nodesskipped - True if nodes were skipped in building the menu
     */
    protected function  build_action_menu_from_navigation(action_menu $menu,
    navigation_node $node,
    $indent = false,
    $onlytopleafnodes = false) {
        $skipped = false;

        // Build an action menu based on the visible nodes from this navigation tree.
        foreach ($node->children as $menuitem) {

            // ADDJJUPIN: No displaying "outcomes / fr:objectifs".
            if ($menuitem->key == "outcomes") {
                continue;
            }

            if ($menuitem->display) {
                if ($onlytopleafnodes && $menuitem->children->count()) {
                    $skipped = true;
                    continue;
                }
                if ($menuitem->action) {
                    if ($menuitem->action instanceof action_link) {
                        $link = $menuitem->action;
                        // Give preference to setting icon over action icon.
                        if (!empty($menuitem->icon)) {
                            $link->icon = $menuitem->icon;
                        }
                    } else {
                        $link = new action_link($menuitem->action, $menuitem->text, null, null, $menuitem->icon);
                    }
                } else {
                    if ($onlytopleafnodes) {
                        $skipped = true;
                        continue;
                    }
                    $link = new action_link(new moodle_url('#'), $menuitem->text, null, ['disabled' => true], $menuitem->icon);
                }
                if ($indent) {
                    $link->add_class('ml-4');
                }
                if (!empty($menuitem->classes)) {
                    $link->add_class(implode(" ", $menuitem->classes));
                }

                $menu->add_secondary_action($link);
                $skipped = $skipped || $this->build_action_menu_from_navigation($menu, $menuitem, true);
            }

            // ADD JJUPIN: We display the custom menu after "turn editing" / add jjupin.
            if ($menuitem->key == "turneditingonoff" ) {
                $this->umboost_get_custom_action_menu_for_course_header($menu);
            }
        }
        return $skipped;
    }

    /**
     * Add custom items to the course settings menu.
     * - participation
     * - enrolmentmethods
     * - questionbank
     */
    protected function umboost_get_custom_action_menu_for_course_header( $menu) {

        // Participants (if the user has the good capacity).
        if (has_capability('report/participation:view',  $this->page->context)) {
            $text = get_string('participants', 'core');
            $url = new moodle_url('/user/index.php', array('id' => $this->page->course->id));
            $customactionmenu = new action_link($url, $text, null, null, new pix_icon('t/cohort', ''));
            $customactionmenu->prioritise = true;
            $menu->add_secondary_action($customactionmenu);
        }
        // Méthode d'inscription.
        if (has_capability('moodle/course:enrolreview',  $this->page->context)) {
            $text = get_string('enrolmentmethods', 'core');
            $url = new moodle_url('/enrol/instances.php', array('id' => $this->page->course->id));
            $customactionmenu = new action_link($url, $text, null, null, new pix_icon('t/enrolusers', ''));
            $menu->add_secondary_action($customactionmenu);
        }
        // Banque de qestion.
        if (has_capability('moodle/question:add',  $this->page->context)) {
            $text = get_string('questionbank', 'question');
            $url = new moodle_url('/question/edit.php', array('courseid' => $this->page->course->id));
            $customactionmenu = new action_link($url, $text, null, null, new pix_icon('t/edit', ''));
            $menu->add_secondary_action($customactionmenu);
        }

    }


    /* -- -- -- LOGIN FORM CUSTOMISATION :  -- -- -- */

    /**
     * Renders the login form (to have the "CAS" or "NOCAS" value)
     *
     * @param \core_auth\output\login $form The renderable.
     * @return string
     */
    public function render_login(\core_auth\output\login $form) {

        global $CFG, $SITE;

        $context = $form->export_for_template($this);

        // Override because rendering is not supported in template yet.
        if ($CFG->rememberusername == 0) {
            $context->cookieshelpiconformatted = $this->help_icon('cookiesenabledonlysession');
        } else {
            $context->cookieshelpiconformatted = $this->help_icon('cookiesenabled');
        }
        $context->errorformatted = $this->error_text($context->error);
        $url = $this->get_logo_url();
        if ($url) {
            $url = $url->out(false);
        }
        $context->logourl = $url;
        $context->sitename = format_string(
            $SITE->fullname,
            true,
            ['context' => context_course::instance(SITEID), "escape" => false]
        );

        /* Add informaiton about the CAS (from GET) CAS or NOCAS. */
        /* If we are in /login/ => we want CAS*/
        $cas = true;
        // If "NOCAS" => we want only manual login.
        if (isset($_GET['authCAS']) and $_GET['authCAS'] == 'NOCAS') {
            $cas = false;
        }
        $context->cas = $cas;

        // Create URL: CAS / NOCAS / Angers.
        $linkcas = new moodle_url('/login/index.php',
        array('authCAS' => "CAS"));
        $context->linkcas = $linkcas;

        $linnocas = new moodle_url('/login/index.php',
        array('authCAS' => "NOCAS"));
        $context->linknocas = $linnocas;

        // ISSUE WITH HTTPS: @todo, CHECK ALL THIS LATER !
        // We force https (so no: new moodle_url('/auth/shibboleth/index.php').
        $linkangers = new moodle_url('https://ead.univ-lemans.fr/moodle/auth/shibboleth/index.php');
        $context->linkangers = $linkangers;

        return $this->render_from_template('theme_valeoboost/loginform', $context);
    }


}
