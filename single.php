<?php
get_header();
    while ( have_posts() ) {
    the_post();
    echo apply_filters('the_content', the_content());
}
get_footer();