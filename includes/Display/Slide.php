<?php

class Slide {
    private $from;
    private $to;
    private $duration;
    private $id;
    private $position;

    public function fromHrs() {
        if ('everyday' == $this->duration['type']) {
            $tmp =explode(':',$this->duration['from-time-everyday']);
            return $tmp[0];
        }
        else {
            return 0;
        }
    }

    public function fromMin() {
        if ('everyday' == $this->duration['type']) {
            $duration = explode(':',$this->duration['to-time-everyday']);
            return substr($duration[1],0,2);
        }
        else {
            return 0;
        }
    }

    public function amountInSec() {
        if ('amount' == $this->duration['type']) {
            return $this->duration['hrs']*60*60 + $this->duration['min']*60 + $this->duration['sec'];
        }
        return $this->to() - $this->from();
    }

    public function from() {
        switch($this->duration['type']) {
            case 'exact':
                return strtotime($this->duration['from'] . ' ' . $this->duration['from-time']);
                break;
            case 'everyday':
                return strtotime('11-01-01' . ' ' . $this->duration['from-time-everyday']);
                break;
        }
        return 0;
    }
    public function to() {
        switch($this->duration['type']) {
            case 'exact':
                return strtotime($this->duration['to'] . ' ' . $this->duration['to-time']);
                break;
            case 'everyday':
                return strtotime('11-01-01' . ' ' . $this->duration['to-time-everyday']);
                break;
        }
        return 0;
    }
    public function duration() { return $this->duration; }
    public function id() { return $this->id; }
    public function position() { return $this->position; }

    public function setFrom($from) { $this->from = $from; }
    public function setTo($to) { $this->to = $to; }
    public function setDuration($duration) { $this->duration = $duration; }
    public function setPosition($position) { $this->position = $position; }
    public function setDurationInSec($sex) { $this->durationInSec = $sex; }

    public function __construct($id, $position = 0, $duration) {
        $this->id = $id;
        $this->from = "0";
        $this->to = "0";
        $this->duration = $duration;
        $this->position = $position;
    }

} 