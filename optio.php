<?php

/**

 * Plugin Name: Optio Integration Tools

 * Plugin URI: http://richardconsulting.ro/blog/2012/05/optio-integration-tools-for-wordpress/

 * Description: Use Optio Publishing videos in your WP powered dentistry website

 * Author: Richard Vencu

 * Author URI: http://richardconsulting.ro

 * Version: 0.5

 * License: GPLv2

 *

 *  Copyright 2011  Richard Vencu  (email : richard.vencu@richardconsulting.ro)



    This program is free software; you can redistribute it and/or modify

    it under the terms of the GNU General Public License as published by

    the Free Software Foundation; either version 2 of the License, or

    (at your option) any later version.



    This program is distributed in the hope that it will be useful,

    but WITHOUT ANY WARRANTY; without even the implied warranty of

    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

    GNU General Public License for more details.



    You should have received a copy of the GNU General Public License

    along with this program; if not, write to the Free Software

    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

	

*/





register_activation_hook ( __FILE__ , 'optio_install' );



register_deactivation_hook ( __FILE__ , 'optio_deactivation' );



//require_once('dummy_db_translations.php');



require_once('optio_post_metabox.php');


require_once('optio_widget.php');


require_once('optio_shortcode.php');


add_action( 'plugins_loaded', 'optio_setup' );



function optio_install() {



	/* Declare default values */

	$optio_options = array(

	

		'useshortcode' => 1,

		

		'frontpage' => 0,

		

		'search' => 0,

		

		'archive' => 0,

		

		'author' => 0,



		'category' => 0,



		'tag' => 0,

		

		'loggedinonly' => 1



	);

	

	/* At first activation push values to database */

	if ( is_multisite() ) {



		global $optio_all_blogs;

	

		optio_retrieve_blogs();

	

		foreach ($optio_all_blogs as $blog) {

			if ( !get_blog_option($blog , 'optio_options') )

				update_blog_option ($blog , 'optio_options' , $optio_options);

		}

	} else {

		if ( !get_option('optio_options') )

			update_option ('optio_options',$optio_options);

	}

}



function optio_deactivation() {



	/* Delete options */

	

	

	

}



/*

*

*

*/

function optio_setup() {



	/* Load translation */

	load_plugin_textdomain ('optio', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );



	/* Add filters, actions, and theme-supported features. */

	/* Add shortcodes. */

	add_shortcode ('optiocatalog','optio_catalog');

	add_shortcode ('optio','optio_shortcode');
	
	add_shortcode ('optiolist','optio_shortcode_list');



	/* Add custom actions. */

	add_action ('admin_init','optio_load_scripts');

	add_action ('admin_init','optio_admin_init');

	add_action ('admin_menu','optio_admin_page');

	

	add_action ('init','optio_load_scripts');

	add_action ('init','optio_widget_init');

	

	add_action ('loop_start','optio_displayon');

	

	add_action ('wpmu_new_blog','optio_init_newblog');

	add_action ('edit_post','optio_quickedit_save', 5, 2);

	add_action ('bulk_edit_custom_box','optio_quickedit_bulk', 10, 2);

	add_action ('manage_posts_custom_column','optio_cpt_custom_column', 10, 2);

	add_action ('manage_pages_custom_column','optio_cpt_custom_column', 10, 2);

	add_action ('quick_edit_custom_box','optio_quickedit_show', 10, 2);

	add_action ('admin_head-edit.php','optio_quickedit_get');

	

	/* Add custom filters. */

	add_filter ('manage_posts_columns','optio_cpt_columns');

	add_filter ('manage_pages_columns','optio_cpt_columns');



}



/*

*

*

*/

function optio_init_newblog() {

	global $blog_id;

	

	/* Declare default values */

	$optio_options = array(

	

		'useshortcode' => 1,

		

		'frontpage' => 0,

		

		'search' => 0,

		

		'archive' => 0,

		

		'author' => 0,



		'category' => 0,



		'tag' => 0,

		

		'loggedinonly' => 1

		

	);

	

	update_blog_option ($blog_id , 'optio_options' , $optio_options);



}



/*

*

*

*/

