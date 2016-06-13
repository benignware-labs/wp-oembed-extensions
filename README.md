wp-oembed-extensions
====================

> Wordpress OEmbed Extension Pack (Beta)

* OEmbed Widget
* Responsive iframes
* Custom Embed Params



## OEmbed Widget

The OEmbed Widget lets you add an OEmbed-URL to your sidebars.
It takes an option `oembed` which is passed as attributes to the `oembed_html`-filter.
Adds a provider-based css class for custom styling.

## OEmbed Responsive

Makes any iframe embedded via OEmbed responsive.

## OEmbed Params

This hook lets theme developers pass url-params to the generated iframe-src with certain providers.

#### Example: Don't show info with youtube-videos in OEmbed widgets

```php
function my_dynamic_sidebar_params( $sidebar_params ) {
  if ( is_admin() ) {
    return $sidebar_params;
  }
  foreach($sidebar_params as $index => $widget_params) {
    $widget_name = isset($widget_params['widget_name']) ? sanitize_title($widget_params['widget_name']) : '';
    if ($widget_name === 'oembed') {
      $sidebar_params[$index] = array_merge_recursive( 
        $widget_params,
        array(
          'oembed' => array(
            'params' => array(
              'youtube' => array(
                'showinfo' => 0
              )
            )
          )
        )
      );
    } 
  }
}

add_filter( 'dynamic_sidebar_params', 'my_dynamic_sidebar_params' );
```