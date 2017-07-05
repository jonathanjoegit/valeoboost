<?php
//require_once($CFG->dirroot . '/theme/boost/classes/output/core_renderer.php');

/**
 * Theme EAD UM core renderers.
 *
 * @package    theme_eadumboost
 * @copyright  2017 Jonathan JUPIN
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
use context_course;
use pix_icon;


defined('MOODLE_INTERNAL') || die;

class core_renderer extends \theme_boost\output\core_renderer {	
    /*
     * Overriding the custom_menu function ensures the custom menu is
     * always shown, even if no menu items are configured in the global
     * theme settings page.
     */
    public function custom_menu($custommenuitems = '') {
        global $CFG;

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }
        $custommenu = new custom_menu($custommenuitems, current_language());
		
		// Custom :
        // Mail
        $branchtitle = $branchlabel = get_string('mail', 'theme_eadumboost');
        $branchurl = new moodle_url('http://webmail.univ-lemans.fr/');
        $branchsort = 50000;
        $custommenu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
        
        // Aide :
        $branchtitle = $branchlabel = get_string('support', 'theme_eadumboost');
        $branchurl = new moodle_url('');
        $branchsort = 40000;
        $branch = $custommenu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
       
        // sous branches :
        $SSbranchtitle = $SSbranchlabel = get_string('assistanceEtu', 'theme_eadumboost');
        $SSbranchurl = new moodle_url('/um_apps/faq/faq-connexion.html');
        $branch->add($SSbranchlabel, $SSbranchurl, $SSbranchtitle);
        
        $SSbranchtitle = $SSbranchlabel = get_string('serviceUni', 'theme_eadumboost');
        $SSbranchurl = new moodle_url('/course/view.php?id=591&section=6');
        $branch->add($SSbranchlabel, $SSbranchurl, $SSbranchtitle);
        
        $SSbranchtitle = $SSbranchlabel = get_string('methodologie', 'theme_eadumboost');
        $SSbranchurl = new moodle_url('/course/view.php?id=591&section=2');
        $branch->add($SSbranchlabel, $SSbranchurl, $SSbranchtitle);
        
		
		
        return parent::render_custom_menu($custommenu);
    }
	
	
}