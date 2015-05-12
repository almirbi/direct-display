<?php try { ?>

<?php
    require_once(__DIR__.'/../Display.php');
    require_once(__DIR__.'/../Slide.php');
    $display = isset ( $stored_meta['dd_slides'] ) ? unserialize($stored_meta['dd_slides'][0]) : "0";

    if ("0" == $display) {
        $display = new Display();
    }
?>

<div>
    <div class="slides-display">
        <h4 class="label">Slides in this display<b> (drag to reorder)</b>:</h4>
        <ul>
            <?php foreach ($display->slides() as $slide) { ?>
                <?php $this->printSlide($slide); ?>
            <?php } ?>
        </ul>

    </div>
    <div class="slides-available">
        <h4 class="label">Drag and drop a slide to the left <br>box in order to add it to this display.</h4>
        <h4 class="label">You can create more slides <a href="<?= get_site_url() . "/wp-admin/edit.php?post_type=dd_slide" ?>">here</a>.</h4>
        <input type="text" placeholder="search slides" value="" id="search-slides" />

        <a target="_blank" href="<?= get_site_url() . "/wp-admin/post-new.php?post_type=dd_slide" ?>">
            <li class="slide ui-state-default ui-draggable ui-draggable-handle add">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            </li>
        </a>

        <?php
            $slides = get_posts( array( 'post_type' => 'dd_slide', 'posts_per_page' => -1 ) );
            if (count($slides) > 0) {
                foreach ($slides as $slide) {
                    if ('publish' == get_post_status($slide->ID)) {
                        $this->printSlide($slide);
                    }
                }
            } else { ?>
                <p>No social widgets found. Please click
                    <a href="<?= get_site_url() ?>/wp-admin/options-general.php?page=social-stream">here</a>
                    to create one.</p>
            <?php } ?>
    </div>
    <div class="clearfix"></div>
</div>

<?php } catch(Exception $e) {
    var_dump($e);
}?>