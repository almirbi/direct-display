<?php try { ?>
<?php
    require_once(__DIR__.'/../Block.php');
    require_once(__DIR__.'/../Layout.php');

    $layout = isset ( $stored_meta['dd_layout'] ) ? unserialize($stored_meta['dd_layout'][0]) : "0";

    if ("0" == $layout) {
        $layout = $this->getLayoutById(3);
    }

    $blocks = $layout->blocks();
    if (is_bool($layout->getBlockByPosition('side'))) {
        $blocks[] = new Block('side');
    }
    if (is_bool($layout->getBlockByPosition('bottom'))) {
        $blocks[] = new Block('bottom');
    }

?>

<div>


    <div class="slide-layout">
        <p class="label">Choose layout:</p>
        <input type="hidden" id="dd_layout" value="<?= $layout->id() ?>" name="dd_layout"/>

        <?php foreach($this->layouts() as $blank_layout) { ?>
            <div data-id="<?= $blank_layout->id() ?>" class="layout-box <?= ($blank_layout->id() == $layout->id()) ? 'active' : '' ?>" id="<?= $blank_layout->name() ?>">
                <div class="layout-icon">
                </div>
            </div>
        <?php } ?>

    </div>

    <div class="blocks">

        <?php foreach ($blocks as $block) { ?>
            <div <?= ('0' != $block->name()) ? 'title="'.$block->name().'"' : 'title="Drag a widget here"' ?> class="block-container <?= $block->position() ?>" style="display: inline-block">
                <p><?= strtoupper($block->position()) ?></p>
                <div class="block <?= $block->position() ?>">
                    <input type="hidden" name="type[]" value="<?= $block->type() ?>" />
                    <input type="hidden" name="position[]" value="<?= $block->position() ?>" />
                    <input type="hidden" name="name[]" value="<?= $block->name() ?>" />
                    <input type="hidden" name="id[]" value="<?= $block->id() ?>" />
                    <button type="button" class="clear-block">Clear Block</button>
                    <?php if ("0" != $block->type()) { ?>
                        <div data-type="<?= $block->type() ?>" class="widget <?= $block->type() ?>">
                            <p class="block-name"><?= (strlen($block->name()) > 7) ? substr($block->name(), 0, 4) . '...' : $block->name() ?></p>
                            <p class="block-id">ID: <?= $block->id() ?></p>
                        </div>
                    <?php } ?>

                </div>
            </div>

        <?php } ?>
        <div class="message">
            <p>Drag and drop the available widget into main, side or bottom block.</p>
        </div>
    </div>

    <div class="available-widgets">
        <h2 class="label">Available widgets:</h2>

        <div style="display: inline-block!important; min-width: 100px!important;" class="weather-widgets widgets-container">
            <p class="label">Weather</p>
            <div data-id="1" data-name="Weather" data-type="weather" class="widget weather ui-draggable ui-draggable-handle">
                <p class="block-name">Weather</p>
                <p class="block-id">ID: 1</p>
            </div>
        </div>

        <div style="display: inline-block!important; min-width: 100px!important;" class="clock-widgets widgets-container">
            <p class="label">Clock</p>
            <div data-id="1" data-name="clock" data-type="clock" class="widget clock">
                <p class="block-name">Clock</p>
                <p class="block-id">ID: 1</p>
            </div>
        </div>


        <div class="social-widgets widgets-container">
            <p class="label">Social stream</p>
            <?php $query = new WP_Query( array( 'post_type' => 'dc_streams' ) );
                if ( $query->have_posts() )
                {
                    while ( $query->have_posts() ) : $query->the_post();
                        parse_str(trim(get_the_content(), '"'), $output);
                        ?>
                        <div data-id="<?= get_the_ID() ?>" data-name="<?= $output['name'] . ' feed' ?>" data-type="social" class="widget social">
                            <p class="block-name"><?= ( $output['name'] == '' ) ? 'n/a' : $output['name'] ?></p>
                            <p class="block-id">ID: <?= get_the_ID() ?></p><a class="widget-edit" title="Edit" href="<?= get_site_url() ?>/wp-admin/options-general.php?page=social-stream&stream=<?= get_the_ID() ?>#dcwss_streams"><span class="glyphicon glyphicon-edit"></span></a>
                        </div>
                    <?php endwhile; ?>

                    <?php wp_reset_postdata(); ?>

                <?php } else { ?>
                    <p>No social widgets found. Please click
                        <a href="<?= get_site_url() ?>/wp-admin/options-general.php?page=social-stream">here</a>
                        to create one.</p>
                <?php } ?>
            <a target="_blank" href="<?= get_site_url() ?>/wp-admin/options-general.php?page=social-stream">
                <div class="widget text add">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                </div>
            </a>
            <div class="clearfix"></div>
        </div>




        <div class="text-widgets widgets-container">
            <p class="label">Text</p>
            <?php $query = new WP_Query( array( 'post_type' => 'post' ) );
                if ( $query->have_posts() )
                {
                    while ( $query->have_posts() ) : $query->the_post();
                        ?>
                        <div data-id="<?= get_the_ID() ?>" data-name="<?= get_the_title() ?>" data-type="text" class="widget text">
                            <p class="block-name"><?= get_the_title() ?></p>
                            <p class="block-id">ID: <?= get_the_ID() ?></p><a class="widget-edit" title="Edit" href="<?= get_site_url() ?>/wp-admin/post.php?post=<?= get_the_ID() ?>&action=edit"><span class="glyphicon glyphicon-edit"></span></a>
                        </div>
                    <?php endwhile; ?>

                    <?php wp_reset_postdata();?>

                <?php
                } else { ?>
                    <p>No text widgets found. Please click
                        <a href="<?= get_site_url() ?>/wp-admin/edit.php">here</a>
                        to create one.</p>
                <?php } ?>
            <a target="_blank" href="<?= get_site_url() ?>/wp-admin/edit.php">
                <div class="widget text add">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                </div>
            </a>

        </div>
        <div class="clearfix"></div>
    </div>

</div>
    <div class="clearfix"></div>
<?php } catch(Exception $e) {
    var_dump($e);
}?>