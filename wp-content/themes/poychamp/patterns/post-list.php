<?php
/**
 * Title: Post list
 * Slug: poychamp/post-list
 * Categories: query
 * Block Types: core/query
 * Description: One post per row: image, title, date, excerpt and a read link, with pagination.
 */
?>
<!-- wp:query {"queryId":1,"query":{"inherit":true}} -->
<div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"0"}}} -->
<!-- wp:group {"className":"poychamp-post-item","layout":{"type":"default"},"style":{"spacing":{"blockGap":"var:preset|spacing|3"}}} -->
<div class="wp-block-group poychamp-post-item"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9"} /-->

<!-- wp:post-title {"level":2,"isLink":true} /-->

<!-- wp:post-date {"className":"poychamp-meta poychamp-meta--post"} /-->

<!-- wp:post-excerpt {"excerptLength":55} /-->

<!-- wp:read-more {"content":"Read post →","className":"poychamp-read-more"} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>No posts yet.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->

<!-- wp:query-pagination {"paginationArrow":"arrow","layout":{"type":"flex","justifyContent":"space-between"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|8"}}}} -->
<!-- wp:query-pagination-previous {"label":"Newer posts"} /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next {"label":"Older posts"} /-->
<!-- /wp:query-pagination --></div>
<!-- /wp:query -->
