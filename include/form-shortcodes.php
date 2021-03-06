<?php

class Create_Sendy_Forms extends Subscription_tools {

	public function __construct() {
		parent::__construct();

		add_shortcode('FWSSendySubForm', array($this, 'create_sendy_subform'));
		add_shortcode('FWSSendyUnsubscribe', array($this, 'unsubscribe_form'));
		add_shortcode('getsendycount', array($this, 'get_sendy_list_count'));
		add_shortcode('resubscribeSendy', array($this, 'resubscribe_button'));
	}

	public function get_sendy_list_count($atts = null) {
		$atts = shortcode_atts(
			array(
				'addmore' => 0
			),
			$atts
		);
		$sendy_count = get_transient( 'fws_sendy_list_count' );
		if ( false === $sendy_count  ) {
			$result = $this->make_api_call(array(), 'subscriber_count');
			if ((int)$result > 1) {
				$sendy_count = $result + $atts['addmore'];
			} else {
				$sendy_count = 'n/a';
			}
			set_transient( 'fws_sendy_list_count', $sendy_count, DAY_IN_SECONDS );
		}
		return $sendy_count;
	}

	public function create_sendy_subform($atts = null) {

		$extra_merge_field_name = get_option('fws_sendy_sec_field');
		$atts = shortcode_atts(
			array(
				'title' => __( 'Subscribe now!', 'fws_sendy_subscribe' ),
				'description' => __( 'Subscribe today and get future blog posts your email.', 'fws_sendy_subscribe' ),
				'gdpr_text' => get_option('fws_sendy_gdpr_text'),
				'btnlabel' => __('Subscribe', 'fws_sendy_subscribe'),
				'extramergefield' => '',
				'bs_icon' => '',
				'fsize' => '',
				'container_class' => 'sendy-optin'
			),
			$atts
		);
		$field_size = '';
		$btn_size = '';
		if ($atts['fsize'] != '') {
			$field_size = ' input-'.$atts['fsize'];
			$btn_size = ' btn-'.$atts['fsize'];
		}
		if ($atts['bs_icon'] != '') {
			$btn_lbl = $atts['btnlabel'].' <span class="glyphicon glyphicon-'.$atts['bs_icon'].'" aria-hidden="true"></span>';
		} else {
			$btn_lbl = $atts['btnlabel'];
		}

		if ($atts['gdpr_text'] != '') {
			$gdpr_info = sprintf( wp_kses( $atts['gdpr_text'], array(  'a' => array( 'href' => array() ) ) ), esc_url( get_privacy_policy_url() ) );
		} else {
			$gdpr_info = '';
		}
		return '
		<div class="'.$atts['container_class'].'">
			<h3>'.$atts['title'].'</h3>
			<p>'.$atts['description'].'</p>
			<form id="fws-subscribeform" role="form" class="form-inline">
				<div class="form-group">
					<label class="sr-only" for="firstname">'.__( 'Your first name', 'fws_sendy_subscribe' ).'</label>
					<input type="text" class="form-control'.$field_size.'" placeholder="'.__( 'Your first name', 'fws_sendy_subscribe' ).'" name="name" tabindex="1" />
				</div>
				<div class="form-group">
					<label class="sr-only" for="emailaddress">'.__( 'Your email address', 'fws_sendy_subscribe' ).'</label>
					<input type="text" class="form-control'.$field_size.'" placeholder="'.__( 'Your email address', 'fws_sendy_subscribe' ).'" name="email" tabindex="2" />
				</div>
				'.wp_nonce_field('fwssendy_subform', '_fwssendy_subnonce', true, false).'
				<input type="hidden" name="action" value="sendy_subscribeform_action" />
				<input type="hidden" name="'.$extra_merge_field_name.'" value="'.esc_attr($atts['extramergefield']).'" />
				<button class="btn btn-primary sendy-subscr-fws'.$btn_size.'" tabindex="3" type="button">'.$btn_lbl.'</button>
			</form>
			<p id="fws-subscribeform-msg" class="error-message">&nbsp;</p>
			<p class="privacy">'.$gdpr_info.'</p>
		</div>
		';
	}

	public function unsubscribe_form($atts = null) {
		$atts = shortcode_atts(
			array(
				'title' => __( 'Unsubscribe from your mailing list', 'fws_sendy_subscribe' ),
				'description' => __( 'Enter your email address and click the button to unsubscribe.', 'fws_sendy_subscribe' ),
				'btnlabel' => __('Unsubscribe', 'fws_sendy_subscribe'),
				'fsize' => '',
				'container_class' => 'sendy-optout'
			),
			$atts
		);
		$field_size = '';
		$btn_size = '';
		if ($atts['fsize'] != '') {
			$field_size = ' input-'.$atts['fsize'];
			$btn_size = ' btn-'.$atts['fsize'];
		}
		return '
		<div class="'.$atts['cotainer_class'].'">
			<h3>'.$atts['title'].'</h3>
			<p>'.$atts['description'].'</p>
			<form id="fws-unsubscribeform" role="form" class="form-inline">
				<div class="form-group">
					<label class="sr-only" for="emailaddress">'.__( 'Your email address', 'fws_sendy_subscribe' ).'</label>
					<input type="text" class="form-control'.$field_size.'" placeholder="'.__( 'Your email address', 'fws_sendy_subscribe' ).'" name="email" tabindex="1" />
				</div>
				'.wp_nonce_field('fwssendy_unsubform', '_fwssendy_unsubnonce', true, false).'
				<input type="hidden" name="action" value="sendy_unsubscribe_action" />
				<button class="btn btn-primary sendy-unsub-fws'.$btn_size.'" tabindex="2" type="button">'.$atts['btnlabel'].'</button>
			</form>
			<p id="fws-subscribeform-msg" class="error-message">&nbsp;</p>
		</div>
		';
	}
	public function resubscribe_button($atts = null) {
		$atts = shortcode_atts(
			array(
				'btnlabel' => __('Subscribe again', 'fws_sendy_subscribe'),
				'btnsize' => 'lg',
				're_container_class' => 'sendy-re-optin',
				'subscribe_slug' => 'subscribe'
			),
			$atts
		);
		$valid_request = true;
		$html = '
			<div class="'.$atts['re_container_class'].'">';
		if (empty($_GET['email']) || empty($_GET['listid'])) {
			$valid_request = false;
		} else {
			$default_list = get_option('fws_sendy_list_id');
			$email = sanitize_email($_GET['email']);
			$list = sanitize_text_field($_GET['listid']);
			if ($default_list != $list) {
				$valid_request = false;
			} else {
				$html .= '
				<form id="fws-resubscribeform" role="form">
					'.wp_nonce_field('fwssendy_resubform', '_fwssendy_resubnonce', true, false).'
					<input type="hidden" name="action" value="sendy_resubscribe_action" />
					<input type="hidden" name="email" value="'.$email.'" />
					<button class="btn btn-primary sendy-resub-fws btn-'.$atts['btnsize'].'" type="button">'.$atts['btnlabel'].'</button>
				</form>
				<p id="fws-resubscribeform-msg" class="error-message">&nbsp;</p>';
			}
		}
		if (!$valid_request) {
			$html .= '
				<a href="'.home_url('/'.$atts['subscribe_slug'].'/').'" class="btn btn-primary sendy-unsub-fws btn-'.$atts['btnsize'].'">'.$atts['btnlabel'].'</a>';
		}
		$html .= '
			</div>';
		return $html;
	}

}
