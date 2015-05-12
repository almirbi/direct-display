<?php

class Display {
    private $slides;

    public function __construct() {
        $this->slides = array();
    }

    public function slides() {

        foreach ($this->slides as $i => &$slide) {
            if ('publish' != get_post_status($slide->id())) {
                unset($this->slides[$i]);
            }
        }
        return $this->slides;
    }

    public function getSlideById($id) {
        foreach ($this->slides as $i => &$slide) {
            if ('publish' != get_post_status($slide->id()) && $id == $slide->id()) {
                return $slide;
            }
        }
        return false;
    }

    public function addSlide(Slide $slide) {
        if ('publish' == get_post_status($slide->id())) {
            $this->slides[] = $slide;
        }
        //sort($this->slides, SORT_NUMERIC);
    }

}