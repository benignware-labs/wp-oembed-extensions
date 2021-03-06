<?php
function wp_oembed_responsive_enqueue_scripts() {
  $url = plugins_url( 'wp_oembed_responsive.css', __FILE__ );
  wp_enqueue_style(
    'oembed-responsive-style',
    $url
  );
}
add_action( 'wp_enqueue_scripts', 'wp_oembed_responsive_enqueue_scripts' );

function wp_oembed_responsive_mce_css( $mce_css ) {
  $url = plugins_url( 'wp_oembed_responsive.css', __FILE__ );
  if ( ! empty( $mce_css ) )
    $mce_css .= ',';

  $mce_css .= $url;

  return $mce_css;
}
add_filter( 'mce_css', 'wp_oembed_responsive_mce_css' );

function wp_oembed_responsive_oembed_filter($html, $url, $attr, $post_ID = null) {
  if (is_admin()) {
    return $html;
  }
  $iframe_atts_matches = array();
  $iframe_match = preg_match_all('/<iframe[^>]*>/Ui', $html, $iframe_atts_matches);
  $container_match = preg_match('~class="oembed-container"~', $html);
  if (strlen(trim($html)) > 0 && $iframe_match && !$container_match) {
    $embed_atts_string =  $iframe_atts_matches[0][0];
    $embed_size_pattern = '/(width|height)="([0-9]*)"/i';
    preg_match_all($embed_size_pattern, $embed_atts_string, $embed_size_matches, PREG_SET_ORDER);
    $size = array();
    foreach ($embed_size_matches as $match) {
      if ($match[1] === 'width') {
        $size[0] = $match[2];
      } elseif ($match[1] === 'height') {
        $size[1] = $match[2];
      }
    }
    $container_html = '';
    $container_html.= '<div class="oembed-container" style="';
    $container_html.= 'max-width: ' . $size[0] . 'px; ';
    $container_html.= 'max-height: ' . $size[1] . 'px; ';
    $container_html.= '">';
    $container_html.= '<div class="oembed-responsive"';
    if (count($size) == 2) {
      $ratio = $size[1] / $size[0];
      $container_html.= ' style="';
      $container_html.= 'padding-bottom: ' . ($ratio * 100) . '%; ';
      $container_html.= '"';
    }
    $container_html.= ">";
    $container_html.= $html;
    $container_html.= '</div>';
    $container_html.= '</div>';
    return $container_html;
  } else if ($container_match) {
    // Clean up empty oembed-containers
    $html = preg_replace("~<div[^>]*class=['\"][^'\"]*oembed-container['\"][^>]*>\s*</div>~", "", $html);
  }
  return $html;
}
add_filter( 'embed_oembed_html', 'wp_oembed_responsive_oembed_filter', 10, 4 ) ;

?>