function optio_load_scripts() {



	//load necessary css and scripts at the proper time both in frontend and backend



	wp_register_style( 'optio-style', plugins_url('css/optio.css', __FILE__) );

    wp_enqueue_style( 'optio-style' );



	wp_register_style( 'nyro-style', plugins_url('css/nyroModal.css', __FILE__) );

    wp_enqueue_style( 'nyro-style' );



	wp_register_script( 'nyro', plugin_dir_url( __FILE__ ) . 'js/jquery.nyroModal.custom.min.js' , array('jquery'), '');

	wp_enqueue_script( 'nyro' );

	

/* 	wp_register_script( 'sweetpages', plugin_dir_url( __FILE__ ) . 'js/jquery.sweetpages.js' , array('jquery'), '');

	wp_enqueue_script( 'sweetpages' ); */



}





/*

* This function loads Optio scripts only in the frontend

* Used by optio_displayon() function

*/

function optio_load_header() {



	//only load this script in pages where the integration is used

	if( !$_SERVER['HTTPS'] )

		wp_register_script( 'optio', 'http://www.optiopublishing.com/api/js' , array(), '');

	else

		wp_register_script( 'optio', 'https://www.optiopublishing.com/api/js' , array(), '');

	

	//do not use in SSL pages since there is no SSL available 

	if( !is_admin() ) {

		wp_enqueue_script( 'optio' );

	}

}





/*

* Controls the integration usage in contexts

*

*/

function optio_displayon() {



	global $blog_id;

	global $post;

	global $optiodisplay;

	

	$optiodisplay = false;



	$singular = 0;



	/* Read options */

	if (is_multisite())

		$optio_options = get_blog_option($blog_id , 'optio_options');

	else

		$optio_options = get_option('optio_options');

		

	if ( is_singular() ) {

		$singular = 1;

		if (get_post_meta($post->ID, 'optio_exclude', true))

			$singular = 0;

	}



	if ( ( $optio_options['loggedin'] == 1 && is_user_logged_in() ) || $optio_options['loggedin'] == 0) {

		if ( ( ($singular == 1 && !is_front_page()) || ($optio_options['frontpage'] == 1 && is_front_page()) ) ||

		( $optio_options['search'] == 1 && is_search() ) ||

		( $optio_options['archive'] == 1 && is_archive() ) ||

		( $optio_options['category'] == 1 && is_category() ) ||

		( $optio_options['author'] == 1 && is_author() ) ||

		( $optio_options['tag'] == 1 && is_tag() )

		) {

			add_action ('wp_footer','optio_load_header');

			$optiodisplay = true;

		}

	}	

}



/* Setup the admin options page */

function optio_admin_page() {



	add_options_page (



		__('Optio Integration Tools Settings Page','optio'),

		

		__('Optio Integration Tools','optio'),

		

		'manage_options',

		

		__FILE__,

		

		'optio_admin_settings_page'

	);

}



/*  Draw the option page */

function optio_admin_settings_page() {



	?>

	

	<div class="wrap">

	

		<?php screen_icon(); ?>

		

		<h2><?php _e('Optio Toolkit','optio'); ?></h2>

		

		<form action="options.php" method="post">

		

			<?php settings_fields('optio_options'); ?>

			

			<?php do_settings_sections('optio'); ?>

			

			<p><input class="button" name="Submit" type="submit" value="<?php _e('Save Changes','optio'); ?>" /></p>

			

		</form>

		

	</div>

	

	<?php

}



/* Register and define the settings */

function optio_admin_init(){



	register_setting(

		'optio_options',

		'optio_options',

		'optio_validate_options'

	);

	

	add_settings_section(

		'optio_main',

		__('Usage information','optio'),

		'optio_section_text',

		'optio'

	);

	

	add_settings_field(

		'optio_useshortcode',

		__('Use shortcode','optio'),

		'optio_setting_checkbox9',

		'optio',

		'optio_main'

	);

	

	add_settings_section(

		'optio_display',

		__('Where to use optio integration besides single pages?','optio'),

		'optio_section_text',

		'optio'

	);



	add_settings_field(

		'optio_frontpage',

		__('Display on frontpage','optio'),

		'optio_setting_checkbox2',

		'optio',

		'optio_display'

	);

	

	add_settings_field(

		'optio_search',

		__('Display on search page','optio'),

		'optio_setting_checkbox3',

		'optio',

		'optio_display'

	);

	

	add_settings_field(

		'optio_archive',

		__('Display on archive pages','optio'),

		'optio_setting_checkbox4',

		'optio',

		'optio_display'

	);

	

	add_settings_field(

		'optio_category',

		__('Display on category pages','optio'),

		'optio_setting_checkbox5',

		'optio',

		'optio_display'

	);

	

	add_settings_field(

		'optio_author',

		__('Display on author pages','optio'),

		'optio_setting_checkbox6',

		'optio',

		'optio_display'

	);

	

	add_settings_field(

		'optio_tag',

		__('Display on tag pages','optio'),

		'optio_setting_checkbox7',

		'optio',

		'optio_display'

	);

	

	add_settings_field(

		'optio_members',

		__('Display only if users are logged in?','optio'),

		'optio_setting_checkbox8',

		'optio',

		'optio_display'

	);

	

	add_settings_section(

		'optio_fb',

		__('Facebook page tab','optio'),

		'optio_section_facebook',

		'optio'

	);

	

}



