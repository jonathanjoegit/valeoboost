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
* Theme functions.
*
* @package    theme_eadumboost
* @copyright  2017 Jonathan JUPIN - Université du Maine
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

/* lib here !*/


/**
* Returns the main SCSS content.
*
* @param theme_config $theme The theme config object.
* @return string
*/
function theme_eadumboost_get_main_scss_content($theme) {
	global $CFG;

	$scss = '';
	$filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
	$fs = get_file_storage();

	$context = context_system::instance();
	if ($filename == 'default.scss') {
		// We still load the default preset files directly from the boost theme. No sense in duplicating them.
		$scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
	} else if ($filename == 'plain.scss') {
		// We still load the default preset files directly from the boost theme. No sense in duplicating them.
		$scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');

	} else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_eadumboost', 'preset', 0, '/', $filename))) {
		// This preset file was fetched from the file area for theme_eadumboost and not theme_boost (see the line above).
		$scss .= $presetfile->get_content();
	} else {
		// Safety fallback - maybe new installs etc.
		$scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
	}

	// Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
	$pre = file_get_contents($CFG->themedir . '/eadumboost/scss/pre.scss');
	// Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
	$post = file_get_contents($CFG->themedir . '/eadumboost/scss/post.scss');

	// Combine them together.
	return $pre . "\n" . $scss . "\n" . $post;
}




/**
* Modification du Nav-drawer de Moodle (appelé dans les layouts)
* 	 //doc NAVIGATION: https://docs.moodle.org/dev/Navigation_API#How_the_navigation_works
*/
function theme_eadumboost_custom_nav_drawer(global_navigation $navigation) {
	global $PAGE, $CFG, $COURSE, $USER;
	require_once ($CFG->libdir . '/completionlib.php');
	

	// enlever "Home"
	if ($homenode = $navigation->find('home', global_navigation::TYPE_ROOTNODE)) {
		$homenode->showinflatnavigation = false;
	}
	// enlever "Privat files"
	// fait en CSS (display:none;) sinon c'est un peu galère (à voir plus tard)
	
	// ajouter plugin "tuteur" :
	// Vérifier si l'user à le droit d'afficher le rapport Tuteur
	$context = $PAGE->context;
	if (has_capability('report/tuteur:view', $context)) {
		
		// s'il y a des activités 
		$completion = new completion_info ( $COURSE );
		$activities = $completion->get_activities ();
		if (count ( $activities )>0 ){
			// on récupère le noeud du cours (cours + section + ...)
			$coursenode = $PAGE->navigation->find($COURSE->id, navigation_node::TYPE_COURSE); 			
			// Si la navigation contient des items
			if($coursenode && $coursenode->has_children() ){
		
				// on créer un noeud et on utilise le add de la classe navigation_node_collection pour le ranger
				$url = new moodle_url($CFG->wwwroot.'/report/tuteur/index.php', array('course'=>$COURSE->id));
				$nodereport = navigation_node::create("Rapport Tuteur", $url, navigation_node::TYPE_SETTING, "rapporttuteur", "rapporttuteur" ); 
				/* signature fonc : public static function create($text, $action=null, $type=self::TYPE_CUSTOM, $shorttext=null, $key=null, pix_icon $icon=null) {*/
				// on check s'il y a le noeud "grades", si oui on le met en dessous (sinon à la fin)
				if ($PAGE->navigation->find("grades", navigation_node::TYPE_SETTING)){
					$node = $coursenode->children->add($nodereport, "grades");
				}else{ // sinon à la fin du noeud
					$node = $coursenode->children->add($nodereport);
				}
		
			}
		}
	}

} // end of function theme_eadumboost_custom_nav_drawer

