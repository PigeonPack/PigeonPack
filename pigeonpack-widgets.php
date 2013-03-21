<?php
/**
 * Register Pigeon Pack Widgets in WordPress
 *
 * @package Pigeon Pack
 * @since 0.0.1
 */
 
if ( !function_exists( 'register_pigeonpack_widgets' ) ) {
	
	/**
	 * Register our widgets with WP
	 *
	 * @since 0.0.1
	 */
	function register_pigeonpack_widgets() {
		
		register_widget( 'pigeonpack_form_widget' );
	
	}
	add_action( 'widgets_init', 'register_pigeonpack_widgets' );
	
}

/**
 * This class registers and returns the Pigeon Pack subscriber form widget
 *
 * @since 0.0.1
 */
class pigeonpack_form_widget extends WP_Widget {
	
	/**
	 * Set's widget name and description
	 *
	 * @since 0.0.1
	 */
	function pigeonpack_form_widget() {
		
		$widget_ops = array('classname' => 'pigeonpack_form_widget', 'description' => __( 'Pigeon Pack Subscriber Form', 'pigeonpack' ) );
		$this->WP_Widget( 'pigeonpack', __( 'Pigeon Pack Subscriber Form', 'pigeonpack' ), $widget_ops );
	
	}
	
	/**
	 * Displays the widget on the front end
	 *
	 * @since 0.0.1
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	function widget( $args, $instance ) {

		extract( $args );
		
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		
		$output = PigeonPack_Shortcodes::do_subscribe_form( $instance );
		
		if ( ! empty( $output ) ) {
			
			echo $before_widget;
			
			if ( $title)
				echo $before_title . $title . $after_title;
			
			echo $output; 
			
			echo $after_widget;	
		
		}
	
	}

	/**
	 * Save's the widgets options on submit
	 *
	 * @since 0.0.1
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update( $new_instance, $old_instance ) {
		
		$instance 						= $old_instance;
		$instance['title'] 				= strip_tags( $new_instance['title'] );
		$instance['list_id'] 			= $new_instance['list_id'];
		$instance['required_only'] 		= ( "on" == $new_instance['required_only'] ) ? "on" : "off";
	
		return $instance;
	
	}

	/**
	 * Displays the widget options in the dashboard
	 *
	 * @since 0.0.1
	 *
	 * @param array $instance Current settings
	 */
	function form( $instance ) {
	
		$pigeonpack_settings = get_pigeonpack_settings();
		
		//Defaults
		$defaults = array( 
						'title'					=> '',
						'list_id'				=> 0,
					);
		
		extract( wp_parse_args( (array)$instance, $defaults ) );
 
		?>
        
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'pigeonpack' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( strip_tags( $title ) ); ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('list_id'); ?>"><?php _e( 'List ID:', 'pigeonpack' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'list_id' ); ?>" id="<?php echo $this->get_field_id( 'list_id' ); ?>">
                <option value="0" <?php selected( $list_id, 0 ); ?>><?php _e( 'Select a List', 'pigeonpack' ); ?></option>
                <?php
						
				$args = array(
							'posts_per_page'	=> -1,
							'post_type'			=> 'pigeonpack_list'
						);
				$lists = get_posts( $args );
				
				foreach ( $lists as $list ) {
				
					echo '<option value="' . $list->ID . '" ' . selected( $list_id, $list->ID, false ) . '>' . $list->post_title . '</option>';
					
				}
				
				?>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id( 'required_only' ); ?>"><?php _e( 'Only show required fields?', 'pigeonpack' ); ?></label>
            <input class="checkbox" id="<?php echo $this->get_field_id( 'required_only' ); ?>" name="<?php echo $this->get_field_name( 'required_only' ); ?>" type="checkbox" value="on" <?php checked( 'on' == $required_only ) ?> />
        </p>
        
      	<?php
    
	}

}