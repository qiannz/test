<?php

// TODO
interface Core_Queue {
    public function push();
    public function pop();
    public function get();
    public function getAll();
}