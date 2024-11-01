<?php
/*
Plugin Name: Visitor Contact
Plugin URI: http://www.visitorcontact.com/
Description: Embed Visitor Contact on your blog.
Version: 1.0
*/


// Adding Visitor Contact Asset Files (JS + CSS)

add_action('init', visitor_contact_assets);

function visitor_contact_assets() {
	if (function_exists('wp_enqueue_script')) {
		if (!is_admin()){
			if (!$_POST['visitor_contact_form_id']) {
				if (get_option('visitor_contact_form_id')) {
					if (get_option('visitor_contact_sticky_button') == "true") {
						wp_enqueue_script('visitor_contact_script', 'http://visitorcontact.com/scripts/'.get_option('visitor_contact_form_id').'.js');
					}
				}
			}
		}
	}
	if (function_exists('wp_enqueue_style')) {
			wp_enqueue_style('visitor_contact_style', get_bloginfo('wpurl') . '/wp-content/plugins/visitorcontact/css/visitor-contact.css');
	}
}


// Adding Admin Menu
add_action('admin_menu', 'my_plugin_menu');

function my_plugin_menu() {
	add_options_page('VisitorContact Plugin Setup', 'VisitorContact Plugin', 10, __FILE__, 'visitor_contact_setup');
}


function visitor_contact_setup() {
	
	if ($_POST['action']) {
		if ($_POST['visitor_contact_form_id']) { 
			update_option("visitor_contact_form_id", $_POST['visitor_contact_form_id']);
			if ($_POST['visitor_contact_publish_page']) {
				if (get_option('visitor_contact_publish_page') == "true") {
					wp_delete_post( get_option('visitor_contact_page_id') );
					$visitor_contact_page_id = wp_insert_post(array(
						'post_status' => 'publish',
						'post_type' => 'page',
						'post_name' => 'Contact',
						'post_title' => 'Contact',
						'comment_status' => 'closed',
						'post_content' => '<iframe src="http://visitorcontact.com/embed/'.$_POST['visitor_contact_form_id'].'" frameborder="0" scrolling="no" allowtransparency="true" style="height: 340px; width: 320px;"></iframe>',
					));
				} else {
					$visitor_contact_page_id = wp_insert_post(array(
						'post_status' => 'publish',
						'post_type' => 'page',
						'post_name' => 'Contact',
						'post_title' => 'Contact',
						'comment_status' => 'closed',
						'post_content' => '<iframe src="http://visitorcontact.com/embed/'.$_POST['visitor_contact_form_id'].'" frameborder="0" scrolling="no" allowtransparency="true" style="height: 340px; width: 320px;"></iframe>',
					));
				}
				update_option("visitor_contact_publish_page", "true");
				update_option("visitor_contact_page_id", $visitor_contact_page_id);
			} else {
				wp_delete_post( get_option('visitor_contact_page_id') );
				update_option("visitor_contact_publish_page", "false");
				update_option("visitor_contact_page_id", "");
			}
			// Sticky Button
			if ($_POST['visitor_contact_sticky_button']) {
				update_option("visitor_contact_sticky_button", "true");
			} else {
				update_option("visitor_contact_sticky_button", "false");
			}
		} else {
			update_option("visitor_contact_form_id", "");
		}
	}
	
?>
<div class="wrap">
<div class="icon32" id="icon-options-general"><br/></div>
<h2>VisitorContact Options</h2>
<div class="greenBox">
	<strong>Getting Started</strong>
  <ol>
  	<li>Open <a href="http://www.visitorcontact.com/" target="_blank">http://www.visitorcontact.com/</a> and signup. It's free and takes less than 30 seconds!</li>
    <li>Create your contact form and get the <strong>Form ID</strong> as shown here:<br /><img src="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/visitorcontact/img/buttoncode.jpg" border="0" style="border: 1px solid #CCC;" /></li>
    <li>Type your Form ID in the text field below and click "Save Changes".</li>
    <li>Yipeee! Your contact button is ready and served.</li>
  </ol>
</div>
<?php
	if ($_POST && !$_POST['visitor_contact_form_id']) { 
?>
<div class="redBox">
	<strong>Please enter a valid Form ID</strong>
</div>
<?php
	}
?>
<h2>Form ID</h2>
Enter your Form ID below:<br />
<form method="post" action="">
<input type="text" id="visitor_contact_form_id" name="visitor_contact_form_id" style="border: 1px solid #CCC; width: 200px;" value="<?php echo get_option('visitor_contact_form_id') ; ?>">
<br /><br />
<input type="checkbox" name="visitor_contact_sticky_button" id="visitor_contact_sticky_button" style="border: 0px;" <?php if (get_option('visitor_contact_sticky_button') != "false") { ?>checked<?php } ?> /> <label for="visitor_contact_sticky_button"> Sticky Contact Button</label><br />
<input type="checkbox" name="visitor_contact_publish_page" id="visitor_contact_publish_page" style="border: 0px;" <?php if (get_option('visitor_contact_publish_page') != "false") { ?>checked<?php } ?> /> <label for="visitor_contact_publish_page"> Make a Contact page</label>
<br />
<p class="submit">
	<input type="hidden" name="action" value="update" />
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>
</div>
<?php  
}
?>