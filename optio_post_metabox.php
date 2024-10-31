<?php

class optio_post_metabox{

    function admin_init()
    {
		/* List all post types */
		$post_types=get_post_types('','names'); 
		
		/* Eliminate certain post types from the list: mediapage, attachment, revision, nav_menu_item - (compatible up to WordPress version 3.2)  */
		foreach( $post_types as $key => $value ) {
	
			if( $value == 'mediapage' || $value == 'attachment' || $value == 'revision' || $value == 'nav_menu_item' ) {
		
				unset( $post_types[$key] );
		
			}
		}
	
		/*  */
		$screens = apply_filters('optio_post_metabox_screens', $post_types );
        
		foreach($screens as $screen)
        {
			add_meta_box('optio', 'Optio Related Videos', array($this, 'post_metabox'), $screen, 'side', 'default'  );
        }
			add_action('save_post', array($this, 'save_post') );
        
			add_filter('default_hidden_meta_boxes', array($this,  'default_hidden_meta_boxes' )  );
    }

    function default_hidden_meta_boxes($hidden)
    {
        $hidden[] = 'optio';
        
		return $hidden;
    }

    function post_metabox(){
        global $post_id;

        if ( is_null($post_id) )
        		$checked = '';
        else
        {
            $custom_fields = get_post_custom($post_id);
            $checked = ( isset ($custom_fields['optio_exclude'])   ) ? 'checked="checked"' : '' ;
			$movieslist = $custom_fields['optio_movies'][0];
        }

        wp_nonce_field('optio_postmetabox_nonce', 'optio_postmetabox_nonce');

        echo '<label for="optio_movies">';        		

		_e("Insert a comma separated list of videos attached to this post:", 'optio' );

		echo '</label> ';

		echo '<input id="optio_movies_input" name="optio_movies_input" value="' . $movieslist . '" /> ';

		$args = array('width'=>570);
		echo optio_catalog($args);

		echo '<br /><br /><label for="optio_show_option">';
        
		_e("Do not use Optio Lightbox on this page:", 'optio' );
        
		echo '</label> ';
        
		echo '<input type="checkbox" id="optio_show_option" name="optio_show_option" value="1" '.$checked.'>';

    }

    function save_post($post_id)
    {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return;

        if ( ! isset($_POST['optio_postmetabox_nonce'] ) ||  !wp_verify_nonce( $_POST['optio_postmetabox_nonce'], 'optio_postmetabox_nonce' ) ) 
            return;

        if ( ! isset($_POST['optio_show_option']) )
        {
            delete_post_meta($post_id, 'optio_exclude');
        }
        else
        {
            $custom_fields = get_post_custom($post_id);
            if (! isset ($custom_fields['optio_exclude'][0])  )
            {
                add_post_meta($post_id, 'optio_exclude', 'true');
            }
            else
            {
				delete_post_meta($post_id, 'optio_exclude');
				add_post_meta($post_id, 'optio_exclude', 'true');
                //update_post_meta($post_id, 'optio_exclude', 'true' , $custom_fields['optio_exclude'][0]  ); 
            }
        }
		
		if ( ! isset($_POST['optio_movies_input']) || $_POST['optio_movies_input'] == '' )
        {
            delete_post_meta($post_id, 'optio_movies');
        }
        else
        {
            $custom_fields = get_post_custom($post_id);
            if (! isset ($custom_fields['optio_movies'][0])  )
            {
                add_post_meta($post_id, 'optio_movies', $_POST['optio_movies_input']);
            }
            else
            {
				delete_post_meta($post_id, 'optio_movies');
				add_post_meta($post_id, 'optio_movies', $_POST['optio_movies_input']);
                //update_post_meta($post_id, 'optio_movies', $_POST['optio_movies'] , $custom_fields['optio_movies'][0]  ); 
            }
        }
    }
}

$optio_post_metabox = new optio_post_metabox;
add_action ('admin_init',array($optio_post_metabox, 'admin_init'));
 
function optio_cpt_columns($defaults) {
  $defaults['optio_movies'] = __('Optio Related Videos','optio');
  return $defaults;
}
 
function optio_cpt_custom_column($column_name, $post_id) {
  $taxonomy = $column_name;
  $post_type = get_post_type($post_id);
  $terms = get_the_terms($post_id, $taxonomy);
  $movieslist = get_post_meta($post_id, 'optio_movies', true);
  if ($column_name == 'optio_movies') {
	if( $movieslist ) echo '<em>' . $movieslist . '</em>';
	else echo '<em>' . __('No related videos linked yet.','optio') . '</em>';
  }
}
 
