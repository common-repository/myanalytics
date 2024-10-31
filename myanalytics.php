<?php
/*
Plugin Name: My Analytics
Description: Affiche le tag Google Analytics ainsi que le message d'obligation légale sur les cookies.
Version: 7.0.1
Author: Tom Baumgarten
Author URI: http://www.tombgtn.fr/
Text Domain: myanalytics
Domain Path: /lang
License: GPL2
*/

function myanalytics_use_webmaster_tools() {
	return (get_option('myanalytics_setting_use_webmaster_tools')=='1');
}

function myanalytics_in_footer() {
	return !myanalytics_use_webmaster_tools();
}

function myanalytics_get_code_id() {
	return (get_option('myanalytics_setting_code_id')) ? addslashes(get_option('myanalytics_setting_code_id')) : addslashes("UA") ;
}

function myanalytics_get_ga4() {
	return myanalytics_get_code_id()==='G';
}

function myanalytics_get_code() {
	return (get_option('myanalytics_setting_code')) ? addslashes('-'.get_option('myanalytics_setting_code')) : addslashes("-ZZZZZZ") ;
}

function myanalytics_get_code_letter() {
	return (get_option('myanalytics_setting_code_letter') && !myanalytics_get_ga4()) ? addslashes('-'.get_option('myanalytics_setting_code_letter')) : '' ;
}

function myanalytics_get_message() {
	return (get_option('myanalytics_setting_message')) ? addslashes(get_option('myanalytics_setting_message')) : addslashes(__("We use Google Analytics. By continuing to navigate, you authorize us to drop a cookie for the purpose of audience measurement.", 'myanalytics' )) ;
}

function myanalytics_get_message_dnt() {
	return (get_option('myanalytics_setting_message_dnt')) ? addslashes(get_option('myanalytics_setting_message_dnt')) : addslashes(__("You have enabled DoNotTrack, we respect your choice.", 'myanalytics' )) ;
}

function myanalytics_get_message_decline() {
	return (get_option('myanalytics_setting_message_decline')) ? addslashes(get_option('myanalytics_setting_message_decline')) : addslashes(__("You objected to drop a cookie for the purpose of audience measurement.", 'myanalytics' )) ;
}

function myanalytics_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) { wp_die('Access denied'); }
	?>
	<div class="wrap myanalytics-admin">
		<h2><?php echo get_admin_page_title(); ?></h2>
		<form method="post" action="options.php">
			<?php settings_fields('myanalytics_settings'); ?>
			<?php do_settings_sections('myanalytics_settings'); ?>
			<?php submit_button(); ?>
		</form>
	</div><?php
}

function myanalytics_section_html() {}

function myanalytics_setting_code_html() { ?>
	<select
		name="myanalytics_setting_code_id"
		onchange="if(this.value=='G'){document.getElementById('myanalytics_code_letter_only_v1').style.display='none';}else{document.getElementById('myanalytics_code_letter_only_v1').style.display='initial';}"
	>
		<option value="UA" <?php selected(get_option('myanalytics_setting_code_id'), 'UA'); ?>>UA</option>
		<option value="G" <?php selected(get_option('myanalytics_setting_code_id'), 'G'); ?>>G</option>
	</select>-<input
		type="text"
		name="myanalytics_setting_code"
		maxlength="10"
		value="<?php echo (get_option('myanalytics_setting_code')) ? get_option('myanalytics_setting_code') : ((get_option('myanalytics_setting_code_id')=='G')?'XXXXXXXXXX':'XXXXXXXX') ;?>"
	/><span
		id="myanalytics_code_letter_only_v1"
		style="display:<?php echo(get_option('myanalytics_setting_code_id')=='G') ? 'none' : 'initial' ; ?>"
		>-<input
			type="text"
			class="small-text"
			name="myanalytics_setting_code_letter"
			maxlength="2"
			value="<?php echo (get_option('myanalytics_setting_code_letter')) ? get_option('myanalytics_setting_code_letter') : '1' ;?>"
	/></span>
<?php }

function myanalytics_setting_message_html() { ?>
	<input type="text" class="large-text" name="myanalytics_setting_message" value="<?php echo (get_option('myanalytics_setting_message')) ? get_option('myanalytics_setting_message') : __("We use Google Analytics. By continuing to navigate, you authorize us to drop a cookie for the purpose of audience measurement.", 'myanalytics' ) ;?>"/>
<?php }

