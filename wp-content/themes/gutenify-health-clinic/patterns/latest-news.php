
<?php
 /**
  * Title: Latest News
  * Slug: gutenify-health-clinic/latest-news
  * Categories: gutenify-starter
  */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","right":"var:preset|spacing|40","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40"},"blockGap":"0px"}},"backgroundColor":"background-secondary","layout":{"inherit":true,"type":"constrained"}} -->
<div class="wp-block-group alignfull has-background-secondary-background-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"1px","padding":{"bottom":"var:preset|spacing|30"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide" style="padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:group {"style":{"spacing":{"blockGap":"10px"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"align":"center","textColor":"primary"} -->
<p class="has-text-align-center has-primary-color has-text-color"><strong><?php echo esc_html__( 'From Our Blog Posts', 'gutenify-health-clinic' ); ?></strong></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"textColor":"foreground","fontSize":"slider-title"} -->
<h2 class="wp-block-heading has-text-align-center has-foreground-color has-text-color has-slider-title-font-size" style="font-style:normal;font-weight:700"><?php echo esc_html__( 'All the Latest Agency Stories', 'gutenify-health-clinic' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"body-text"} -->
<p class="has-text-align-center has-body-text-color has-text-color"><?php echo esc_html__( 'are many variations of passages available but the majority have', 'gutenify-health-clinic' ); ?> </p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","textColor":"body-text"} -->
<p class="has-text-align-center has-body-text-color has-text-color"><?php echo esc_html__( 'alteration in some form, by injected', 'gutenify-health-clinic' ); ?> </p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"metadata":{"name":"Latest News"},"align":"wide","style":{"spacing":{"padding":{"bottom":"0px","top":"0"}}},"layout":{"inherit":true,"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:0;padding-bottom:0px"><!-- wp:query {"queryId":1,"query":{"perPage":"3","pages":"3","offset":"","postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"tagName":"main","align":"wide","layout":{"type":"default"}} -->
<main class="wp-block-query alignwide"><!-- wp:post-template {"align":"wide","layout":{"type":"grid","columnCount":3}} -->
<!-- wp:group {"style":{"spacing":{"padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"}}},"backgroundColor":"background"} -->
<div class="wp-block-group has-background-background-color has-background" style="padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px"><!-- wp:cover {"useFeaturedImage":true,"dimRatio":0,"customOverlayColor":"#988975","isUserOverlayColor":true,"minHeight":273,"minHeightUnit":"px","isDark":false} -->
<div class="wp-block-cover is-light" style="min-height:273px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#988975"></span><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size"></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:cover -->

<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"18px","padding":{"top":"30px","right":"30px","bottom":"30px","left":"30px"}}},"className":"has-no-underline "} -->
<div class="wp-block-group alignwide has-no-underline" style="padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px"><!-- wp:post-title {"level":3,"isLink":true,"align":"wide","style":{"typography":{"fontStyle":"normal","fontWeight":"600","textTransform":"capitalize"}}} /-->

<!-- wp:post-date {"format":"F j, Y","isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"300"}},"fontSize":"small"} /-->

<!-- wp:post-excerpt {"moreText":"Know More","style":{"elements":{"link":{"color":{"text":"var:preset|color|primary"}}}}} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template --></main>
<!-- /wp:query -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"><?php echo esc_html__( 'View More', 'gutenify-health-clinic' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->