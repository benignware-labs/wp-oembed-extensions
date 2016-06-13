<?php

function wp_oembed_params_filter($html, $url, $attr, $post_ID) {
  if (isset($attr['params'])) {
    $matched = preg_match("/iframe[^>]*src\s*=\s*[\"']([^\"']*)/", $html, $src_match, PREG_OFFSET_CAPTURE);
    if ($matched) {
      $src_offset = $src_match[1][1];
      $src = $src_match[1][0];
      $srcinfo = parse_url($src);
      parse_str($srcinfo['query'], $params);
      // Get provider slug 
      $domain = str_ireplace('www.', '', $srcinfo['host']);
      $domain_res = preg_match('/(.*?)((?:\.co)?.[a-z]{2,4})$/i', $domain, $domain_match);
      $domain_name = $domain_res ? $domain_match[1] : $domain;
      $provider_slug = array_pop(explode('.', $domain_name));
      // Provider params
      if (isset($attr['params'][$provider_slug])) {
        // Get Provider attributes and merge with existing params
        $params = array_merge_recursive($attr['params'][$provider_slug], $params);
        // Build url and replace original src
        $query_string = http_build_query($params);
        $custom_src = $srcinfo['scheme'] . "://" . $srcinfo['host'] . ($srcinfo['path'] ? $srcinfo['path'] : '') . ($query_string ? '?' . $query_string : '');
        $html = substr_replace ( $html , $custom_src , $src_offset, strlen($src) );
      }
    }
  }
  return $html;
}
add_filter( 'embed_oembed_html', 'wp_oembed_params_filter', 10, 4 ) ;

?>