/*  Draw the section header */

function optio_section_text() {

	echo '<p>' . __('Enter your settings below.','optio') . '</p>';

}



function optio_section_facebook() {

	echo '<p>' . __('If you want to create a dedicated tab for Optio videos in your Facebook page you need to:','optio') . '</p>';

	echo '<ol><li>' . __('Create an application via <a href="http://developers.facebook.com" target="_blank">http://developers.facebook.com</a> and define it as tab application using iframe integration. Make it 810 pixels wide.','optio') . '</li><li>' . __('Point the tab and the canvas to this url (make sure to use ? at the end):','optio') . ' <pre>' . plugins_url('facebook_tab/index.html?', __FILE__ ) . '</pre></li></ol>';

}



function optio_setting_checkbox2() {



	/* Get option 'frontpage' value from the database */

	global $blog_id;

	/* Read options */

	if (is_multisite())

		$optio_options = get_blog_option($blog_id , 'optio_options');

	else

		$optio_options = get_option('optio_options');

	

	$text_string = $optio_options['frontpage'];

	

	/* Echo the field */

	echo "<input id='frontpage' name='optio_options[frontpage]' type='checkbox' value='1' ";

	

	checked( 1 == $text_string );

	

	echo " />";

	echo "<p class='description'>".__("Please check if you want to display Optio integration in the frontpage.","optio")."</p>";

}



function optio_setting_checkbox3() {



	/* Get option 'search' value from the database */

	global $blog_id;

	/* Read options */

	if (is_multisite())

		$optio_options = get_blog_option($blog_id , 'optio_options');

	else

		$optio_options = get_option('optio_options');

	

	$text_string = $optio_options['search'];

	

	/* Echo the field */

	echo "<input id='search' name='optio_options[search]' type='checkbox' value='1' ";

	

	checked( 1 == $text_string );

	

	echo " />";

	echo "<p class='description'>".__("Please check if you want to display Optio integration in the search results page.","optio")."</p>";

}



function optio_setting_checkbox4() {



	/* Get option 'archive' value from the database */

	global $blog_id;

	/* Read options */

	if (is_multisite())

		$optio_options = get_blog_option($blog_id , 'optio_options');

	else

		$optio_options = get_option('optio_options');

	

	$text_string = $optio_options['archive'];

	

	/* Echo the field */

	echo "<input id='archive' name='optio_options[archive]' type='checkbox' value='1' ";

	

	checked( 1 == $text_string );

	

	echo " />";

	echo "<p class='description'>".__("Please check if you want to display Optio integration in the archive pages.","optio")."</p>";

}



function optio_setting_checkbox5() {



	/* Get option 'category' value from the database */

	global $blog_id;

	/* Read options */

	if (is_multisite())

		$optio_options = get_blog_option($blog_id , 'optio_options');

	else

		$optio_options = get_option('optio_options');

	

	$text_string = $optio_options['category'];

	

	/* Echo the field */

	echo "<input id='category' name='optio_options[category]' type='checkbox' value='1' ";

	

	checked( 1 == $text_string );

	

	echo " />";

	echo "<p class='description'>".__("Please check if you want to display Optio integration in the category pages.","optio")."</p>";

}



