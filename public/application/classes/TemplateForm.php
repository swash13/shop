<?php

// Шаблон формы в админке
class TemplateForm extends TemplatePage {
    public $classes;

    public function __construct() {
        parent::__construct('chunks/admin-form');
        $this->vars['fields'] = array();
    }

    public function addTextField($name, $title, $value = '', $error = '', $langs = false) {
        $this->vars['fields'][$name] = array(
            'type' => 'text',
            'title' => $title,
            'langs' => $langs,
            'value' => $value,
            'error' => $error
        );
    }

    public function addTextareaField($name, $title, $value = '', $error = '', $editor = false, $langs = false) {
        $this->vars['fields'][$name] = array(
            'type' => 'textarea',
            'title' => $title,
            'editor' => $editor,
            'langs' => $langs,
            'value' => $value,
            'error' => $error
        );
    }

    public function addSelectField($name, $title, $options, $value = '', $error = '', $empty='') {
        $this->vars['fields'][$name] = array(
            'type' => 'select',
            'title' => $title,
            'options' => $options,
            'empty' => $empty,
            'value' => $value,
            'error' => $error
        );
    }

    public function addCheckboxField($name, $title, $checked = false) {
        $this->vars['fields'][$name] = array(
            'type' => 'checkbox',
            'title' => $title,
            'checked' => $checked
        );
    }

    public function addUploaderField($name, $title, $url, $file_types = '', $file_description = '', $size_limit = '10MB', $post_params = null, $value = null, $error = '') {
        $this->vars['fields'][$name] = array(
            'type' => 'uploader',
            'title' => $title,
            'url' => $url,
            'file_types' => $file_types,
            'file_description' => $file_description,
            'size_limit' => $size_limit,
            'post_params' => $post_params,
            'value' => $value,
            'error' => $error
        );
    }
}