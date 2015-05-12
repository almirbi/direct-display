<?php

    $isAjax = false;
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
    {
        $isAjax = true;
        $started = intval($_POST['display-started']);
    }
    if (!$isAjax)
    {
        get_header();
        echo '<div id="active-slide"></div>';
    }

while ( have_posts() ) {
	the_post();

    $display = get_post_meta(get_the_ID(), 'dd_slides', true);
    $slides = $display->slides();
    $isModified = false;
    foreach ($slides as $slide) {
        if (intval(get_post_meta($slide->id(), 'slide_edited', true)) > $started) {
            $isModified = true;
        }
    }
    if (intval(get_post_meta(get_the_ID(), 'display_edited', true)) > $started) $isModified = true;

    if (!$isModified && $isAjax) { echo json_encode(array('status' => 'not_modified', 'time' => time())); die(); }
    ?>
        <div data-display-started="<?= time() ?>"
             data-permalink="<?= get_post_permalink() ?>"
             class="display" id="display">
        <?php

        foreach ($slides as $slide) { ?>
            <?php $slideMeta = get_post_meta($slide->id());
            $layout = isset ( $slideMeta['dd_layout'] ) ? unserialize($slideMeta['dd_layout'][0]) : "0";
            $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($slide->id()), 'full');
            $duration = $slide->duration();

            $background = (is_bool($large_image_url)) ? "" : "background-image: url('" . $large_image_url[0] . "');";
            ?>
            <div style="width: 100%; <?= $background ?>" class="slide <?= $duration['type'] ?>"
                 data-duration-type="<?= $duration['type'] ?>"
                 data-duration-from="<?= $slide->from() ?>"
                 data-duration-amount="<?= $slide->amountInSec() ?>"
                 data-from-hrs="<?= $slide->fromHrs() ?>"
                 data-from-min="<?= $slide->fromMin() ?>"
                 data-position="<?= $slide->position() ?>"
                 data-slide-id="<?php $slide->id() ?>"
                 data-layout-id="<?php $layout->id() ?>">
            <?php switch ($layout->id()) {
                case '0': ?>
                    <div class="block main i<?= $layout->id() ?>">
                        <?php
                            $block = $layout->getBlockByPosition('main');
                            echo $block->shortcode();
                        ?>
                    </div>
                    <?php break;
                case '1': ?>
                    <div class="block main i<?= $layout->id() ?>">
                        <?php
                            $block = $layout->getBlockByPosition('main');
                            echo $block->shortcode();
                        ?>
                    </div>
                    <div class="block side i<?= $layout->id() ?>">
                        <?php
                            $block = $layout->getBlockByPosition('side');
                            echo $block->shortcode();
                        ?>
                    </div>
                    <?php break;
                case '2': ?>
                    <div class="block side i<?= $layout->id() ?>">
                        <?php
                            $block = $layout->getBlockByPosition('side');
                            echo $block->shortcode();
                        ?>
                    </div>
                    <div class="block main i<?= $layout->id() ?>">
                        <?php
                            $block = $layout->getBlockByPosition('main');
                            echo $block->shortcode();
                        ?>
                    </div>
                    <?php break;
                case '3': ?>
                    <div class="block main i<?= $layout->id() ?>">
                        <?php
                            $block = $layout->getBlockByPosition('main');
                            echo $block->shortcode();
                        ?>
                    </div>
                    <div class="block side i<?= $layout->id() ?>">
                        <?php
                            $block = $layout->getBlockByPosition('side');
                            echo $block->shortcode();
                        ?>
                    </div>
                    <div class="block bottom i<?= $layout->id() ?>">
                        <?php
                            $block = $layout->getBlockByPosition('bottom');
                            echo $block->shortcode();
                        ?>
                    </div>
                    <?php break;
                case '4': ?>
                    <div class="block side i<?= $layout->id() ?>">
                        <?php
                            $block = $layout->getBlockByPosition('side');
                            echo $block->shortcode();
                        ?>
                    </div>
                    <div class="block main i<?= $layout->id() ?>">
                        <?php
                            $block = $layout->getBlockByPosition('main');
                            echo $block->shortcode();
                        ?>
                    </div>
                    <div class="block bottom i<?= $layout->id() ?>">
                        <?php
                            $block = $layout->getBlockByPosition('bottom');
                            echo $block->shortcode();
                        ?>
                    </div>
                    <?php break;

            }
            ?>
            </div>
        <?php } ?>
    <?php
    } ?>
</div>
<div id="loader"></div>
<?php if (!$isAjax) get_footer(); ?>
