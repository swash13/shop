<?php

class LangModel extends Model {
    const TABLE = 'lang';

    protected static $fields = array('code', 'active');
    protected static $fieldsLang = array('name');
}