<?php
require_once('Display.php');
require_once('Slide.php');
    class DisplayPostType {
        private $_textDomain = 'direct_display';


        //register slide post type in the constructor
        public function __construct() {

            add_action( 'init', array($this, 'registerDisplayPostType'));
            add_action( 'admin_print_styles', array($this, 'dd_admin_styles') );
            add_action( 'admin_enqueue_scripts', array($this, 'register_scripts') );
            add_action( 'save_post', array($this, 'dd_save_display') );
        }

        private function GUID()
        {
            return sprintf('%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
        }

        public function dd_save_display($post) {
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
                return;
            if( isset(  $_POST[ 'id' ],
                        $_POST[ 'active' ])  ) {

                    $display = new Display();

                    for ($i = 0; $i < count($_POST['id']); $i++) {
                        if ('1' == $_POST['active'][$i]) {
                            $duration['type'] = ('' != trim($_POST['duration-type'][$i])) ? $_POST['duration-type'][$i] : 'amount';
                            $duration['from'] = ('' != trim($_POST['from'][$i])) ? $_POST['from'][$i] : '15-02-01';
                            $duration['to'] = ('' != trim($_POST['to'][$i])) ? $_POST['to'][$i] : '15-02-24';
                            $duration['from-time'] = ('' != trim($_POST['from-time'][$i])) ? $_POST['from-time'][$i] : '05:00';
                            $duration['to-time'] = ('' != trim($_POST['to-time'][$i])) ? $_POST['to-time'][$i] : '14:10';
                            $duration['hrs'] = ('' != trim($_POST['hrs'][$i])) ? $_POST['hrs'][$i] : '0';
                            $duration['min'] = ('' != trim($_POST['min'][$i])) ? $_POST['min'][$i] : '0';
                            $duration['sec'] = ('' != trim($_POST['sec'][$i])) ? $_POST['sec'][$i] : '10';
                            $duration['from-time-everyday'] = ('' != trim($_POST['from-time-everyday'][$i])) ? $_POST['from-time-everyday'][$i] : '05:00';
                            $duration['to-time-everyday'] = ('' != trim($_POST['to-time-everyday'][$i])) ? $_POST['to-time-everyday'][$i] : '14:10';
                            $display->addSlide(new Slide($_POST['id'][$i], $i, $duration));
                        }
                    }
                update_post_meta( $post, 'dd_slides', $display );
                update_post_meta( $post, 'display_edited', time() );
            }
        }

        public function register_scripts() {
            wp_enqueue_script( 'display_metabox_custom', get_template_directory_uri() . '/assets/metabox.display.jquery.js', array(), '1.0.0', true );
        }

        public function dd_admin_styles() {
            wp_enqueue_style( 'dd_meta_box_styles_display', get_template_directory_uri() . '/metabox_display.css' );
        }

        public function addMetaBoxes($post) {
            add_meta_box( 'dd_display', __( 'Display', $this->_textDomain ), array($this,'dd_display_callback'), 'dd_display' );
        }

        public function dd_display_callback( $post ) {
            wp_nonce_field( basename( __FILE__ ), 'dd_nonce' );
            $stored_meta = get_post_meta( $post->ID );
            include_once('views/display_meta_box.php');
        }

        public function printSlide($slide = null) {
            $guid = $this->GUID();
            if ('WP_Post' == get_class($slide)) {
                $name = ("" == $slide->post_title ) ? "(" . $slide->ID . ")" : $slide->post_title;
                $id = $slide->ID;
                $duration['type'] = 'amount';
                $duration['from'] = '15-02-01';
                $duration['to'] = '15-02-26';
                $duration['from-time'] = '05:00';
                $duration['to-time'] = '14:10';
                $duration['hrs'] = '0';
                $duration['min'] = '0';
                $duration['sec'] = '10';
                $duration['from-time-everyday'] = '05:00';;
                $duration['to-time-everyday'] = '14:10';
                $active = "0";
                ?>
        <?php } else {
                $name = ("" == trim(get_the_title( $slide->id() )) ) ? "(" . $slide->id() . ")" : get_the_title( $slide->id() );
                $id = $slide->id();
                $duration = $slide->duration();
                $active = "1"; ?>
            <?php } ?>
            <li class="slide ui-state-default">
                <div>
                    <button type="button" class="remove-slide"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></button>
                    <button type="button" class="slide-control-b"><span class="glyphicon glyphicon-time" aria-hidden="true"></span></button>
                    <p class="name"><?= $name ?></p>
                    <a title="Edit slide" target="_blank" href="<?= get_site_url()?>/wp-admin/post.php?post=<?= $id ?>&action=edit"><span class="glyphicon glyphicon-edit" aria-hidden="true"></a>

                    <input type="hidden" class="id" name="id[]" value="<?= $id ?>"/>
                    <input type="hidden" class="active" name="active[]" value="<?= $active ?>"/>
                    <div class="slide-control">
                        <input type="hidden" id="duration-type" name="duration-type[]" value="<?= $duration['type'] ?>" />
                        <div class="duration">
                            <input type="radio" <?php echo ('exact' == $duration['type']) ? 'checked' : '' ?> data-target-id="exact" id="duration-<?= $guid ?>-e"id="duration-<?= $guid ?>-e" name="duration-<?= $guid ?>" value="exact" />
                            <label for="duration-<?= $guid ?>-e">Specific interval</label>
                        </div>
                        <div class="duration">
                            <input type="radio" <?php echo ('amount' == $duration['type']) ? 'checked' : '' ?> data-target-id="amount" id="duration-<?= $guid ?>-a"id="duration-<?= $guid ?>-e" name="duration-<?= $guid ?>" value="amount"/>
                            <label for="duration-<?= $guid ?>-a">Duration</label>
                        </div>
                        <div class="duration">
                            <input type="radio" <?php echo ('everyday' == $duration['type']) ? 'checked' : '' ?> data-target-id="everyday" id="duration-<?= $guid ?>-ed" name="duration-<?= $guid ?>" value="everyday"/>
                            <label for="duration-<?= $guid ?>-ed">Everyday</label>
                        </div>
                        <div class="duration-picker <?= ('exact' == $duration['type']) ? 'active' : '' ?>" id="exact">
                            <div>
                                <label class="exact-label">From:</label>
                                <input type="text" class="from" name="from[]" value="<?= isset($duration['from']) ? $duration['from'] : '' ?>"/>
                                <input class="spinner-from" name="from-time[]" value="<?= isset($duration['from-time']) ? $duration['from-time'] : '' ?>">
                            </div>
                            <div>
                                <label style="display: margin-right: 15px;" class="exact-label">To:</label>
                                <input type="text" class="to" name="to[]" value="<?= isset($duration['to']) ? $duration['to'] : '' ?>"/>
                                <input class="spinner-to" name="to-time[]" value="<?= isset($duration['to-time']) ? $duration['to-time'] : '' ?>">
                            </div>
                        </div>
                        <div class="duration-picker <?= ('amount' == $duration['type']) ? 'active' : '' ?>" id="amount">
                            <div class="labels">
                                <label>hrs</label>
                                <label class="middle">min</label>
                                <label>sec</label>
                            </div>
                            <input class="hours" name="hrs[]" value="<?= isset($duration['hrs']) ? $duration['hrs'] : '' ?>" size="3"/>
                            <input class="minutes" name="min[]" value="<?= isset($duration['min']) ? $duration['min'] : '' ?>"  size="2"/>
                            <input class="seconds" name="sec[]" value="<?= isset($duration['sec']) ? $duration['sec'] : '' ?>" size="2"/>
                        </div>
                        <div class="duration-picker <?= ('everyday' == $duration['type']) ? 'active' : '' ?>" id="everyday">
                            <div>
                                <label>From:</label>
                                <input class="spinner-from" name="from-time-everyday[]" value="<?= isset($duration['from-time-everyday']) ? $duration['from-time-everyday'] : '' ?>">
                            </div>
                            <div>
                                <label style="margin-right: 15px;">To:  </label>
                                <input class="spinner-to" name="to-time-everyday[]" value="<?= isset($duration['to-time-everyday']) ? $duration['to-time-everyday'] : '' ?>">
                            </div>
                        </div>
                    </div>
                </div>

            </li>
        <?php }

        public function registerDisplayPostType() {
            $labels = array(
                'public' => true,
                'name' => __('Displays', $this->_textDomain),
                'singular_name' => __('Display', $this->_textDomain),
                'all_items' => __('All displays', $this->_textDomain),
                'add_new_item' => __('Add new display', $this->_textDomain),
                'edit_item' => __('Edit display', $this->_textDomain),
                'new_item' => __('New display', $this->_textDomain),
                'view_item' => __('View display', $this->_textDomain),
                'search_items' => __('Search displays', $this->_textDomain),
                'not_found' => __('No displays found', $this->_textDomain),
                'not_found_in_trash' => __('No displays found in trash', $this->_textDomain)
            );

            $args = array(
                'labels' => $labels,
                'description' => __('A display consisted of slides.'),
                'show_ui' => true,
                'show_in_nav_menus' => false,
                'publicly_queryable' => true,
                'exclude_from_search' => true,
                'show_in_admin_bar' => true,
                'show_in_menu' => true,
                'menu_position' => 6,
                'supports' => array('title'),
                'public' => true,
                'register_meta_box_cb' => array($this, 'addMetaBoxes')

            );
            register_post_type( 'dd_display', $args );
        }
    }