function optio_setting_checkbox6() {



	/* Get option 'author' value from the database */

	global $blog_id;

	/* Read options */

	if (is_multisite())

		$optio_options = get_blog_option($blog_id , 'optio_options');

	else

		$optio_options = get_option('optio_options');

	

	$text_string = $optio_options['author'];

	

	/* Echo the field */

	echo "<input id='author' name='optio_options[author]' type='checkbox' value='1' ";

	

	checked( 1 == $text_string );

	

	echo " />";

	echo "<p class='description'>".__("Please check if you want to display Optio integration in the author pages.","optio")."</p>";

}



function optio_setting_checkbox7() {



	/* Get option 'tag' value from the database */

	global $blog_id;

	/* Read options */

	if (is_multisite())

		$optio_options = get_blog_option($blog_id , 'optio_options');

	else

		$optio_options = get_option('optio_options');

	

	$text_string = $optio_options['tag'];

	

	/* Echo the field */

	echo "<input id='tag' name='optio_options[tag]' type='checkbox' value='1' ";

	

	checked( 1 == $text_string );

	

	echo " />";

	echo "<p class='description'>".__("Please check if you want to display Optio integration in the tag pages.","optio")."</p>";

}



function optio_setting_checkbox8() {



	/* Get option 'loggedin' value from the database */

	global $blog_id;

	/* Read options */

	if (is_multisite())

		$optio_options = get_blog_option($blog_id , 'optio_options');

	else

		$optio_options = get_option('optio_options');

	

	$text_string = $optio_options['loggedin'];

	

	/* Echo the field */

	echo "<input id='loggedin' name='optio_options[loggedin]' type='checkbox' value='1' ";

	

	checked( 1 == $text_string );

	

	echo " />";

	echo "<p class='description'>".__("Please check if you want to display Optio integration in the selected pages only for logged in users.","optio")."</p>";

}



function optio_setting_checkbox9() {



	/* Get option 'useshortcode' value from the database */

	global $blog_id;

	/* Read options */

	if (is_multisite())

		$optio_options = get_blog_option($blog_id , 'optio_options');

	else

		$optio_options = get_option('optio_options');

	

	$text_string = $optio_options['useshortcode'];

	

	/* Echo the field */

 	echo "<input id='useshortcode' name='optio_options[useshortcode]' type='checkbox' value='1' ";

	

	checked( 1 == $text_string );

	

	echo " />";

	echo "<p class='description'>".__("Please check if you want to activate shortcode (all types of integration)","optio")."</p>";

 

}



/* Validate user input */

function optio_validate_options( $input ) {



	$valid = array();

	

	$valid['frontpage'] = 0;

	

	if( isset( $input['frontpage'] ) && ( 1 == $input['frontpage'] ) )

	

        $valid['frontpage'] = 1;

		

	$valid['search'] = 0;

	

	if( isset( $input['search'] ) && ( 1 == $input['search'] ) )

	

        $valid['search'] = 1;

		

	$valid['archive'] = 0;

	

	if( isset( $input['archive'] ) && ( 1 == $input['archive'] ) )

	

        $valid['archive'] = 1;

		

	$valid['category'] = 0;

	

	if( isset( $input['category'] ) && ( 1 == $input['category'] ) )

	

        $valid['category'] = 1;

		

	$valid['tag'] = 0;

	

	if( isset( $input['tag'] ) && ( 1 == $input['tag'] ) )

	

        $valid['tag'] = 1;

		

	$valid['author'] = 0;

	

	if( isset( $input['author'] ) && ( 1 == $input['author'] ) )

	

        $valid['author'] = 1;

		

	$valid['loggedin'] = 0;

	

	if( isset( $input['loggedin'] ) && ( 1 == $input['loggedin'] ) )

	

        $valid['loggedin'] = 1;

		

	$valid['useshortcode'] = 0;

	

	if( isset( $input['useshortcode'] ) && ( 1 == $input['useshortcode'] ) )

	

        $valid['useshortcode'] = 1;

		

	return $valid;

}



function optio_retrieve_blogs() {

	/* Retrieve all blog ids */



	global $wpdb, $optio_all_blogs;



	$sql = "SELECT blog_id FROM $wpdb->blogs";



	$optio_all_blogs = $wpdb->get_col($wpdb->prepare($sql));

}





function optio_url() {

	$pageURL = 'http';

	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}

	$pageURL .= "://";

	if ($_SERVER["SERVER_PORT"] != "80") {

		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];

	} else {

		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

	}

	return $pageURL;

}



?>