<?php

class Block {
    private $position;
    private $type;
    private $id;
    private $name;
    private $options;

    public function __construct($position) {
        $this->position = $position;
        $this->shortcode = "0";
        $this->type = "0";
        $this->id = "0";
        $this->name = "0";
    }

    private function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function position() { return $this->position; }
    public function shortcode() {
        switch($this->type) {
            case 'weather':
                try {
                    $geoip = wp_remote_post( 'http://ip-api.com/json/' . $this->getRealIpAddr());
                    $location = json_decode($geoip['body'], true);
                } catch(Exception $e) {}

                //var_dump($location);
                //echo do_shortcode('[icit_weather city="'.$location['city'].'" country="'.$location['countryCode'].'" celsius="true" breakdown="false" display="extended" background_day="red" background_night="rgb(129,160,255)"]');
                echo do_shortcode('[icit_weather city="Oslo" country="NO" celsius="true" breakdown="false" display="extended" background_day="red" background_night="rgb(129,160,255)"]');
                break;
            case 'social':
                if (strpos($this->position,'side') !== false) {
                    echo do_shortcode('[dc_social_feed id="'.$this->id.'"]');
                } else {
                    echo do_shortcode('[dc_social_wall id="'.$this->id.'"]');
                }
                break;
            case 'text':
                ?>
                <div class="text">

                    <div class="content"><?= get_post_field('post_content', $this->id) ?></div>
                </div>
                <?php
                break;
            case 'clock': ?>
                <div class="clock">
                    <div class="Date"></div>
                    <ul>
                        <li class="hours"></li>
                        <li class="point">:</li>
                        <li class="min"></li>
                        <li class="point">:</li>
                        <li class="sec"></li>
                    </ul>
                </div>
                <?php //echo '<div style="margin: 0 auto; text-align: center; width: 472px;"><noscript><div style="display: inline-block; padding: 2px 4px; margin: 0px 0px 5px; border: 1px solid rgb(204, 204, 204); text-align: center; background-color: rgb(255, 255, 255);"><a href="" style="text-decoration: none; font-size: 13px; color: rgb(0, 0, 0);"> </a></div></noscript><script type="text/javascript" src="http://localtimes.info/clock.php?&cp1_Hex=000000&cp2_Hex=FFFFFF&cp3_Hex=000000&fwdt=472&ham=0&hbg=0&hfg=0&sid=0&mon=0&wek=0&wkf=0&sep=0&widget_number=1000"></script></div>';
                break;
        }
    }
    public function type() { return $this->type; }
    public function id() { return $this->id; }
    public function name() { return $this->name; }

    public function setType($type) { $this->type = $type; }
    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
}