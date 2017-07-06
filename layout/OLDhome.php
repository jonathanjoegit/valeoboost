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
* The one column layout.
*
* @package   theme_clean
* @copyright 2013 Moodle, moodle.org
* @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

// Get the HTML for the settings bits.
//$html = theme_clean_get_html_for_settings($OUTPUT, $PAGE);

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

    <?php echo $OUTPUT->standard_top_of_body_html() ?>

    <div id="page" class="container-fluid ">

        <header id="page-header" class="clearfix">
            <div id="subheader">
                <div class="container">
                    <div class="row centered">
                        <div class="col-lg-8 col-lg-offset-2">
                            <img alt="EAD" src="<?php echo $OUTPUT->image_url('logo608px', 'theme')?>" height="90px">
                            <h1><?php echo get_string('sitetitle', 'theme_eadum')?></h1>
                               
                               <video playsinline autoplay muted loop poster="<?php echo $OUTPUT->image_url('screenshotvid_1400', 'theme')?>" id="bgvid">
                                   <source src="http://umotion.univ-lemans.fr/media/videos/jjupin/467/video_467_720.mp4" type="video/mp4">
                                   <source src="http://umotion.univ-lemans.fr/media/videos/jjupin/467/video_467_720.webm" type="video/webm">
                                       
                                  
                               </video>
                               
            
                               
                               <!--[if lt IE 9]>
                               <script>
                               	document.createElement('video');
                               </script>
                               <![endif]-->
                               
                              
                               
                               <div class="btn-login"><a href="/moodle/login/"><?php echo get_string('connect', 'theme_eadum');?></a></div>       
                               
                               <div class="btn-play centered">
                                   <a href="http://umotion.univ-lemans.fr/video/0339-luniversite-du-maine-a-distance/" target="_blank">
                                       <img alt="Lire la vidéo" src="<?php echo $OUTPUT->image_url('play_wh', 'theme')?>" height="120px" />
                                        
                                        <?php echo get_string('watchvideo', 'theme_eadum');?>
                                    </a>
                                </div>
                                
                        </div>
                    </div><!-- row -->
                </div>
                
            </div> 
            
            <div id="page-navbar" class="clearfix">
                <nav class="breadcrumb-nav"><span class="accesshide">Chemin de la page</span><ul class="breadcrumb"><li><a href="http://ead.univ-lemans.fr/moodle/">Accueil</a></li></ul></nav>
                <div class="breadcrumb-button"></div>
            </div>
            <div id="course-header">
            </div>
        </header>

        <div id="page-content" class="row-fluid ead-home-content">
        
            <section id="region-main" class="span12">
                
                <div class="row-fluid">
                    <div class="span10 offset1  cadre-home">
                    
                        <div class="row-fluid text_home">
                            
                            <div class="span10 offset1">
                                
                                <div class="contain_text">
                                    <img alt="Icone" src="<?php echo $OUTPUT->image_url('id-card_70', 'theme')?>" height="65px">
                                    <div class="p_home"> 
                                        <?php echo get_string('txthome1', 'theme_eadum')?>
                                    </div> 
                                </div>
                                <div class="contain_text">
                                    <img alt="Icone" src="<?php echo $OUTPUT->image_url('layers_70', 'theme')?>" height="60px">
                                    <div class="p_home"> 
                                        <?php echo get_string('txthome2', 'theme_eadum')?>
                                    </div> 
                                </div>
                                <div class="contain_text">
                                    <img alt="Icone" src="<?php echo $OUTPUT->image_url('info_70', 'theme')?>" height="60px">
                                    <div class="p_home"> 
                                        <?php echo get_string('txthome3', 'theme_eadum')?>
                                    </div> 
                                </div>
                                <div class="contain_text">
                                    <img alt="Icone" src="<?php echo $OUTPUT->image_url('laptop_70', 'theme')?>" height="60px">
                                    <div class="p_home"> 
                                        <?php echo get_string('txthome4', 'theme_eadum')?>
                                    </div> 
                                </div>
                                
                                
                            </div>
                            
                            
                        </div>
        
                    </div>
                </div>
            </section>
        
        
        
            <section class="novisible" >
                <?php echo $OUTPUT->main_content(); ?>
            </section>
        </div>
        <?php echo $OUTPUT->standard_end_of_body_html() ?>

    </div>

    <?php require_once(dirname(__FILE__) . '/includes/footer.php'); ?>


</body>
</html>
