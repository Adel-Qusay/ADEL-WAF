<?php
/*
Plugin Name: ADEL WAF
Plugin URI: http://wordpress.org/plugins/adel-waf/
Description: ADEL WAF - ADEL WAF is a lightweight WordPress Web Application Firewall.
Author: Adel Qusay
Version: 1.0
Author URI: https://github.com/Adel-Qusay
*/

define('AdelWAF_PLUGIN_DIR', plugin_dir_path(__FILE__));
require_once AdelWAF_PLUGIN_DIR . 'AdelWAF.php';

$settings  = (array) get_option( 'adel_waf_settings' );
$settings['waf_status']             = isset( $settings['waf_status'] )           ? $settings['waf_status']           : 1;
$settings['waf_logs']               = isset( $settings['waf_logs'] )             ? $settings['waf_logs']             : 0;
$settings['waf_logs_file']          = isset( $settings['waf_logs_file'] )        ? $settings['waf_logs_file']        : time().'.log';
$settings['waf_notification']       = isset( $settings['waf_notification'] )     ? $settings['waf_notification']     : 0;
$settings['waf_email']              = isset( $settings['waf_email'] )            ? $settings['waf_email']            : get_option( 'admin_email' );

function adel_waf( $content ) {
	global $settings;
	$AdelWAF = new AdelWAF;
	$AdelWAF->ENABLE_WAF = $settings['waf_status'];
	$AdelWAF->ENABLE_EMAIL_NOTIFICATIONS = $settings['waf_notification'];
	$AdelWAF->EMAIL = $settings['waf_email'];
	$AdelWAF->ENABLE_LOGS = $settings['waf_logs'];
	$AdelWAF->LOGS_FILE = AdelWAF_PLUGIN_DIR.$settings['waf_logs_file'];	
	$AdelWAF->isDA();
	$AdelWAF->run();
}

add_action( 'posts_selection', 'adel_waf' );

function adel_waf_init() {
	register_setting( 'adel_waf_settings_group', 'adel_waf_settings', 'adel_waf_validation' );
}

add_action( 'admin_init', 'adel_waf_init' );

function adel_waf_validation( $input ) {
	$input['waf_email'] = wp_filter_nohtml_kses( $input['waf_email'] );
	$input['waf_logs_file'] = wp_filter_nohtml_kses( $input['waf_logs_file'] );
	return $input;
}

function adel_waf_settings_link( $links, $file ) {
	static $this_plugin;

	if ( empty( $this_plugin ) )
		$this_plugin = plugin_basename( __FILE__ );

	if ( $file == $this_plugin )
		$links[] = '<a href="' . admin_url( 'options-general.php?page=adel_waf' ) . '">' . __( 'Settings', 'adel-waf' ) . '</a>';

	return $links;
}

add_filter( 'plugin_action_links', 'adel_waf_settings_link', 10, 2 );

function adel_waf_plugin_menu() {
	add_options_page( 'ADEL WAF Options', 'ADEL WAF', 'manage_options', 'adel_waf', 'adel_waf_settings' );
}

add_action( 'admin_menu', 'adel_waf_plugin_menu' );

function adel_waf_settings() {
	global $settings;
?>

	<div class="wrap">
		<h1><?php _e( 'ADEL WAF Settings', 'adel_waf' ); ?></h1>
		<p><?php _e( 'The ADEL WAF is a Web Application Firewall for Wordpress. Protects against web attacks.', 'adel_waf' ); ?></p>
		<p><?php printf( __( '%1$sTest your configuration now!%2$s', 'adel_waf' ), '<a href="'.get_bloginfo( 'wpurl' ).'/?s=<script>alert(test)</script>" target="_blank">', '</a>' ); ?></p>
		<div class="clear" id="poststuff" style="width: 50%;">
			<form method="post" action="options.php">
<?php
settings_fields( 'adel_waf_settings_group' );
?>
				<div class="postbox">
					
					<div class="inside">
						<h3 style="cursor: default;"><?php _e( 'Manage firewall', 'adel_waf' ); ?></h3>
						<table class="widefat">
							<tr valign="top">
								<th scope="row" width="50%"><?php _e( 'Firewall status', 'adel_waf' ); ?></th>
								<td>
									<select name="adel_waf_settings[waf_status]">
										<option <?php selected( 1, $settings['waf_status'] ); ?> value="1">
											<?php _e( 'ON', 'adel_waf' ); ?>
										</option>
										<option <?php selected( 0, $settings['waf_status'] ); ?> value="0">
											<?php _e( 'OFF', 'adel_waf' ); ?>
										</option>
									</select>
								</td>
							</tr>
						</table>
					</div>
					<div class="inside">
						<h3 style="cursor: default;"><?php _e( 'Manage logs', 'adel_waf' ); ?></h3>
						<table class="widefat">
							<tr valign="top">
								<th scope="row" width="50%"><?php _e( 'Logs status', 'adel_waf' ); ?></th>
								<td>
									<select name="adel_waf_settings[waf_logs]">
										<option <?php selected( 1, $settings['waf_logs'] ); ?> value="1">
											<?php _e( 'ON', 'adel_waf' ); ?>
										</option>
										<option <?php selected( 0, $settings['waf_logs'] ); ?> value="0">
											<?php _e( 'OFF', 'adel_waf' ); ?>
										</option>
									</select>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row" width="50%">
									<?php _e( 'Logs file' , 'adel_waf' ); ?>
								</th>
								<td>
									<input type="text" name="adel_waf_settings[waf_logs_file]" value="<?php echo $settings['waf_logs_file']; ?>" />
								</td>
							</tr>							
						</table>
					</div>
					<div class="inside">
						<h3 style="cursor: default;"><?php _e( 'Manage notification', 'adel_waf' ); ?></h3>					
						<table class="widefat">
							<tr valign="top">
								<th scope="row"><?php _e( 'Send notifications', 'adel_waf' ); ?></th>
								<td>
									<input type="checkbox" name="adel_waf_settings[waf_notification]" value="1" id="waf_notification" <?php checked( '1', $settings['waf_notification'] ); ?> />
								</td>
							</tr>						
							<tr valign="top">
								<th scope="row" width="50%">
									<?php _e( 'Email address' , 'adel_waf' ); ?>
								</th>
								<td>
									<input type="text" name="adel_waf_settings[waf_email]" value="<?php echo $settings['waf_email']; ?>" />
								</td>
							</tr>
							
						</table>
					</div>
				</div>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'adel_waf' ) ?>" />
				</p>
			</form>
		</div>
		<!-- /poststuff -->
	</div>
	<!-- /wrap -->
<?php }

function adel_waf_load_languages() {
	load_plugin_textdomain( 'adel_waf', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'init', 'adel_waf_load_languages' );
