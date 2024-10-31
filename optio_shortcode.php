<?php

/*

*	Shortcode definition for Optio integration

*

*/





/*

* the [optio] shortcode

* parameters:

* type = type of embedding, either library or single

* scope = scope of embedding, either a library name or video name

* width = width of library

* lightbox = display single video in lightbox or not (available only for single video)

* thumbnail = url of the thumbnail to display if lightbox is active (available only for single video)

* description = description for single video

* alt = alternate text for thumbnail image

* title = title for link to lightbox

*/



function optio_shortcode( $atts ) {

	extract( shortcode_atts( array(

		'type' => 'library',

		'scope' => 'all',

		'width' => '',

		'lightbox' => '',

		'thumbnail' => '',

		'title' => '',

		'alt' => '',

		'description' => ''

	), $atts ) );

	

	$id = str_replace("/","_",$scope);

	

	global $blog_id;

	

	/* Read options */

	if (is_multisite())

		$optio_options = get_blog_option($blog_id , 'optio_options');

	else

		$optio_options = get_option('optio_options');

		

	if ($optio_options['useshortcode'] != 1)

		return "";

		

	if( !$_SERVER['HTTPS'] )

		$base = "http://www.optiopublishing.com";

	else

		$base = "https://www.optiopublishing.com";

	

	//here display the result

	if ($type == 'library') {

		//integrate library

		if ($width == '')

			return "<script src='$base/embed/' type='text/javascript'></script><script type='text/javascript'>jQuery(document).ready(function () {var full_url = jQuery(location).attr('href'); var parts = full_url.split('#'); var target = parts[0] + '#' + '$scope'; window.location = target;	});</script>";

		else

			return "<script src='$base/embed/?width=$width' type='text/javascript'></script><script type='text/javascript'>jQuery(document).ready(function () { var full_url = jQuery(location).attr('href'); var parts = full_url.split('#'); var target = parts[0] + '#' + '$scope'; window.location = target;	});</script>";

	}

	elseif ($type == 'single') {

		//integrate single video

		if ($width == '') {

			if ($lightbox == '')

				return "<script type='text/javascript'  src='$base/embed/?control=video_player&video=$scope'></script>";

			else

				return '<dl id="optio_'. $id .'" class="wp-caption aligncenter"><dt><a href="javascript:optio.openLightbox(\'' . $scope . '\');" title="' . $title . '"><img class="wp-image" title="' . $title . '" src="' . plugin_dir_url( __FILE__ ) . 'images/' . $thumbnail .'" alt="' . $alt . '" width="175" height="131" /></a><dt><dd class="wp-caption-text">' . $description . '</dd></dl>';

		}

		else {

			if ($lightbox == '')

				return "<script type='text/javascript'  src='$base/embed/?control=video_player&video=$scope&width=$width'></script>";

			else

				return '<dl id="optio_'. $id .'" class="wp-caption aligncenter" style="width:' . $width . 'px;"><dt><a href="javascript:optio.openLightbox(\'' . $scope . '\');" title="' . $title . '"><img class="wp-image" title="' . $title . '" src="' . plugin_dir_url( __FILE__ ) . 'images/' . $thumbnail .'" alt="' . $alt . '" height="131" /></a><dt><dd class="wp-caption-text">' . $description . '</dd></dl>';

				

		}

	}

	else

		return "";

}



