<?php if(isset($_POST['ilc_mode'])){
    require_once('../../../wp-blog-header.php');
	header('HTTP/1.1 200 OK');
    if($_POST['ilc_mode'] == 'ajaxget'){
        $ilc_post_id = $_POST['ilc_post_id'];
        echo get_post_meta($ilc_post_id, 'optio_movies', true);
    }
    elseif($_POST['ilc_mode'] == 'ajaxsave'){
        $ilc_ids = explode(" ", $_POST['ilc_ids']);
        foreach ( $ilc_ids as $ilc_post_id)
            update_post_meta($ilc_post_id, 'optio_movies', $_POST['ilc_val']);
    }
    return;
}
?>