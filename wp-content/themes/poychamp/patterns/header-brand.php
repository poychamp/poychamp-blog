<?php
/**
 * Title: Header brand
 * Slug: poychamp/header-brand
 * Inserter: no
 * Description: Logo and site title, both linking home.
 */
?>
<!-- wp:group {"className":"poychamp-brand","layout":{"type":"flex","flexWrap":"nowrap"},"style":{"spacing":{"blockGap":"var:preset|spacing|3"}}} -->
<div class="wp-block-group poychamp-brand"><!-- wp:html -->
<a class="poychamp-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo.webp' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="40" height="40" /></a>
<!-- /wp:html -->

<!-- wp:site-title {"level":0} /--></div>
<!-- /wp:group -->
