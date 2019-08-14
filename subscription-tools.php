<?php
/*
Plugin Name: Subscription tools for Sendy
Version: 1.0.0
Plugin URI: https://www.web-development-blog.com/
Description: Increase the count of new subscribers for your blog or website by using Sendy and a professional subscription form.
Author: Olaf Lederer
Author URI: https://www.olaflederer.com/
Text Domain: fws_sendy_subscribe
Domain Path: /languages/
License: GPL v3

Subscription tools for Sendy
Copyright (C) 2019, Olaf Lederer - https://www.olaflederer.com/

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

define('STFS_DIR', plugin_dir_path( __FILE__ ));

include_once STFS_DIR.'include/options.php';
include_once STFS_DIR.'include/form-shortcodes.php';
include_once STFS_DIR.'include/widget.php';


if ( ! defined( 'ABSPATH' ) ) exit;

class Subscription_tools {

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public function init() {
		load_plugin_textdomain( 'fws_sendy_subscribe', false, STFS_DIR . 'languages/' );

		add_filter( 'the_content', array($this, 'add_form_to_content'), 20 );

		add_action('wp_enqueue_scripts', array($this, 'add_assets'));

		add_action( 'wp_ajax_subscribeform_action', array($this, 'subform_action_callback') );
		add_action( 'wp_ajax_nopriv_subscribeform_action', array($this, 'subform_action_callback') );

		add_action( 'wp_ajax_unsubscribe_action', array($this, 'unsubscribe_action_callback') );
		add_action( 'wp_ajax_nopriv_unsubscribe_action', array($this, 'unsubscribe_action_callback') );

		add_action('wp_ajax_mailmunch_action', array($this, 'process_mailmunch_request'));
		add_action('wp_ajax_nopriv_mailmunch_action', array($this, 'process_mailmunch_request'));

		add_action('add_meta_boxes', array($this, 'add_custom_box'));
		add_action('save_post', array($this, 'save_custom_box'));


	}

	public function add_assets() {
		global $post;
		$show = false;
		if ( get_option('fws_sendy_show_all_pages') ) {
			$show = true;
		}
		if (is_singular(array('post', 'page'))) {
			if (is_a( $post, 'WP_Post' ) && (has_shortcode( $post->post_content, 'FWSSendySubForm') || has_shortcode( $post->post_content, 'FWSSendyUnsubscribe'))) {
				$show = true;
			}
			if (get_option('fws_sendy_add_to_content') || get_option('fws_sendy_show_all_pages')) {
				$show = true;
				if (get_post_meta($post->ID, 'fws_sendy_hide_form', true)) {
					$show = false;
				}
			}
		}
		if (is_active_widget( false, false, 'sendy-subscription-widget', true )) {
			$show = true;
		}
		if (is_category()) {
			if (false == get_option('fws_sendy_show_categories')) {
				$show = false;
			}
		}
		if ($show) {
			wp_enqueue_script('fws-sendy', plugin_dir_url(__FILE__).'include/sendy.js', array('jquery'), '', true );
			wp_localize_script( 'fws-sendy', 'msp_ajax_object',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'plugin_base_path' => plugin_dir_url(__FILE__),
					'js_alt_loading' => __( 'Loading...', 'fws_sendy_subscribe' ),
					'js_msg_enter_email_name' => __( 'Please enter your name and email address.', 'fws_sendy_subscribe' ),
					'js_msg_enter_email' => __( 'Please enter your email address.', 'fws_sendy_subscribe' ),
					'js_msg_invalid_email' => __( 'The entered email address is invalid.', 'fws_sendy_subscribe' ),
					'googleanalytics' => get_option('fws_sendy_google_analytics'),
					'clickyanalytics' => get_option('fws_sendy_clicky')
				)
			);
			if (get_option('fws_sendy_include_css')) {
				wp_enqueue_style( 'fws-sendy-style', plugin_dir_url(__FILE__).'include/style.css' );
			}
		}
	}



	public function make_api_call($data, $action = 'subscribe') {
		$url = get_option('fws_sendy_url');
		$list = get_option('fws_sendy_list_id');
		$sec_name = get_option('fws_sendy_sec_name');
		$api_key = get_option('fws_sendy_api_key');
		if ($action == 'subscribe') {
			$post_array = array(
				'name' => $data['name'],
				'email' => $data['email'],
				'ipaddress' => $this->get_client_ip(),
				'boolean' => 'true',
				'list' => $list
			);
			if (!empty($data['referrer'])) $post_array['referrer'] = $data['referrer'];
			if (!empty($data[$sec_name])) $post_array[$sec_name] = $data[$sec_name];
		} elseif ($action == 'mailmunch') {
			$action = 'subscribe';
			$post_array = array(
				'name' => $data['name'],
				'email' => $data['email'],
				'ipaddress' => $data['ipaddress'],
				'referrer' => $data['referrer'],
				'boolean' => 'true',
				'list' => $list
			);
		} elseif ($action == 'subscriber_count') {
			$action = '/api/subscribers/active-subscriber-count.php';
			$post_array = array(
				'list_id' => $list,
				'api_key' => $api_key
			);
		} elseif ($action == 'subscriber_status') {
			$action = '/api/subscribers/subscription-status.php';
			$post_array = array(
				'list_id' => $list,
				'api_key' => $api_key,
				'email' => $data['email']
			);
		} elseif ($action == 'unsubscribe') {
			$post_array = array(
				'list_id' => $list,
				'email' => $data['email'],
				'boolean' => 'true'
			);
		}
		$postdata = http_build_query($post_array);
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url.'/'.$action);
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-type: application/x-www-form-urlencoded'
		));
		$result = curl_exec($ch);
		return $result;
	}

	public function subform_action_callback() {
		global $wp;
		$error = '';
		$status = 'error';

		if (empty($_POST['name']) || empty($_POST['email'])) {
			$error = __( 'Both fields are required to enter.', 'fws_sendy_subscribe' );
		} else {
			if (!wp_verify_nonce($_POST['_fwssendy_subnonce'], 'fwssendy_subform')) {
				$error = __( 'Verification error, try again.', 'fws_sendy_subscribe' );
			} else {
				$data = array();
				$data['name'] = sanitize_text_field($_POST['name']);
				$data['email'] = sanitize_email($_POST['email']);
				$data['referrer'] = home_url( $wp->request );

				$sec_name = get_option('fws_sendy_sec_name');
				if (isset($data[$sec_name])) {
					$data[$sec_name] = sanitize_text_field($_POST[$sec_name]);
				}
				$result = $this->make_api_call($data);

				if ($result == 1) {
					$status = 'success';
					$error = __( 'Thanks, for joining our mailing list!', 'fws_sendy_subscribe' );
				} elseif ($result == 'Already subscribed.') {
					$error = __( 'You\'re already subscribed to this list.', 'fws_sendy_subscribe' );
				} else {
					$error = __( 'An unknown error occurred.', 'fws_sendy_subscribe' );
				}
			}
		}
		$resp = array('status' => $status, 'errmessage' => $error);
		header( "Content-Type: application/json" );
		echo json_encode($resp);
		die();
	}

	public function unsubscribe_action_callback() {
		$error = '';
		$status = 'error';

		if (empty($_POST['email'])) {
			$error = __( 'Please enter your email address.', 'fws_sendy_subscribe' );
		} else {
			if (!wp_verify_nonce($_POST['_fwssendy_unsubnonce'], 'fwssendy_subform')) {
				$error = __( 'Verification error, try again.', 'fws_sendy_subscribe' );
			} else {
				$data = array();
				$data['email'] = sanitize_email($_POST['email']);

				$result = $this->make_api_call($data, 'unsubscribe');

				if ($result == 'true') {
					$status = 'success';
				} elseif ($result == 'Email does not exist.') {
					$error = __( 'Your email address is not on our list.', 'fws_sendy_subscribe' );
				} else {
					$error = __( 'An unknown error occurred.', 'fws_sendy_subscribe' );
				}
			}
		}
		$resp = array('status' => $status, 'errmessage' => $error);
		header( "Content-Type: application/json" );
		echo json_encode($resp);
		die();
	}

	public function process_mailmunch_request() {
		$text = '';
		if ($this->mailmunch_secure_request()) {
			$data['name'] = sanitize_text_field($_POST['name']);
			$data['email'] = sanitize_email($_POST['email']);
			$data['ipaddress'] = sanitize_text_field($_POST['ip-address']);
			$data['referrer'] = sanitize_text_field($_POST['referral']);
			$text .= implode(PHP_EOL, $data);
			$resp = $this->make_api_call($data, 'mailmunch');

			$text .= PHP_EOL.'Sendy: '.$resp;
		}
		if (WP_DEBUG) file_put_contents(STFS_DIR.'test.log', PHP_EOL.$text, FILE_APPEND);
		die();
	}

	public function add_custom_box() {
		//$curr_screen = get_current_screen();
		//print_r($curr_screen);
		$screens = array('post', 'page');
		foreach ($screens as $screen) {
			add_meta_box(
				'fws_sendy_box_id',
				'Sendy options',
				array($this, 'sendy_custom_box_html'),
				$screen,
				'side',
				'high'
			);
		}
	}

	public function sendy_custom_box_html($post) {
		$value = get_post_meta($post->ID, 'fws_sendy_hide_form', true);
		wp_nonce_field( 'fws_action_save_sendy_hide', 'fws_sendy_hide_nonce_field' );
		echo '
			<input type="checkbox" name="fws_sendy_hide_form" id="hide_sendy_form" value="1"'.checked( $value, 1, false ).'>
			<label for="hide_sendy_form">'.__('Hide Sendy form', 'fws_sendy_subscribe').'</label>
		';
	}

	public function save_custom_box($post_id) {
		if ( isset( $_POST['fws_sendy_hide_nonce_field'] ) && wp_verify_nonce( $_POST['fws_sendy_hide_nonce_field'], 'fws_action_save_sendy_hide' ) ) {
			if (array_key_exists('fws_sendy_hide_form', $_POST)) {
				$value = (int)$_POST['fws_sendy_hide_form'];
			} else {
				$value = 0;
			}
    		update_post_meta(
	            $post_id,
	            'fws_sendy_hide_form',
	            $value
        	);
		}
	}


	public function add_form_to_content($content) {
		if (get_option('fws_sendy_add_to_content') && is_singular(array('post', 'page'))) {
			if (method_exists($this, 'create_subform')) {
				$content .= $this->create_subform();
			}
		}
		return $content;
	}

	public function get_client_ip() {
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
			if (array_key_exists($key, $_SERVER) === true){
				foreach (explode(',', $_SERVER[$key]) as $ip){
					$ip = trim($ip); // just to be safe

					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
						return $ip;
					}
				}
			}
		}
	}

	public function mailmunch_secure_request() {
		$secret = get_option('fws_sendy_mailmunch_secret');
		$time = $_SERVER['HTTP_X_MAILMUNCH_TIME'];
		$authorization = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;
		if (empty($authorization)) return false;
		list($algo, $hash) = explode(' ', $authorization);
		if (hash('sha256', $secret . $time, true) == base64_decode($hash)) {
			return true;
		} else {
			return false;
		}
	}

}

$fws_sendy_settings = new Sendy_Plugin_Settings( __FILE__ );
$fws_sendy = new Create_Sendy_Forms();
