<?php

    require_once('Block.php');

    class Layout {
        private $name;
        private $blocks;
        private $id;
        private $img;

        public function img() {
            return $this->img;
        }

        public function __construct($name, $id, $img = '') {
            $this->name = $name;
            $this->id = $id;
            $this->img = get_template_directory_uri() . $img;
        }

        public function addBlock(Block $block) {
            $this->blocks[] = $block;
        }

        public function blocks() {
            return $this->blocks;
        }

        public function id() {
            return $this->id;
        }

        public function name() {
            return $this->name;
        }

        public function getBlockByPosition($position) {
            foreach ($this->blocks as $block) {
                if ($position == $block->position()) {
                    return $block;
                }
            }
            return false;
        }
    }