function optio_catalog($atts) {



	extract( shortcode_atts( array(

			'width' => 570

		), $atts ) );



	global $blog_id;

	

	/* Read options */

	if (is_multisite())

		$optio_options = get_blog_option($blog_id , 'optio_options');

	else

		$optio_options = get_option('optio_options');		

	

	if ($optio_options['useshortcode'] != 1)

		return "";

	

	$thumbwidth = floor(($width - 30) / 4);

	$gaps = $width - 4 * $thumbwidth;

	$gap = floor ($gaps / 3);

	$imgwidth = $thumbwidth - 4;

	$imgheight = floor((131 * $imgwidth) / 175);

	$overx = $imgwidth - 37;

	$overy = $imgheight - 30;

	$menuwidth = $width - 18;

	$modalwidth = $width + 40;

	

	if (file_exists(plugin_dir_path( __FILE__ ) . 'library/VideoIdentifiers.xml')) {

			$movies = simplexml_load_file(plugin_dir_path( __FILE__ ) . 'library/VideoIdentifiers.xml');

			$products = array();

			$categories = array();

			

			foreach ($movies as $movie) {

					$products[] = $movie->lib;

					$categories[] = $movie->cat;

			}

			$products[] = __('All','optio');

			$products = array_unique($products);

			$categories = array_unique($categories);

			

	$output = 	'<div id="optio-lib" style="display:none;width: 600px;padding-right:18px;">';

	$output .=	'<div id="optio-plugin-library" class="OptioVideoLibrary" style="opacity: 1; width: ' . $width . 'px;">';

	$output .=	'<button class="nyroModalClose button">' . __('Close Library','optio') . '</button>';

	$prd =		'<ul class="OptioProducts" style="width: ' . $menuwidth . 'px;">';

				foreach($products as $prod) { 

					$prd .= '<li class="';

					if ($prod == __('All','optio')) {$prd .= "OptioAll";} 

					$prd .= '">

					<a href="javascript:optio2626.navigateTo(\'dentistry\');" class="OptioActive">' . $prod .	'</a></li>';

				  }

				$prd .= '</ul>';

	$ctg =		'<ul class="OptioCategories" style="width: '. $menuwidth . 'px;">';



				foreach($categories as $cat) { 

					$ctg .= '<li style="width: 33%; ">

					<a href="javascript:optio2626.navigateTo(\'dentistry/decay\');">' . $cat . '</a></li>';

				}		  

				$ctg .= '</ul>';



	//$output .= $prd . $ctg; //attach here filtering menus

	

	$output .=	'<div class="OptioVideoList" style="padding-left: 0px; padding-right: 0px; ">';

				$i=0;

				foreach($movies as $mov) { 

					$i=$i+1;

			  

					if (!is_admin()) {

						$output .= '<a class="OptioVideo" title="' . $mov->description . '" href="javascript:optio.openLightbox(\'' . $mov->identifier . '\');" style="width: ' . $thumbwidth . 'px; height: 168px; margin-right: ';

						if ($i % 4 != 0) {$output .= $gap;} else {$output .= "0";} 

						$output .= 'px; ">

						<div class="OptioPlayButtonOverlay" style="left: ' . $overx . 'px; top: ' . $overy . 'px; "></div>';

					} 

					else { 

						global $post_id;

						$custom_fields = get_post_custom($post_id);

						$movieslist = $custom_fields['optio_movies'][0];

						$films = explode(',',$movieslist);



						if ( in_array($mov->id, $films) ) {$text = __( 'Remove this video', 'optio' ); } else {$text = __( 'Add this video', 'optio' );}

					   

						$output .= '<a style="width: ' . $thumbwidth . 'px; height: 168px; margin-right: ';

						if ($i % 4 != 0) {$output .= $gap;} else {$output .= "0";} 

						$output .= 'px; background-color: ';

						if ( in_array($mov->id, $films) ) {$output .= "greenyellow";} else {$output .= "bisque";}

						$output .= ';" >

						<button id="but'. $mov->id .'" class="button" onclick="javascript:var text = jQuery(\'#optio_movies_input\').val();

							if (text == \'' . __("Wait for values to load...","optio") . '\') {text = \'\';}

							var movies = text.split(\',\');

							if (this.innerHTML == \'' .  __( 'Add this video', 'optio' ) . '\') { 

								movies.push(\'' . $mov->id . '\');

								text = movies.join();

								this.innerHTML = \'' .  __( 'Remove this video', 'optio' ) . '\';

								jQuery(this).parent().css(\'background-color\',\'greenyellow\');

							}

							else {

								var mov = new Array();

								for(var i=0;i<movies.length;i++) {

								if (movies[i] != \'' . $mov->id . '\')

									mov.push(movies[i]);

								}

								text = mov.join();

								this.innerHTML = \'' . __( 'Add this video', 'optio' ) . '\';

								jQuery(this).parent().css(\'background-color\',\'bisque\');

							}

							if ( text.charAt(0) == \',\' ) { text = text.substr(1,text.lenght); }

							jQuery(\'#optio_movies_input\').val(text);

							jQuery(\'#optio_moviesbulk\').val(text);" >' . $text . '</button>';

					}

					$output .= '<img src="' . plugin_dir_url( __FILE__ ) . 'images/' . $mov->thumbnail . '" style="width: ' . $imgwidth . 'px; height: ' . $imgheight . 'px; ">

					<span class="OptioVideoCategory">' . $mov->cat . '</span>

					<span class="OptioVideoTitle">' . $mov->name . '</span>';

					$output .= '</a>';

				}

			 

			$output .= '</div>

		</div>

	</div>

	<a href="#optio-lib" class="nyroModal">Video library</a>

	<script type="text/javascript">

		jQuery(document).ready(function() {

			jQuery(\'a.nyroModal\').nyroModal({

				sizes: {

                    minW: '. $modalwidth .'

                }

			});		

		});

	</script>';

 } 

 return $output;
 
} 


function optio_shortcode_list( $atts ) {

	extract( shortcode_atts( array(

		'width' => '',

		'id_list' => ''

	), $atts ) );
	
	global $blog_id;
	

	/* Read options */

	if (is_multisite())

		$optio_options = get_blog_option($blog_id , 'optio_options');

	else

		$optio_options = get_option('optio_options');		
	
	if ($optio_options['useshortcode'] != 1)

	return "";

		

	if( !$_SERVER['HTTPS'] )

		$base = "http://www.optiopublishing.com";

	else

		$base = "https://www.optiopublishing.com";

	list($lang,$country) = split("_",WPLANG);
	

	if (class_exists('sitepress') && file_exists(plugin_dir_path( __FILE__ ) . 'library/VideoIdentifiers-' . ICL_LANGUAGE_CODE . '.xml')) {

		$movies = simplexml_load_file(plugin_dir_path( __FILE__ ) . 'library/VideoIdentifiers-' . ICL_LANGUAGE_CODE . '.xml');	

	}

	else {

	

		if (file_exists(plugin_dir_path( __FILE__ ) . 'library/VideoIdentifiers-' . $lang . '.xml'))

			$movies = simplexml_load_file(plugin_dir_path( __FILE__ ) . 'library/VideoIdentifiers-' . $lang . '.xml');	

		elseif (file_exists(plugin_dir_path( __FILE__ ) . 'library/VideoIdentifiers.xml'))

			$movies = simplexml_load_file(plugin_dir_path( __FILE__ ) . 'library/VideoIdentifiers-en.xml');

		else

			echo __("Error: The XML library files are missing in all languages.","optio");

	}

	$output="";
	
	if ($movies) {
	
		add_action ('wp_footer','optio_load_header');
	
		$wdt = $width;

		$half = ($wdt-3 * 3)/3;

		$hgt = 0.7372549 * $wdt;

		$halfh = 0.7372549 * $half;	

		$ids = explode(',',$id_list);

		usort($ids,'optio_cmp1');
	
	
		if (count($ids)>0 && $ids[0] != "" ) {

			$output = "<div style='margin:auto;width:$width"."px;'>";

			$i=0;

			foreach ($ids as $id) {

				$i += 1;

				foreach ($movies as $movie) {

					//display thumbnails with links to lightbox

					if ( $movie->id == $id ) {

						$description = $movie->description;

						$title= $movie->title;

						$alt= $movie->alt;

						$scope= $movie->identifier;

						$thumbnail= $movie->thumbnail;

			 

						//if ($i < count($ids) || count($ids)%2 == 0 )

							$output .= "<div id='optio_" . $i . "' style='text-align:center;width:" . $half . "px;float:left;margin-left:3px;' >" . "<a href='javascript:optio.openLightbox(\"$scope\");' title='$title'><img class='wp-image-" . $i . "' title='$title' src='" . plugin_dir_url( __FILE__ ) . "images/$thumbnail' alt='$alt' width='" . $half . "' height='" . $halfh . "' /></a><p class='wp-caption'>$description</p></div>";

						//else

							//$output .= "<div id='optio_" . $i . "' style='text-align:center;width:" . $wdt . "px;float:left;margin-left:3px;' >" . "<a href='javascript:optio.openLightbox(\"$scope\");' title='$title'><img class='wp-image-" . $i . "' title='$title' src='" . plugin_dir_url( __FILE__ ) . "images/$thumbnail' alt='$alt' width='" . $wdt . "' height='" . $hgt . "' /></a><p class='wp-caption'>$description</p></div>";

						if ($i%3 == 0)

							$output .= "<div class='clear'></div>";

						break;

					}

				}

			}

			$output .= "</div>";

		}
	
		return $output;
	}

}

function optio_cmp1($a, $b)

{

    if ($a == $b) {

        return 0;

    }

    return ($a < $b) ? -1 : 1;

}

?>