<?php

/*
 * This option page is based on the class script from Hugh Lashbrooke
 * https://gist.github.com/hlashbrooke/9267467
*/

if ( ! defined( 'ABSPATH' ) ) exit;


class Sendy_Plugin_Settings {

	private $file;
	private $settings_base;
	private $settings;

	public function __construct( $file ) {
		$this->file = $file;
		$this->settings_base = 'fws_sendy_';
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_init' , array( $this, 'register_settings' ) );
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( $this->file ) , array( $this, 'add_settings_link' ) );
	}

	public function init() {
		$this->settings = $this->settings_fields();
	}

	public function add_menu_item() {
		$page = add_options_page(
			__( 'Sendy Subscriptions Settings', 'fws_sendy_subscribe' ),
			__( 'Sendy Subscriptions', 'fws_sendy_subscribe' ),
			'manage_options',
			'fws-sendy-settings',
			array($this, 'settings_page')
		);
	}

	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=plugin_settings">' . __( 'Settings', 'plugin_textdomain' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	private function settings_fields() {
		$settings['standard'] = array(
			'title'					=> __( 'General', 'fws_sendy_subscribe' ),
			'description'			=> __( 'General settings and options for the Sendy plugin.', 'fws_sendy_subscribe' ),
			'fields'				=> array(
				array(
					'id' 			=> 'api_key',
					'label'			=> __( 'Sendy API Key' , 'fws_sendy_subscribe' ),
					'description'	=> __( 'You can find this key in your Sendy application on the main settings page (/settings)', 'plugin_textdomain' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'url',
					'label'			=> __( 'Sendy URL' , 'fws_sendy_subscribe' ),
					'description'	=> __( 'The URL from your Sendy application.', 'fws_sendy_subscribe' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> 'For example: https://yoursendapp.com'
				),
				array(
					'id' 			=> 'list_id',
					'label'			=> __( 'Sendy List ID' , 'fws_sendy_subscribe' ),
					'description'	=> __( 'Create a mailing list and enter the ID here.', 'fws_sendy_subscribe' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'sec_name',
					'label'			=> __( 'Secondary name field' , 'fws_sendy_subscribe' ),
					'description'	=> __( 'Create a custom field like "First name" and enter the field name here.', 'fws_sendy_subscribe' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> 'FirstName'
				),
				array(
					'id' 			=> 'include_css',
					'label'			=> __( 'Include CSS', 'fws_sendy_subscribe' ),
					'description'	=> __( 'Include the plugin\'s stylesheet for your subscribtion forms.', 'fws_sendy_subscribe' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'add_to_content',
					'label'			=> __( 'Show for for all posts', 'fws_sendy_subscribe' ),
					'description'	=> __( 'Use this checkbox to add the subscription form at the end of each blog post', 'fws_sendy_subscribe' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'show_categories',
					'label'			=> __( 'Incl. JS/CSS for categories', 'fws_sendy_subscribe' ),
					'description'	=> __( 'Use this option if you like to use the form on your blog categorie pages.', 'fws_sendy_subscribe' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'show_all_pages',
					'label'			=> __( 'Incl. JS/CSS sitewide', 'fws_sendy_subscribe' ),
					'description'	=> __( 'Use this option if you like to use the form on all your posts and pages.', 'fws_sendy_subscribe' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'gdpr_text',
					'label'			=> __( 'GDPR text' , 'fws_sendy_subscribe' ),
					'description'	=> __( 'Place here your GDPR info text. Don\'t change the link HTML code, we use the privacy URL which is set via "Settings > Privacy".', 'fws_sendy_subscribe' ),
					'type'			=> 'textarea',
					'default'		=> __('We use your personal data according our <a href="%s">privacy statement</a>.'),
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'google_analytics',
					'label'			=> __( 'Track in Google Analytics' , 'fws_sendy_subscribe' ),
					'description'	=> __( 'Track a page view in Google Analytics after the subscription form is submitted.', 'fws_sendy_subscribe' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> '/subscription/submitted.html'
				),
				array(
					'id' 			=> 'clicky',
					'label'			=> __( 'Track in Clicky' , 'fws_sendy_subscribe' ),
					'description'	=> __( 'Add here the goal ID for a manual goal you\'ve already defined in Clicky.', 'fws_sendy_subscribe' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'mailmunch_secret',
					'label'			=> __( 'Mailmunch secret' , 'fws_sendy_subscribe' ),
					'description'	=> __( 'Add here the secret string which you can use to protect your Sendy app against SPAM submissions.', 'fws_sendy_subscribe' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> ''
				)
			)
		);
		$settings = apply_filters( 'plugin_settings_fields', $settings );
		return $settings;
	}

	public function register_settings() {
		if( is_array( $this->settings ) ) {
			foreach( $this->settings as $section => $data ) {
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), 'fws_sendy_plugin_settings' );
				foreach( $data['fields'] as $field ) {
					$option_name = $this->settings_base . $field['id'];
					register_setting( 'fws_sendy_plugin_settings', $option_name );
					add_settings_field( $field['id'], $field['label'], array( $this, 'display_field' ), 'fws_sendy_plugin_settings', $section, array( 'field' => $field ) );
				}
			}
		}
	}

	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	public function display_field( $args ) {
		$field = $args['field'];
		$html = '';
		$option_name = $this->settings_base . $field['id'];
		$option = get_option( $option_name );
		$data = '';
		if( isset( $field['default'] ) ) {
			$data = $field['default'];
			if( $option ) {
				$data = $option;
			}
		}
		switch( $field['type'] ) {
			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>'. "\n";
			break;
			case 'checkbox':
				$checked = '';
				if( $option && 'on' == $option ){
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
			break;
			default:
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";


		}
		$html .= '<label for="' . esc_attr( $field['id'] ) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
		echo $html;
	}

	public function settings_page() {
		$html = '<div class="wrap" id="plugin_settings">' . "\n";
			$html .= '<h2>' . __( 'Sendy Subscription Plus' , 'fws_sendy_subscribe' ) . '</h2>' . "\n";
			$html .= '<p>'.sprintf ( __( 'To use this plugin you need a working Sendy application. Buy your own copy here: <a href="%s" target="_blank">Sendy, self hosted email newsletter application</a>.', 'fws_sendy_subscribe' ), esc_url( 'https://sendy.co/?ref=44zxc' ) ).'</p>' . "\n";
			$html .= '<form method="post" action="options.php">' . "\n";
				$html .= '<ul id="settings-sections" class="subsubsub hide-if-no-js">' . "\n";
					$html .= '<li><a class="tab all current" href="#all">' . __( 'All' , 'fws_sendy_subscribe' ) . '</a></li>' . "\n";
					foreach( $this->settings as $section => $data ) {
						$html .= '<li>| <a class="tab" href="#' . $section . '">' . $data['title'] . '</a></li>' . "\n";
					}
				$html .= '</ul>' . "\n";
				$html .= '<div class="clear"></div>' . "\n";
				ob_start();
				settings_fields( 'fws_sendy_plugin_settings' );
				do_settings_sections( 'fws_sendy_plugin_settings' );
				$html .= ob_get_clean();
				$html .= '<p class="submit">' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'fws_sendy_subscribe' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
			$option_name = $this->settings_base . 'api_key';
			if (get_option($option_name)) $html .= '
			<h3>'.__( 'How to use?', 'fws_sendy_subscribe' ).'</h3>
			<p>'.__( 'You can use a subscription form in every post (see setting above) or you can use the widget for  your theme\'s sidebar. It\'s also possible to add a shortcode to your pages and posts.', 'fws_sendy_subscribe' ).'</p>
			<p><code>[FWSSendySubForm]</code></p>
			<p><code>[FWSSendySubForm secname="FirstName" title="Subscribe today" description="Subscribe now and get future updates in your mailbox."]</code></p>';
		$html .= '</div>' . "\n";
		echo $html;
	}
}