function optio_quickedit_show( $col, $type ) {
	if( $col !='optio_movies' ) return;
    ?>
	<fieldset class="inline-edit-col-left">
	<div class="inline-edit-col">
	<div class="inline-edit-group"><label style="font: italic 12px Georgia, serif;" for="optio_movies"><?php echo __("Optio Related Videos","optio"); ?></label>
	<span class="input-text-wrap">
	<input id="optio_movies_input" type="text" name="optio_movies_input" value="" size="5" style="background-color: bisque;" /> <?php echo optio_catalog(); ?>
	</span>
	<input type="hidden" name="is_quickedit" value="true" /></div>
	</div></fieldset> 
<?php 
}

function optio_quickedit_get() {
	?>
	<script type="text/javascript">
	  jQuery(document).ready(function() {
		jQuery("a.editinline").live("click", function() {
			jQuery("input#optio_movies_input").css('background-color','bisque');
			jQuery("input#optio_movies_input").val('<?php echo __("Wait for values to load...","optio"); ?>');
			jQuery('#but*').text('<?php echo __( 'Add this video', 'optio' ); ?>');
			jQuery('#but*').parent().css('background-color','bisque');
			var ilc_qe_id = inlineEditPost.getId(this);
			jQuery.post("<?php echo plugin_dir_url( __FILE__ ); ?>ajax_server.php", { ilc_post_id: ilc_qe_id, ilc_mode: "ajaxget" }, function(data){ 
				jQuery("input#optio_movies_input").val(data); 
				var assoc = data.split(',');
				for (var q=0;q<assoc.length;q++) {
					jQuery('#but' + assoc[q]).text('<?php echo __( 'Remove this video', 'optio' ); ?>');
					jQuery('#but' + assoc[q]).parent().css('background-color','greenyellow');
				}
				jQuery("input#optio_movies_input").css('background-color','greenyellow'); 
			});		
		  });
		var ilc_post_ids = [];
		var ilc_bulk_value = '';
		var ilc_post_ids_flat = '';
		
		jQuery(".ilc-updated").hide();
		
		jQuery('#doaction, #doaction2').click(function(e){
			var n = jQuery(this).attr('id').substr(2);
			if ( jQuery('select[name="'+n+'"]').val() == 'edit' ) {
				e.preventDefault();
				jQuery('tbody th.check-column input[type="checkbox"]:checked').each(function(i){
					ilc_post_ids.push(jQuery(this).val());						
				});
				
			} else if ( jQuery('form#posts-filter tr.inline-editor').length > 0 ) {
				t.revert();
			}
		});
		
		jQuery(".ilc-bulk-update").live("click", function() {
			ilc_bulk_value = jQuery('input[name="optio_moviesbulk"]').val();
			for (var ilc_i = 0; ilc_i < ilc_post_ids.length; ilc_i++) {
				ilc_post_ids_flat = ilc_post_ids_flat + " " + ilc_post_ids[ilc_i];
			}  
			jQuery.post("<?php echo plugin_dir_url( __FILE__ ); ?>ajax_server.php", { 
					ilc_ids : ilc_post_ids_flat,
					ilc_mode: "ajaxsave",
					ilc_val: ilc_bulk_value
				},
				function(data){
					jQuery(".ilc-updated").fadeIn(300).delay(800).fadeOut(300);
					jQuery("input#bulk_edit").click();
				}
			);
		});
	});
	</script>
	<?php 
}

function optio_quickedit_save($post_id, $post) {
  if (isset($_POST['is_quickedit']))
    update_post_meta($post_id, 'optio_movies', $_POST['optio_movies_input']);
}

function optio_quickedit_bulk( $col, $type ) {
  if( $col != 'optio_movies' ) return; ?></pre>
	<fieldset class="inline-edit-col-left">
	<div class="inline-edit-col">
	<div class="inline-edit-group"><label style="font: italic 12px Georgia, serif;" for="optio_moviesbulk"><?php echo __("Optio Related Videos","optio"); ?></label>
	<span class="input-text-wrap">
	<input id="optio_moviesbulk" type="text" name="optio_moviesbulk" value="" size="10" /> <?php echo optio_catalog(); ?>
	</span></div>
	</div>
	<p class="ilc-updated"><?php echo __("Optio Related Videos updated","optio"); ?></p>
	<br />
	<a class="ilc-bulk-update button-secondary" href="#"><?php echo __("Update related videos","optio"); ?></a></fieldset>
 
<?php }

?>