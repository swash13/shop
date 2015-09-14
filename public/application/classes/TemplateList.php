<?php

class TemplateList extends TemplatePage {
    public $itemActions = array();
    public $actions = array();
    public $fields = array();
    public $items = array();
    public $classes;
    public $attributes;

    public function __construct() {
        parent::__construct('chunks/admin-list');
    }
}