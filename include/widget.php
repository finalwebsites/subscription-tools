<?php
/**
 * Subscription form widget
 */

function register_SENDY_subscription_Widget() {
    register_widget( 'SENDY_subscription_Widget' );
}
add_action( 'widgets_init', 'register_SENDY_subscription_Widget' );

class SENDY_subscription_Widget extends WP_Widget {

	/**
	 * Init widget
	 */
	function __construct() {
		parent::__construct(
			'sendy-subscription-widget', // Base ID
			__('Sendy Subscriptions', 'fws_sendy_subscribe'), // Name
			array(
				'description' => __( 'Use this widget to add a simple Sendy subscription form to the different sidebars.', 'fws_sendy_subscribe' )
			)
		);
	}

	/**
	 * Front-end display
	 */
	function widget( $args, $instance ) {

		/* Before widget (defined by themes). */
		echo $args['before_widget'];

		/* Display the widget title if one was input (before and after defined by themes). */
		$title = apply_filters('widget_title', $instance['title'] );
		if ( $title ) echo $args['before_title'] . $title . $args['after_title'];
		$description = (!empty($instance['filter'])) ? wpautop($instance['description']) : $instance['description'];
		if ($description != '') echo '
		<div class="fws-widget-description">'.$description.'</div>';
		echo '
		<form id="fws-widget-form" role="form">
			<div class="form-group">
				<label class="sr-only" for="firstname">'.__( 'Your first name', 'fws_sendy_subscribe' ).'</label>
				<input type="text" class="form-control defaultText" title="'.__( 'Your first name', 'fws_sendy_subscribe' ).'" name="name" tabindex="1" />
			</div>
			<div class="form-group">
				<label class="sr-only" for="emailaddress">'.__( 'Your email address', 'fws_sendy_subscribe' ).'</label>
				<input type="text" class="form-control defaultText" title="'.__( 'Your email address', 'fws_sendy_subscribe' ).'" name="email" tabindex="2" />
			</div>
			'.wp_nonce_field('fwssendy_subform', '_fwssendy_subnonce', true, false).'
			<input type="hidden" name="action" value="subscribeform_action" />
			<button class="btn btn-primary btn-sm sendy-subscr-fws" tabindex="3" type="button">'.__( 'Subscribe', 'fws_sendy_subscribe' ).'</button>
		</form>
		<div id="fws-widget-msg" class="error-message"></div>';

		/* After widget (defined by themes). */
		echo $args['after_widget'];
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['description'] = stripslashes(wp_filter_post_kses($new_instance['description']));
		$instance['filter'] = (isset($new_instance['filter'])) ? 1 : 0;
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 */
	function form( $instance ) {

		$defaults = array( 'title' => '', 'description' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults );
		$filter = (!empty($instance['filter'])) ? 1 : 0;

		echo '
		<p>
			<label for="'.$this->get_field_id( 'title' ).'">'.__( 'Title:', 'fws_sendy_subscribe' ).'</label>
			<input id="'.$this->get_field_id( 'title' ).'" type="text" name="'.$this->get_field_name( 'title' ).'" value="'.$instance['title'].'" class="widefat" />
		</p>
		<p>'.__( 'Optional: Add some promotional text (HTML allowed).', 'fws_sendy_subscribe' ).'</p>
		<textarea class="widefat" cols="20" rows="5" id="'.$this->get_field_id( 'description' ).'" name="'.$this->get_field_name( 'description' ).'">'.$instance['description'].'</textarea>
		<p>
			<input id="'.$this->get_field_id( 'filter' ).'" name="'.$this->get_field_name( 'filter' ).'" type="checkbox" value="1" '.checked( 1, $filter, false ).'  />
			&nbsp;<label for="'.$this->get_field_id( 'filter' ).'">'.__( 'Automatically add paragraphs', 'fws_sendy_subscribe' ).'</label>
		</p>';
	}
}
