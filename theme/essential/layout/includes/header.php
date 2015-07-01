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
 * This is built using the bootstrapbase template to allow for new theme's using
 * Moodle's new Bootstrap theme engine
 *
 * @package     theme_essential
 * @copyright   2013 Julian Ridden
 * @copyright   2014 Gareth J Barnard, David Bezemer
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

echo $OUTPUT->doctype();

require_once($OUTPUT->get_include_file('pagesettings'));
?>
<html <?php echo $OUTPUT->htmlattributes(); ?> class="no-js">
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>"/>
    <?php 
    echo $OUTPUT->get_csswww();
    echo $OUTPUT->standard_head_html();
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google web fonts -->
    <?php require_once($OUTPUT->get_include_file('fonts')); ?>
    <!-- iOS Homescreen Icons -->
    <?php require_once($OUTPUT->get_include_file('iosicons')); ?>
    <!-- Start Analytics -->
    <?php require_once($OUTPUT->get_include_file('analytics')); ?> 
    <!-- End Analytics -->
</head>

<body <?php echo $OUTPUT->body_attributes($bodyclasses); ?>>

<?php echo $OUTPUT->standard_top_of_body_html(); ?>

<header role="banner">

    <div id="page-header" class="clearfix<?php echo ($oldnavbar) ? ' oldnavbar' : ''; ?>">
       <!-- <div class="container-fluid">
            <div class="row-fluid">
          
                <div class="<?php echo $logoclass;
                echo (!$left) ? ' pull-right' : ' pull-left'; ?>">
                    <?php if (!$haslogo) { ?>
                        <a class="textlogo" href="<?php echo preg_replace("(https?:)", "", $CFG->wwwroot); ?>">
                            <i id="headerlogo" class="fa fa-<?php echo $OUTPUT->get_setting('siteicon'); ?>"></i>
                            <?php echo $OUTPUT->get_title('header'); ?>
                        </a>
                    <?php } else { ?>
                        <a class="logo" href="<?php echo preg_replace("(https?:)", "", $CFG->wwwroot); ?>" title="<?php print_string('home'); ?>"></a>
                    <?php } ?>
                </div>
                <?php if ($hassocialnetworks || $hasmobileapps) { ?>
                <a class="btn btn-icon" data-toggle="collapse" data-target=".icon-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                <div class="icon-collapse collapse pull-<?php echo ($left) ? 'right' : 'left'; ?>">
                    <?php
                    }
                    // If true, displays the heading and available social links; displays nothing if false.
                    if ($hassocialnetworks) {
                        ?>
                        <div class="pull-<?php echo ($left) ? 'right' : 'left'; ?>" id="socialnetworks">
                            <p id="socialheading"><?php echo get_string('socialnetworks', 'theme_essential') ?></p>
                            <ul class="socials unstyled">
                                <?php
                                echo $OUTPUT->render_social_network('googleplus');
                                echo $OUTPUT->render_social_network('twitter');
                                echo $OUTPUT->render_social_network('facebook');
                                echo $OUTPUT->render_social_network('linkedin');
                                echo $OUTPUT->render_social_network('youtube');
                                echo $OUTPUT->render_social_network('flickr');
                                echo $OUTPUT->render_social_network('pinterest');
                                echo $OUTPUT->render_social_network('instagram');
                                echo $OUTPUT->render_social_network('vk');
                                echo $OUTPUT->render_social_network('skype');
                                echo $OUTPUT->render_social_network('website');
                                ?>
                            </ul>
                        </div>
                    <?php
                    }
                    // If true, displays the heading and available social links; displays nothing if false.
                    if ($hasmobileapps) { ?>
                        <div class="pull-<?php echo ($left) ? 'right' : 'left'; ?>" id="mobileapps">
                            <p id="socialheading"><?php echo get_string('mobileappsheading', 'theme_essential') ?></p>
                            <ul class="socials unstyled">
                                <?php
                                echo $OUTPUT->render_social_network('ios');
                                echo $OUTPUT->render_social_network('android');
                                echo $OUTPUT->render_social_network('winphone');
                                echo $OUTPUT->render_social_network('windows');
                                ?>
                            </ul>
                        </div>
                    <?php
                    }
                    if ($hassocialnetworks || $hasmobileapps) {
                    ?>
                </div>
            <?php } ?>
            </div>
        </div>-->
    </div>
    <nav role="navigation">
        <div id='essentialnavbar' class="navbar<?php echo ($oldnavbar) ? ' oldnavbar' : ''; ?> moodle-has-zindex">
            <div class="container-fluid navbar-inner">
                <div class="row-fluid">
                    <div class="custommenus pull-<?php echo ($left) ? 'left' : 'right'; ?>">
                        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </a>
                        <?php //echo $OUTPUT->get_title('navbar'); ?>
						<logo><a title="The Leader In Me Online" href="/"><span>The Leader In Me</span> Online</a></logo>
                    <div class="pull-<?php echo ($left) ? 'right' : 'left'; ?>">
                        <div class="usermenu">
                            <?php echo $OUTPUT->custom_menu_user(); ?>
                        </div>
                        <div class="messagemenu">
                            <?php echo $OUTPUT->custom_menu_messages(); ?>
                        </div>
                        <div class="gotobottommenu">
                            <?php echo $OUTPUT->custom_menu_goto_bottom(); ?>
                        </div>
                    </div>
                        <div class="nav-collapse collapse pull-<?php echo ($left) ? 'left' : 'right'; ?>">
                            <div id="custom_menu_language">
                                <?php echo $OUTPUT->custom_menu_language(); ?>
                            </div>
                            <div id="custom_menu_courses">
                                <?php echo $OUTPUT->custom_menu_courses(); ?>
                            </div>
                            <?php if ($colourswitcher) { ?>
                                <div id="custom_menu_themecolours">
                                    <?php echo $OUTPUT->custom_menu_themecolours(); ?>
                                </div>
                            <?php } ?>
                            <div id="custom_menu">
                                <?php echo $OUTPUT->custom_menu(); ?>
                            </div>
                            <div id="custom_menu_activitystream">
                                <?php echo $OUTPUT->custom_menu_activitystream(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			
			
			
			
			
			
			
			
			
		<?php if(isset($USER->id) && $USER->id != 0 && is_numeric($USER->id)):?>			
		<div class="page-container">
        <nav>
            
            <div class="page-container clearfix">
            
                <!--TOP NAVIGATION-->
                <a class="mobile-menu"><?php echo ('MENU'); ?></a>
                
                <script>
                    $(function() {
                    
                        $('.mobile-menu').click(function() {
                            $(this).toggleClass('is-down');
                            $(this).siblings('ul').slideToggle('fast');
                        });
                    
                    });
                </script>
                
                <ul>
                <li class="home"><a href="/"><?php echo ('HOME'); ?></a></li>
                <li class="learn">
                    <a href="#" onClick="return false;"
                        <?php
                            if(stristr($_SERVER["REQUEST_URI"], "/learn")) { 
                                echo ' class="selected"';
                            }
                        ?>  
                    ><?php echo ('VIDEOS'); ?></a>
                    <div class="drop-menu">
                    	<div class="arrow"></div>
                    	<ul>
                    		<li><a class="menuicon icon-videos" href="/videos/video_library"><?php echo ('FranklinCovey Insights(TM) Video Library');?></a></li>
                    		<li><a class="menuicon icon-staff-videos" href="/videos/new_staff_videos"><?php echo ('New Staff Video Training');?></a></li>
                    	</ul>
                    </div>
                </li>
                <li class="teach">
                    <a href="#" onClick="return false;"
                        <?php
                            if(stristr($_SERVER["REQUEST_URI"], "/teach")) { 
                                echo ' class="selected"';
                            }
                        ?>              
                    ><?php echo ('RESOURCES'); ?></a>
                    <div class="drop-menu">
                    	<div class="arrow"></div>
                    	<ul class="header-sub-menu">
                    		<li>
                    		    <a class="menuicon icon-resources" href="/resources/teacher_edition"><?php echo ('Teacher Edition Resources');?></a>
                    		   
                    		</li>
                    		<li><a class="menuicon icon-plans" href="/resources/7_habits_lessons"><?php echo ('7 Habits Lesson Plans');?></a></li>
                    		<li><a class="menuicon icon-graph" href="/resources/leadership_tools"><?php echo ('Leadership & Quality Tools');?></a></li>
                    		<li><a class="menuicon icon-notebook" href="/resources/leadership_notebooks"><?php echo ('Leadership Notebooks');?></a></li>
                    		<li><a class="menuicon icon-calendar" href="/resources/quick_start"><?php echo ('Week by Week Quick Start Guide');?></a></li>
                    		<li><a class="menuicon icon-users-green" href="/resources/lighthouse_team_resources"><?php echo ('Lighthouse Team Resources');?></a></li                
                    		><li><a class="menuicon icon-xo" href="http://www.theleaderinme.org/students/"><?php echo ('Student Schoolyard Games');?></a></li>
                    		<li><a class="menuicon icon-notebook-update" href="/resources/newsletters"><?php echo ('Newsletters from Sean Covey');?></a></li>
                    		<li><a class="menuicon icon-users-green" href="/resources/family_resources"><?php echo ('7 Habits Resources for Families');?></a></li>
                    		<li><a class="menuicon icon-parent-night-green" href="/resources/parent_night"><?php echo ('Parent Night Resources');?></a></li>
                    		<li><a class="menuicon icon-notebook" href="/resources/fc_graphics"><?php echo ('FranklinCovey Graphics');?></a></li>
                    	</ul>
                    </div>                    
                </li>
                <li class="community">
                    <a href="/community/"
                        <?php
                            if(stristr($_SERVER["REQUEST_URI"], "/community/")) { 
                                echo ' class="selected"';
                            }
                        ?>
                    ><?php echo ('COMMUNITY'); ?></a>
                </li>
                <li class="lighthouse">
                	 <a href="/my-school/"
                    <?php
                            if(stristr($_SERVER["REQUEST_URI"], "my-school")) { 
                                echo ' class="selected"'; 
                            }
                        ?>  ><?php echo ('MY SCHOOL'); ?></a>
                </li>               
                 <li class="booster">
                    <a href="/boosters/"
                    <?php
                            if(stristr($_SERVER["REQUEST_URI"], "boosters")) { 
                                echo ' class="selected"'; 
                            }
                        ?>  ><?php echo ('BOOSTERS'); ?></a>
                </li>  
				<li class="lighthouse">
                    <a href="http://store.theleaderinme.org/" target="_blank"><?php echo ('STORE'); ?></a>
                </li>            
                </ul>        
                
            </div>
            <div class="update-status" style="display:none;"><?php echo ('Updating '); ?><img src="/img/processing-dark.gif" alt="" class="processing"></div>
        </nav>
        </div>
		<?php endif;?>
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
        </div>
    </nav>
</header>
