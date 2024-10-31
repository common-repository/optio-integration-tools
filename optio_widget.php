<?php

function optio_display_thumbnails()

{

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



	if ($movies) {

	  //find associated videos

	  //$data = array(); //get_option('optio-publishing-videos');
	  
	  $data = get_option('optio-publishing-videos');

	  //$data['width']=372; //hardcoded for swift theme
	  //$data['margin']=10;
	  
	  $wdt = $data['width']-$data['margin'];

	  $half = ($wdt-2 * $data['margin'])/2;

	  $hgt = 0.7372549 * $wdt;

	  $halfh = 0.7372549 * $half;

	  

	  

	  if ( is_singular() && !is_front_page() ) {

		global $wp_query;

		$thePostID = $wp_query->post->ID;

		$ids = explode(',',get_post_meta($thePostID, 'optio_movies', true));

		usort($ids,'optio_cmp');

		

			if (count($ids)>0 && $ids[0] != "" ) {

		

				//echo "<h6>". __("Number of videos for this page:","optio") . " <strong>" . count($ids) . "</strong></h6><div class='clear'></div>";

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

				 

							if ($i < count($ids) || count($ids)%2 == 0 )

								echo do_shortcode("[caption id='optio_" . $i . "' align='alignleft' width='" . $half . "px']" . "<a class='optiolink' href='javascript:optio.openLightbox(\"$scope\");' title='$title'><img class='wp-image-" . $i . "' title='$title' src='" . plugin_dir_url( __FILE__ ) . "images/$thumbnail' alt='$alt' width='" . $half . "' height='" . $halfh . "' /></a> ".$description."[/caption]");

							else

								echo do_shortcode("[caption id='optio_" . $i . "' align='alignleft' width='" . $wdt . "px']" . "<a class='optiolink' href='javascript:optio.openLightbox(\"$scope\");' title='$title'><img class='wp-image-" . $i . "' title='$title' src='" . plugin_dir_url( __FILE__ ) . "images/$thumbnail' alt='$alt' width='" . $wdt . "' height='" . $hgt . "' /></a> ".$description."[/caption]");

							if ($i%2 == 0)

								echo "<div class='clear'></div>";

							break;

						}

					}

				}

				echo "<div class='clear'></div>";

			}

			else {

				//echo "<h6>". __("Video of the day","optio") . "</h6>";

					$curdate = getdate();

					$id = $curdate["yday"] % count($movies);

					foreach ($movies as $movie) {

						//display thumbnails with links to lightbox

						if ( $movie->id == $id ) {

							$description = $movie->description;

							$title= $movie->title;

							$alt= $movie->alt;

							$scope= $movie->identifier;

							$thumbnail= $movie->thumbnail;

				 

							echo do_shortcode("[caption id='optio_" . $id . "' align='aligncenter' width='" . $wdt . "px']" . "<a class='optiolink' href='javascript:optio.openLightbox(\"$scope\");' title='$title'><img class='wp-image-" . $id . "' title='$title' src='" . plugin_dir_url( __FILE__ ) . "images/$thumbnail' alt='$alt' width='" . $wdt . "' height='" . $hgt . "' /></a> ".$description."[/caption]");

							break;

						}

					}

			}

			

	  }

	  else {

			//echo "<h6>". __("Video of the day","optio") . "</h6>";

			$curdate = getdate();

			$id = $curdate["yday"] % count($movies);

			foreach ($movies as $movie) {

				if ( $movie->id == $id ) {

					$description = $movie->description;

					$title= $movie->title;

					$alt= $movie->alt;

					$scope= $movie->identifier;

					$thumbnail= $movie->thumbnail;

		 

					echo do_shortcode("[caption id='optio_" . $id . "' align='aligncenter' width='" . $wdt . "px']" . "<a class='optiolink' href='javascript:optio.openLightbox(\"$scope\");' title='$title'><img class='wp-image-" . $id . "' title='$title' src='" . plugin_dir_url( __FILE__ ) . "images/$thumbnail' alt='$alt' width='" . $wdt . "' height='" . $hgt . "' /></a> ".$description."[/caption]");

					break;

				}

			}

	  }



	}

}

 

function optio_widget($args,$params) {

  extract($args);

  echo $before_widget;

  echo $before_title . __("Related videos","optio") . $after_title;

  optio_display_thumbnails();

  echo $after_widget;

}

 

function optio_widget_init()

{

  wp_register_sidebar_widget('optio-publishing-videos',__('Optio Publishing Videos'), 'optio_widget');

  wp_register_widget_control('optio-publishing-videos', __('Optio Publishing Videos'), 'control');

}



function optio_cmp($a, $b)

{

    if ($a == $b) {

        return 0;

    }

    return ($a < $b) ? -1 : 1;

}



function control(){

  $data = get_option('optio-publishing-videos');

  ?>

  <p><label>Sidebar width</br><input name="optio-publishing-videos_sidebar_width"

type="text" value="<?php echo $data['width']; ?>" /> px</label></p>

<p><label>Margin</br><input name="optio-publishing-videos_sidebar_margin"

type="text" value="<?php echo $data['margin']; ?>" /> px</label></p>

  <?php

   if (isset($_POST['optio-publishing-videos_sidebar_width'])){

    $data['width'] = attribute_escape($_POST['optio-publishing-videos_sidebar_width']);

	$data['margin'] = attribute_escape($_POST['optio-publishing-videos_sidebar_margin']);

    update_option('optio-publishing-videos', $data);

  }

}



?>