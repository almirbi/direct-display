<?php
require_once('Block.php');
require_once('Layout.php');


class SlidePostType {
    private $_textDomain = 'direct_display';
    private $layouts;

    //register slide post type in the constructor
    public function __construct() {
        add_action( 'init', array($this, 'registerSlidePostType'));
        add_action( 'admin_print_styles', array($this, 'dd_admin_styles') );
        add_action( 'save_post', array($this, 'dd_save_slide') );
        add_action( 'admin_enqueue_scripts', array($this, 'register_scripts') );

        $fullLayout = new Layout('full', 0, '/assets/imgs/purple_block.png');
        $rightLayout = new Layout('right', 1, '/assets/imgs/pink_right.png');
        $leftLayout = new Layout('left', 2, '/assets/imgs/pink_left.png');
        $bottomRightLayout = new Layout('bottom_right', 3, '/assets/imgs/blue_right.png');
        $bottomLeftLayout = new Layout('bottom_left', 4, '/assets/imgs/blue_left.png');


        $fullLayout->addBlock(new Block('main'));

        $rightLayout->addBlock(new Block('main'));
        $rightLayout->addBlock(new Block('side'));

        $leftLayout->addBlock(new Block('main'));
        $leftLayout->addBlock(new Block('side'));

        $bottomRightLayout->addBlock(new Block('main'));
        $bottomRightLayout->addBlock(new Block('side'));
        $bottomRightLayout->addBlock(new Block('bottom'));

        $bottomLeftLayout->addBlock(new Block('main'));
        $bottomLeftLayout->addBlock(new Block('side'));
        $bottomLeftLayout->addBlock(new Block('bottom'));


        $this->layouts = array($fullLayout, $rightLayout, $leftLayout, $bottomLeftLayout, $bottomRightLayout);
    }

    public function register_scripts() {
        /*
        <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>*/

        wp_enqueue_style( 'bootstrap.css',
                           'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css' );

        wp_enqueue_script( 'slide_metabox_custom',
                           get_template_directory_uri() . '/assets/metabox.slide.jquery.js',
                           array(),
                           '1.0.0',
                           true );
    }

    public function dd_save_slide($post) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;
        if( isset( $_POST[ 'dd_layout' ], $_POST[ 'type' ], $_POST[ 'position' ])  ) {
            $layout = $this->getLayoutById($_POST[ 'dd_layout' ]);
            $blocks = $layout->blocks();
            foreach ($blocks as &$block) {
                for ($i = 0; $i < count($_POST['position']); $i++) {
                    if ($block->position() == $_POST['position'][$i]) {
                        $block->setType($_POST['type'][$i]);
                        $block->setName($_POST['name'][$i]);
                        $block->setId($_POST['id'][$i]);
                    }
                }
            }
            update_post_meta( $post, 'dd_layout', $layout );
            update_post_meta( $post, 'slide_edited', time() );
        }
    }

    public function getLayoutById($id) {
        foreach ($this->layouts as $layout) {
            if ($id == $layout->id()) {

                return $layout;
            }
        }
    }

    public function dd_admin_styles() {
        wp_enqueue_style( 'dd_meta_box_styles', get_template_directory_uri() . '/metabox_slide.css' );
    }


    public function layouts() {
        return $this->layouts;
    }

    public function addMetaBoxes($post) {
        add_meta_box( 'dd_layout', __( 'Layout', $this->_textDomain ), array($this,'dd_layout_callback'), 'dd_slide' );
    }

    public function dd_layout_callback( $post ) {
        wp_nonce_field( basename( __FILE__ ), 'dd_nonce' );
        $stored_meta = get_post_meta( $post->ID );
        include_once('views/layout_meta_box.php');
    }

    public function registerSlidePostType() {
        $labels = array(
            'public' => true,
            'name' => __('Slides', $this->_textDomain),
            'singular_name' => __('Slide', $this->_textDomain),
            'all_items' => __('All slides', $this->_textDomain),
            'add_new_item' => __('Add new slide', $this->_textDomain),
            'edit_item' => __('Edit slide', $this->_textDomain),
            'new_item' => __('New slide', $this->_textDomain),
            'view_item' => __('View slide', $this->_textDomain),
            'search_items' => __('Search slides', $this->_textDomain),
            'not_found' => __('No slides found', $this->_textDomain),
            'not_found_in_trash' => __('No slides found in trash', $this->_textDomain),
            'parent_item_colon' => __('Parent display', $this->_textDomain),
        );

        $args = array(
            'labels' => $labels,
            'description' => __('A custom slide that can be added to a display.'),
            'show_ui' => true,
            'show_in_nav_menus' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_in_admin_bar' => true,
            'show_in_menu' => true,
            'menu_position' => 6,
            'supports' => array('title','thumbnail'),
            'register_meta_box_cb' => array($this, 'addMetaBoxes')

        );
        register_post_type( 'dd_slide', $args );
    }
}