function myanalytics_setting_message_dnt_html() { ?>
	<input type="text" class="large-text" name="myanalytics_setting_message_dnt" value="<?php echo (get_option('myanalytics_setting_message_dnt')) ? get_option('myanalytics_setting_message_dnt') : __("You have enabled DoNotTrack, we respect your choice.", 'myanalytics' ) ;?>"/>
<?php }

function myanalytics_setting_message_decline_html() { ?>
	<input type="text" class="large-text" name="myanalytics_setting_message_decline" value="<?php echo (get_option('myanalytics_setting_message_decline')) ? get_option('myanalytics_setting_message_decline') : __("You objected to drop a cookie for the purpose of audience measurement.", 'myanalytics' ) ;?>"/>
<?php }

function myanalytics_setting_use_webmaster_tools_html() { ?>
	<input type="checkbox" name="myanalytics_setting_use_webmaster_tools" value="1" <?php echo (get_option('myanalytics_setting_use_webmaster_tools')=='1') ? 'checked' : '' ;?>/>
<?php }

function myanalytics_load_js() {
	wp_enqueue_script('myanalytics', plugin_dir_url(__FILE__).'myanalytics.js', array(), null, myanalytics_in_footer());
	wp_add_inline_script('myanalytics', 'var myanalytics_ga4 = '.((myanalytics_get_ga4()) ? 'true' : 'false').';', 'before');
	wp_add_inline_script('myanalytics', 'var myanalytics_code = "'.myanalytics_get_code_id().myanalytics_get_code().myanalytics_get_code_letter().'";', 'before');
	wp_add_inline_script('myanalytics', 'var myanalytics_message = "'.myanalytics_get_message().'";', 'before');
	wp_add_inline_script('myanalytics', 'var myanalytics_message_dnt = "'.myanalytics_get_message_dnt().'";', 'before');
	wp_add_inline_script('myanalytics', 'var myanalytics_message_decline = "'.myanalytics_get_message_decline().'";', 'before');
}
add_action('wp_enqueue_scripts', 'myanalytics_load_js');

function myanalytics_add_menu() {
	add_options_page( 'MyAnalytics', 'MyAnalytics', 'manage_options', 'myanalytics', 'myanalytics_settings_page');
}
if ( is_admin() ) { add_action('admin_menu', 'myanalytics_add_menu'); }

function myanalytics_register_settings() {
	register_setting('myanalytics_settings', 'myanalytics_setting_code_id');
	register_setting('myanalytics_settings', 'myanalytics_setting_code');
	register_setting('myanalytics_settings', 'myanalytics_setting_code_letter');
	register_setting('myanalytics_settings', 'myanalytics_setting_message');
	register_setting('myanalytics_settings', 'myanalytics_setting_message_dnt');
	register_setting('myanalytics_settings', 'myanalytics_setting_message_decline');
	register_setting('myanalytics_settings', 'myanalytics_setting_use_webmaster_tools');

	add_settings_section('myanalytics_section', __('Settings'),'myanalytics_section_html', 'myanalytics_settings');

	add_settings_field('myanalytics_setting_code', __('Analytics Code', 'myanalytics' ), 'myanalytics_setting_code_html', 'myanalytics_settings', 'myanalytics_section');
	add_settings_field('myanalytics_setting_message', __('Message', 'myanalytics' ), 'myanalytics_setting_message_html', 'myanalytics_settings', 'myanalytics_section');
	add_settings_field('myanalytics_setting_message_dnt', __('Message if DNT is enabled', 'myanalytics' ), 'myanalytics_setting_message_dnt_html', 'myanalytics_settings', 'myanalytics_section');
	add_settings_field('myanalytics_setting_message_decline', __('Message for objection', 'myanalytics' ), 'myanalytics_setting_message_decline_html', 'myanalytics_settings', 'myanalytics_section');
	add_settings_field('myanalytics_setting_use_webmaster_tools', __('Webmaster Tools are used', 'myanalytics' ), 'myanalytics_setting_use_webmaster_tools_html', 'myanalytics_settings', 'myanalytics_section');
}
add_action('admin_init', 'myanalytics_register_settings');

function myanalytics_link_settings( $links, $file ) {
	array_unshift( $links, '<a href="'.admin_url( 'options-general.php?page=myanalytics' ).'">'.__('Settings').'</a>');
	return $links;
}
add_filter( 'plugin_action_links_'.plugin_basename( __FILE__ ), 'myanalytics_link_settings', 10, 2 );

function myanalytics_load_languages(){
	load_child_theme_textdomain( 'myanalytics', plugin_dir_path(__FILE__).'lang' );
}
add_action( 'after_setup_theme', 'myanalytics_load_languages' );