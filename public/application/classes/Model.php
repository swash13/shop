<?php
class ModelException extends Exception {}

class Model {
    // Таблица с которой работает модель
    const TABLE = '';

    // Идентификатор объекта модели
    protected $id;

    // Список полей модели из таблицы (кроме id)
    protected static $fields = array();

    // Список полей модели из языковой таблицы (кроме id, id_lang и id_object)
    protected static $fieldsLang = array();

    // Список связей
    protected static $links = array();

    // Текущий язык
    protected $id_lang;

    // Значения полей основной таблицы
    protected $values = array();

    // Значения языковых полей
    protected $values_lang = array();

    // Связанные модели
    protected $models = array();

    protected $autoload = true;

    // Возвращает id объекта модели
    public function getId(){
        return $this->id;
    }

    // С помошью конструктора можно создать пустую модель для заполнения
    // Или загрузить существующую передав id
    public function __construct($id = null, $id_lang = null) {
        $this->id_lang = $id_lang;

        foreach (static::$fields as $field) {
            $this->values[$field] = null;
        }

        if (is_numeric($this->id_lang)) {
            if ($this->id_lang) {
                foreach (static::$fieldsLang as $field) {
                    $this->values_lang[$field] = null;
                }
            } else {
                $langs = App::getLangs();

                foreach (static::$fieldsLang as $field) {
                    $this->values_lang[$field] = array();

                    foreach ($langs as $lang) {
                        $this->values_lang[$field][$lang->getId()] = null;
                    }
                }
            }
        }

        if ($id) {
            if (is_numeric($this->id_lang) && static::hasLang()) {
                if ($this->id_lang) {
                    $row = Database::getRow("
                        SELECT `t`.*, `l`.`" . implode('`, `l`.`', static::$fieldsLang) . "`
                        FROM `" . static::TABLE . "` AS `t`
                        LEFT JOIN `" . static::TABLE . "_lang` AS `l` ON `l`.`id_object` = $id AND `l`.`id_lang` = $id_lang
                        WHERE `t`.`id` = $id
                    ");

                    $this->id = $row['id'];

                    foreach (static::$fields as $field) {
                        $this->values[$field] = $row[$field];
                    }

                    foreach (static::$fieldsLang as $field) {
                        $this->values_lang[$field] = $row[$field];
                    }
                } else {
                    $row = Database::getRow("
                        SELECT *
                        FROM `" . static::TABLE . "`
                        WHERE `id` = $id
                    ");

                    $this->id = $row['id'];

                    foreach (static::$fields as $field) {
                        $this->values[$field] = $row[$field];
                    }

                    $rows = Database::getTable("
                        SELECT `l`.*
                        FROM `" . static::TABLE . "_lang` AS `l`
                        LEFT JOIN `lang` AS `ll` ON `l`.`id_lang` = `ll`.`id`
                        WHERE `l`.`id_object` = {$this->id} AND `ll`.`active` = 1
                    ");

                    foreach ($rows as $row) {
                        $lang = $row['id_lang'];

                        foreach (static::$fieldsLang as $field) {
                            $this->values_lang[$field][$lang] = $row[$field];
                        }
                    }
                }
            } else {
                $row = Database::getRow("
                    SELECT *
                    FROM `" . static::TABLE . "`
                    WHERE `id` = $id
                ");

                $this->id = $row['id'];

                foreach (static::$fields as $field) {
                    $this->values[$field] = $row[$field];
                }
            }
        }
    }

    // Можно присвоить id пустой модели
    public function setId($id) {
        if (!$this->id) {
            $this->id = $id;
        }
    }

    // Автоматическое получение полей или связанных моделей
    public function __get($field){
        if (in_array($field, static::$fields)) {
            return $this->values[$field];
        } else if (in_array($field, static::$fieldsLang) && is_numeric($this->id_lang)) {
            return $this->values_lang[$field];
        } else if (array_key_exists($field, static::$links)) {
            if (!array_key_exists($field, $this->models) && $this->autoload) {
                $this->loadLink($field);
            }

            return isset($this->models[$field]) ? $this->models[$field] : null;
        }

        return null;
    }

    // Автоматическая запись полей
    public function __set($field, $value){
        if (in_array($field, static::$fields)) {
            $this->values[$field] = $value;
        } else if (in_array($field, static::$fieldsLang) && is_numeric($this->id_lang)) {
            if ($this->id_lang && is_scalar($value)) {
                $this->values_lang[$field] = $value;
            } else if (is_array($value)) {
                $this->values_lang[$field] = $value;
            }
        } else if (in_array($field, static::$links)) {
        } else {
            $this->{$field} = $value;
        }
    }

    public function __isset($name) {
        if (in_array($name, static::$fields)) {
            return !empty($this->values[$name]);
        } else if (in_array($name, static::$fieldsLang)) {
            return !empty($this->values_lang[$name]);
        } else if (array_key_exists($name, static::$links)) {
            return isset($this->models[$name]);
        }
    }

    // Запись модели в базу (INSERT или UPDATE)
    // $rescursive - сохранять со связями
    public function save($recursive = false) {
        if ($recursive) {
            foreach (static::$links as $name => $link) {
                if (!$link['virtual'] && ($link['type'] == LinkType::FOREIGN_KEY) && $this->models[$name]) {
                    $this->models[$name]->save(true);
                    $this->values[$link['field']] = $this->models[$name]->getId();
                }
            }
        }

        if ($this->id) {
            $this->update();
        } else {
            $this->insert();
        }

        if ($recursive) {
            foreach (static::$links as $name => $link) {
                if (!$link['virtual']) {
                    switch ($link['type']) {
                        case LinkType::PRIMARY_KEY :
                            if ($link['limit'] == 1 && $this->models[$name]) {
                                $this->models[$name]->{$link['field']} = $this->id;
                                $this->models[$name]->save(true);
                            } else {
                                foreach ($this->models[$name] as $model) {
                                    $model->{$link['field']} = $this->id;
                                    $model->save(true);
                                }
                            }
                            break;
                        case LinkType::TABLE :
                            foreach ($this->models[$name] as $model) {
                                if (!$model->getId()) {
                                    $model->save(true);
                                    Database::query("
                                        INSERT INTO `{$link['table']}`
                                        (`{$link['field1']}`, `{$link['field2']}`)
                                        VALUES ({$this->id}, " . $model->getId() . ")
                                    ");
                                } else {
                                    $model->save(true);
                                }
                            }
                            break;
                    }
                }
            }
        }
    }

    private function update(){
        $query = 'UPDATE `' . static::TABLE . '` SET ';
        foreach (static::$fields as $field) {
            $query .= "`$field` = '{$this->values[$field]}', ";
        }
        $query = rtrim($query, ', ');
        $query .= " WHERE `id` = {$this->id}";
        Database::query($query);

        if (!empty(static::$fieldsLang) && is_numeric($this->id_lang)) {
            if ($this->id_lang) {
                if (Database::getValue("SELECT `id` FROM `" . static::TABLE . "_lang` WHERE `id_lang` = {$this->id_lang} AND `id_object` = {$this->id}")) {
                    $query = 'UPDATE `' . static::TABLE . '_lang` SET ';
                    foreach (static::$fieldsLang as $field) {
                        $query .= "`$field` = '{$this->values_lang[$field]}', ";
                    }
                    $query = rtrim($query, ', ');
                    $query .= " WHERE `id_lang` = {$this->id_lang} AND `id_object` = {$this->id}";
                } else {
                    $query = "INSERT INTO `" . static::TABLE . "_lang` (`id_object`, `id_lang`, `";
                    $query .= implode(`, `, static::$fieldsLang);
                    $query .= "`) VALUES ({$this->id}, {$this->id_lang}, '";
                    $query .= implode("', '", $this->valuesLang);
                    $query .= "')";
                }
                Database::query($query);
            } else {
                foreach (App::getLangs() as $lang) {
                    $lang = $lang->getId();

                    if (Database::getValue("SELECT `id` FROM `" . static::TABLE . "_lang` WHERE `id_lang` = $lang AND `id_object` = {$this->id}")) {
                        $query = 'UPDATE `' . static::TABLE . '_lang` SET ';
                        foreach (static::$fieldsLang as $field) {
                            $query .= "`$field` = '{$this->values_lang[$field][$lang]}', ";
                        }
                        $query = rtrim($query, ', ');
                        $query .= " WHERE `id_lang` = $lang AND `id_object` = {$this->id}";
                    } else {
                        $query = "INSERT INTO `" . static::TABLE . "_lang` (`id_object`, `id_lang`, `";
                        $query .= implode(`, `, static::$fieldsLang);
                        $query .= "`) VALUES ({$this->id}, {$lang}, ";
                        foreach (static::$fieldsLang as $field) {
                            $query .= "'{$this->values_lang[$field][$lang]}', ";
                        }
                        $query = rtrim($query, ', ');
                        $query .= "')";
                    }
                    Database::query($query);
                }
            }
        }
    }

    private function insert(){
        if (in_array('add', static::$fields)) {
            $this->values['add'] = date('Y-m-d H:i:s');
        }

        $query = "INSERT INTO `" . static::TABLE . "`";
        $query .= ' (`' . implode('`, `', static::$fields) . '`) ';
        $query .= "VALUES ('" . implode("', '", $this->values) . "')";
        Database::query($query);

        $this->id = Database::getInsertId();

        if (!empty(static::$fieldsLang) && is_numeric($this->id_lang)) {
            if ($this->id_lang) {
                $query = "INSERT INTO `" . static::TABLE . "_lang` (`id_object`, `id_lang`, `";
                $query .= implode(`, `, static::$fieldsLang);
                $query .= "`) VALUES ({$this->id}, {$this->id_lang}, '";
                $query .= implode("', '", $this->valuesLang);
                $query .= "')";
                Database::query($query);
            } else {
                foreach (App::getLangs() as $lang) {
                    $lang = $lang->getId();

                    $query = "INSERT INTO `" . static::TABLE . "_lang` (`id_object`, `id_lang`, `";
                    $query .= implode('`, `', static::$fieldsLang);
                    $query .= "`) VALUES ({$this->id}, $lang, ";
                    foreach (static::$fieldsLang as $field) {
                        $query .= "'{$this->values_lang[$field][$lang]}', ";
                    }
                    $query = rtrim($query, ', ');
                    $query .= ")";
                    Database::query($query);
                }
            }
        }
    }

    // Удаление из базы
    // $recursive = true - удаление зависимых связей
    public function delete($recursive = true) {
        if (empty($this->id)) {
            return;
        }

        Database::query("
            DELETE FROM `".static::TABLE."`
            WHERE `id` = {$this->id}
        ");

        if (!empty(static::$fieldsLang)) {
            Database::query("
                DELETE FROM `" . static::TABLE . "_lang`
                WHERE `id_object` = {$this->id}
            ");
        }

        if ($recursive) {
            self::deleteLinks(get_called_class(), array($this->id));
        }
    }

    // рекурсивное удаление связанных моделей из базы
    // $ids - список id моделей для которых нужно удалить связи
    private static function deleteLinks($model, $ids) {
        foreach ($model::$links as $link) {
            if (!$link['virtual']) {
                switch ($link['type']) {
                    case LinkType::PRIMARY_KEY :
                        $linkedModel = $link['model'] . 'Model';

                        $ids = Database::getTable("
                            SELECT `id`
                            FROM `" . $linkedModel::TABLE . "`
                            WHERE `{$link['field']}` IN (" . implode(', ', $ids) . ")
                        ");

                        foreach ($ids as $index => $row) {
                            $ids[$index] = $row['id'];
                        }

                        self::deleteLinks($link['model'], $ids);

                        Database::query("
                            DELETE FROM `". $linkedModel::TABLE . "`
                            WHERE `id` IN (" . implode(', ', $ids) . ")
                        ");

                        if ($linkedModel::hasLang()) {
                            Database::query("
                                DELETE FROM `". $linkedModel::TABLE . "_lang`
                                WHERE `id_object` IN (" . implode(', ', $ids) . ")
                            ");
                        }
                        break;
                    case LinkType::TABLE :
                        $linkedModel = $link['model'] . 'Model';

                        $ids = Database::getTable("
                            SELECT `t0`.`{$link['field2']}`
                            FROM `{$link['table']}` AS `t0`
                            LEFT JOIN `{$link['table']}` AS `t1` ON `t1`.`{$link['field2']}` = `t0`.`{$link['field2']}` AND `t1`.`{$link['field1']}` NOT IN (" . implode(', ', $ids) . ")
                            WHERE `t0`.`{$link['field1']}` IN (" . implode(', ', $ids) . ") AND `t1`.`{$link['field2']}` = NULL
                            GROUP BY `t0`.`{$link['field2']}`
                        ");

                        foreach ($ids as $index => $row) {
                            $ids[$index] = $row[$link['field2']];
                        }

                        self::deleteLinks($link['Model'], $ids);

                        Database::query("
                            DELETE FROM `{$link['table']}`
                            WHERE `{$link['field2']}` IN (" . implode(', ', $ids) . ")
                        ");

                        Database::query("
                            DELETE FROM `" . $linkedModel::TABLE . "`
                            WHERE `id` IN (" . implode(', ', $ids) . ")
                        ");

                        if ($linkedModel::hasLang()) {
                            Database::query("
                                DELETE FROM `". $linkedModel::TABLE . "_lang`
                                WHERE `id_object` IN (" . implode(', ', $ids) . ")
                            ");
                        }
                        break;
                }
            }
        }
    }

    // Подгрузка связанных моделей
    // Выполняется автоматически при первом обращении к связи
    private function loadLink($link_name) {
        if (isset(static::$links[$link_name]) && $this->id) {
            $link = static::$links[$link_name];

            switch ($link['type']) {
                // Когда модель связана со списком объектов другой модели
                case LinkType::PRIMARY_KEY :
                    $collection = new Collection($link['model'], $this->id_lang);
                    if (isset($link['order'])) {
                        $collection->order($link['order']);
                    }
                    $where = "`{$link['field']}` = {$this->id}";
                    if (isset($link['where'])) {
                        $where .= " AND {$link['where']}";
                    }
                    $collection->where($where);
                    if (isset($link['limit'])) {
                        $collection->limit($link['limit']);
                    }

                    $models = $collection->items();

                    if (isset($link['limit']) && $link['limit'] == 1) {
                        if (!empty($models)) {
                            $this->models[$link_name] = $models[0];
                        } else {
                            $this->models[$link_name] = null;
                        }
                    } else {
                        $this->models[$link_name] = $models;
                    }
                    break;

                // Когда модель связана с одним объектом другой модели
                case LinkType::FOREIGN_KEY :
                    $collection = new Collection($link['model'], $this->id_lang);
                    $where = "`id` = {$this->{$link['field']}}";
                    if (!empty($link['where'])) {
                        $where .= " AND {$link['where']}";
                    }
                    $collection->where($where);

                    $models = $collection->items();
                    if (!empty($models)) {
                        $this->models[$link_name] = $models[0];
                    } else {
                        $this->models[$link_name] = null;
                    }
                    break;

                // Связь многие ко многим через промежуточную таблицу
                case LinkType::TABLE :
                    $ids = Database::getTable("
                        SELECT `{$link['field2']}`
                        FROM `{$link['table']}`
                        WHERE `{$link['field1']}` = {$this->id}
                    ");

                    foreach ($ids as $index => $row) {
                        $ids[$index] = $row[$link['field2']];
                    }

                    $collection = new Collection($link['model'], $this->id_lang);
                    if ($link['order']) {
                        $collection->order($link['order']);
                    }

                    $where = "`id` IN (" . implode(', ', $ids) . ")";
                    if ($link['where']) {
                        $where .= " AND {$link['where']}";
                    }
                    $collection->where($where);
                    if ($link['limit']) {
                        $collection->limit($link['limit']);
                    }

                    $models = $collection->items();
                    if ($link['limit'] == 1) {
                        if (!empty($models)) {
                            $this->models[$link_name] = $models[0];
                        } else {
                            $this->models[$link_name] = null;
                        }
                    } else {
                        $this->models[$link_name] = $models;
                    }
                    break;
            }
        }
    }

    // Привязка другой модели
    public function link($link, $model) {
        if (static::$links[$link]) {
            if (is_a($model, static::$links[$link]['model'] . 'Model')) {
                if (static::$links[$link]['type'] == LinkType::FOREIGN_KEY || static::$links[$link]['limit'] == 1) {
                    $this->models[$link] = $model;
                } else if (!in_array($model, $this->models[$link])) {
                    $this->models[$link][] = $model;
                }
            } else {
                throw new ModelException("Can not link '" . get_class($model) . "' to '" . get_called_class() . "' in link '$link'.");
            }
        } else {
            throw new ModelException("Model '" . get_called_class() . "' not have link '$link'");
        }
    }

    public function setLink($link, $models) {
        if (array_key_exists($link, static::$links)) {
            $this->models[$link] = $models;
        }
    }

    public function setAutoload($autoload, $recursive = false) {
        $this->autoload = $autoload;

        if ($recursive) {
            foreach ($this->models as $models) {
                if (is_array($models)) {
                    foreach ($models as $model) {
                        $model->setAutoload($autoload, true);
                    }
                } else if ($models) {
                    $models->setAutoload($autoload, true);
                }
            }
        }
    }

    public static function setModelsAutoload($models, $autoload, $recursive = false) {
        if (is_array($models)) {
            foreach ($models as $model) {
                $model->setAutoload($autoload, $recursive);
            }
        } else if ($models) {
            $models->setAutoload($autoload, $recursive);
        }

        return $models;
    }

    public static function getFields() {
        return static::$fields;
    }

    public static function getFieldsLang() {
        return static::$fieldsLang;
    }

    public static function hasLang() {
        return !empty(static::$fieldsLang);
    }

    public static function getLinks() {
        return static::$links;
    }
}