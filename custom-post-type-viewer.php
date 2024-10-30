<?php
/*
Plugin Name: Custom Post Type Viewer
Plugin URI: http://wp-project.nazieb.com/plugins/custom-post-type-viewer/
Description: Allows users to choose which post type that will be shown in front page. See the setting on <a href="options-reading.php">Reading Settings</a>
Version: 1.0
Author: Ainun Nazieb
Author URI: http://nazieb.com/
*/

function ctv_activate() {
	delete_option('ctv_types');
	add_option('ctv_types', array('post'));
}
register_activation_hook(__FILE__, 'ctv_activate');

function ctv_where($where, $filter = "") {
	global $wpdb;
	
	$types = get_option('ctv_types');
	$types = implode("', '", $types);
	$where = str_replace("$wpdb->posts.post_type = 'post'", "$wpdb->posts.post_type in ('$types')", $where);
	
	return $where;
}
add_filter('posts_where', 'ctv_where');

function ctv_setting() {
	$types = get_post_types(array('public' => true), 'objects');
	$current = get_option('ctv_types');
	foreach($types as $type) : ?>
	<label>
		<input type="checkbox" name="ctv_types[]" value="<?php echo $type->name; ?>" <?php if(in_array($type->name, $current)) echo "checked='checked'"; ?> />
		<?php echo ucwords($type->name); ?>
	</label><br/><?php
	endforeach;
}

function ctv_sanitize($types) {
	$current = get_post_types(array('public' => true), 'names');
	foreach($types as $key => $type)
		if(!in_array($type, $current)) unset($types[$key]);
	
	return $types;
}

function ctv_field() {
	register_setting('reading', 'ctv_types', 'ctv_sanitize');
	add_settings_field('ctv_types', 'Post type shown in home', 'ctv_setting', 'reading', 'default');
}
add_action('admin_init', 'ctv_field');