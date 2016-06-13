<?php

// Creating the widget 
class OEmbedWidget extends WP_Widget {

  

  function __construct() {
    parent::__construct(
      // Base ID of your widget
      'oembed', 
      
      // Widget name will appear in UI
      __( 'OEmbed', 'oembed' ), 
      
      // Widget description
      array(
        'classname' => 'widget-oembed',
        'description' => __( 'OEmbed Widget', 'oembed' ), 
      )
    
    
    );
  }
  
  // Creating widget front-end
  // This is where the action happens
  public function widget( $args, $instance ) {
    $title = apply_filters( 'widget_title', $instance['title'] );
    $url = isset($instance['url']) ? $instance['url'] : 'null';
    
    // Get provider slug 
    $domain = str_ireplace('www.', '', parse_url($url, PHP_URL_HOST));
    $domain_res = preg_match('/(.*?)((?:\.co)?.[a-z]{2,4})$/i', $domain, $domain_match);
    $domain_name = $domain_res ? $domain_match[1] : $domain;
    $provider_slug = array_pop(explode('.', $domain_name));
    
    // Add provider slug classname
    $widget_name = isset($args['widget_name']) ? sanitize_title($args['widget_name']) : ''; 
    $before_widget = $args['before_widget'] = preg_replace_callback(
      '/(<[^>]*class=\")([^"]*)(\")/',
      function ($matches) use (&$widget_name, $provider_slug) {
        $classes = explode(' ', $matches[2]);
        foreach ($classes as $index => $class) {
          if (strpos($class, 'widget-oembed') !== false) {
            $classes[$index] = $class . " " . $class . "-" . $provider_slug;
          }
        }
        return $matches[1] . (implode(' ', $classes)) . $matches[3];
      },
      $args['before_widget']
    );

    // Generate HTML Code
    $embed_code = apply_filters( 'embed_oembed_html', wp_oembed_get( $url, $args['oembed'] ), $url, $args['oembed'] ) ;
    
    // Output HTML
    echo $args['before_widget'];
    
    if ( ! empty( $title ) )
      echo $args['before_title'] . $title . $args['after_title'];
    
    if ( ! empty( $embed_code ) )
      echo $embed_code;
    
    echo $args['after_widget'];
  }
      
  // Widget Backend 
  public function form( $instance ) {
    
    if ( isset( $instance[ 'title' ] ) ) {
      $title = $instance[ 'title' ];
    } else {
      $title = __( 'New title', 'oembed' );
    }
    
    if ( isset( $instance[ 'url' ] ) ) {
      $url = $instance[ 'url' ];
    } else {
      $url = __( '', 'oembed' );
    }
    // Widget admin form
  ?>
  <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e( 'URL:' ); ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" type="text" value="<?php echo esc_attr( $url ); ?>" />
  </p>
  <?php 
  }
    
  // Updating widget replacing old instances with new
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['url'] = ( ! empty( $new_instance['url'] ) ) ? $new_instance['url'] : '';
    return $instance;
  }
} // Class wpb_widget ends here

// Register and load the widget
function wp_oembed_load_widget() {
  register_widget( 'OEmbedWidget' );
}
add_action( 'widgets_init', 'wp_oembed_load_widget' );




// Override max-width of oembed-container in widgets
function wp_oembed_widget_enqueue_scripts() {
  $custom_css = "
    .widget-oembed .oembed-container {
      max-width: 100% !important;
    }
  ";
  wp_add_inline_style( 'oembed-responsive-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'wp_oembed_widget_enqueue_scripts' );



?>