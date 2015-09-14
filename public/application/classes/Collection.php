<?php

class CollectionException extends Exception {}

class Collection {
    private $graph = array(
        'model' => null,
        'alias' => 't0',
        'links' => array(),
    );
    private $condition = null;
    private $order = null;
    private $limit = null;

    private $id_lang;
    private $items = null;

    public function __construct($model, $id_lang = 0) {
        $this->graph['model'] = $model . 'Model';
        $this->graph['links'] = array();

        $this->last_alias = 0;
        $this->id_lang = $id_lang;
    }

    public function order($order) {
        $this->order = $order;
    }

    public function where($condition) {
        $this->condition = $condition;
    }

    public function limit($count, $offset = 0) {
        $this->limit = "$offset, $count";
    }

    public function link($link, $condition = null, $order = null) {
        $links = explode('->', $link);
        $last = array_pop($links);

        $graph = &$this->graph;
        foreach ($links as $link) {
            $this::addLink($graph, $link);

            $graph = &$graph['links'][$link];
        }

        $this->addLink($graph, $last, $condition, $order);
    }

    public function items() {
        if (!is_array($this->items)) {
            $query = $this->buildQuery();
            $rows = Database::getTable($query);
            $items = array();
            $firstAlias = substr($this->graph['model'], 0, -5);

            foreach ($rows as $row) {
                $this->parseRow($row, $items, $firstAlias, $this->graph);
            }

            $this->items = isset($items[$firstAlias]) ? array_values($items[$firstAlias]) : array();
        }

        return $this->items;
    }

    private function addLink(&$graph, $link, $condition = null, $order = null) {
        $model = $graph['model'];
        $modelLinks = $model::getLinks();

        if (!array_key_exists($link, $graph['links'])) {
            if (!array_key_exists($link, $modelLinks)) {
                throw new CollectionException("Model $model not have link $link");
            }

            $graph['links'][$link] = array(
                'model' => $modelLinks[$link]['model'] . 'Model',
                'links' => array(),
                'condition' => null,
                'order' => null
            );
        }

        if (!empty($modelLinks[$link]['virtual']) && !empty($modelLinks[$link]['condition'])) {
            $condition = $modelLinks[$link]['condition'];
        }

        if ($condition) {
            $graph['links'][$link]['condition'] = $condition;
        }

        if ($order) {
            $graph['links'][$link]['order'] = $order;
        }

        if (empty($graph['links'][$link]['condition']) && !empty($modelLinks[$link]['condition'])) {
            $graph['links'][$link]['condition'] = $modelLinks[$link]['condition'];
        }

        if (empty($graph['links'][$link]['order']) && !empty($modelLinks[$link]['order'])) {
            $graph['links'][$link]['order'] = $modelLinks[$link]['order'];
        }
    }

    private function joinLink($alias, $link, $linkAlias, $linkCondition = null) {
        $model = $link['model'] . 'Model';

        $join = '';

        switch ($link['type']) {
            case LinkType::PRIMARY_KEY :
                $join = " LEFT JOIN `" . $model::TABLE . "` AS `$linkAlias` ON `$linkAlias`.`{$link['field']}` = `$alias`.`id`" . ($linkCondition ? preg_replace('`([^`]+)`', "`$linkAlias`.`$1`", $linkCondition) : '');
                break;
            case LinkType::FOREIGN_KEY :
                $join = " LEFT JOIN `" . $model::TABLE . "` AS `$linkAlias` ON `$linkAlias`.`id` = `$alias`.`{$link['field']}`" . ($linkCondition ? preg_replace('`([^`]+)`', "`$linkAlias`.`$1`", $linkCondition) : '');
                break;
            case LinkType::TABLE :
                $join = " LEFT JOIN `{$link['table']}` AS `{$linkAlias}_x` ON `{$linkAlias}_x`.`{$link['fields1']}` = `$alias`.`id`";
                $join .= " LEFT JOIN `" . $model::TABLE . "` AS `$linkAlias` ON `$linkAlias`.`id` = `{$linkAlias}_x`.`{$link['field2']}`" . ($linkCondition ? preg_replace('`([^`]+)`', "`$linkAlias`.`$1`", $linkCondition) : '');
                break;
        }

        return $join;
    }

    private function parseGraph($graph, &$selects, &$joins, &$orders, $alias) {
        $model = $graph['model'];

        $selects[$alias] =  "`$alias`.`id` AS `{$alias}->id`, " . implode(', ', preg_replace('/.+/', "`$alias`.`$0` AS `{$alias}->$0`", $model::getFields()));

        if ($this->id_lang && $model::hasLang()) {
            $selects[$alias . '_lang'] = implode(', ', preg_replace('/.+/', "`{$alias}_lang`.`$0` AS `{$alias}_lang->$0`", $model::getFieldsLang()));
            $joins[$alias . '_lang'] = "LEFT JOIN `" . $model::TABLE . "_lang` AS `{$alias}_lang` ON `{$alias}_lang`.`id_object` = `{$alias}`.`id` AND `{$alias}_lang`.`id_lang` = {$this->id_lang}";
        }

        if (!empty($graph['order'])) {
            $fields = $model::getFields();
            $fieldsLang = $model::getFieldsLang();

            $fieldsOrder = explode(',', $graph['order']);
            foreach ($fieldsOrder as $fieldOrder) {
                preg_match('`([^`]+)`', $fieldOrder, $match);
                $field = $match[1];

                if (in_array($field, array_merge(array('id'), $fields))) {
                    $orders["`$alias`.`$field`"] = str_replace("`$field`", "`$alias`.`$field`", $fieldOrder);
                } else if (in_array($field, $fieldsLang)) {
                    if ($this->id_lang) {
                        $orders["`{$alias}_lang`.`$field`"] = str_replace("`$field`", "`{$alias}_lang`.`$field`", $fieldOrder);
                    } else {
                        throw new CollectionException("Can not use lang field '$match' in ORDER without specific language");
                    }
                } else {
                    throw new CollectionException("Can not use field '$match'. Field not exists in model.");
                }
            }
        }

        $modelLinks = $model::getLinks();
        foreach ($graph['links'] as $name => $link) {
            $joins[$alias . "->$name"] = $this->joinLink($alias, $modelLinks[$name], $alias . "->$name", $link['condition']);
            $this->parseGraph($link, $selects, $joins, $orders, $alias . "->$name");
        }
    }

    private function parseMainCondition(&$joins) {
        $condition = $this->condition;
        preg_match_all('/`([^`]+)`/', $this->condition, $matches);

        foreach (array_unique($matches[1]) as $match) {
            $links = explode('->', $match);
            $field = array_pop($links);

            $model = $this->graph['model'];
            $modelAlias = 0;
            $alias = substr($this->graph['model'], 0, -5);

            if (!empty($links)) {
                foreach ($links as $link) {
                    $modelLinks = $model::getLinks();

                    if (!$joins[$alias . "->$link"]) {
                        $joins[$alias . "->$link"] = $this->joinLink($alias, $modelLinks[$link], $alias . "->$link");
                    }

                    $alias .= "->$link";
                    $model = $modelLinks[$link]['model'] . 'Model';
                }
            }

            if (in_array($field, array_merge(array('id'), $model::getFields()))) {
                $condition = str_replace("`$match`", "`$alias`.`$field`", $condition);
            } else if (in_array($field, $model::getFieldsLang())) {
                if ($this->id_lang) {
                    if (!$joins[$alias . '_lang']) {
                        $joins[$alias . '_lang'] = $this->joinLang($model, $alias);
                    }

                    $condition = str_replace("`$match`", "`{$alias}_lang`.`$field`", $condition);
                } else {
                    throw new CollectionException("Can not use lang field '$match' in WHERE condition without specific language");
                }
            } else {
                throw new CollectionException("Can not use field '$match'. Field not exists in model.");
            }
        }

        return $condition;
    }

    private function parseMainOrder(&$joins) {
        $orders = array();
        $fieldsOrder = explode(',', $this->order);
        foreach ($fieldsOrder as $fieldOrder) {
            preg_match('/`([^`]+)`/', $fieldOrder, $match);

            $match = $match[1];
            $links = explode('->', $match);
            $field = array_pop($links);

            $model = $this->graph['model'];
            $alias = substr($model, 0, -5);

            if (!empty($link)) {
                foreach ($links as $link) {
                    $modelLinks = $model::getLinks();

                    if (!$joins[$alias . "->$link"]) {
                        $joins[$alias . "->$link"] = $this->joinLink($alias, $modelLinks[$link], $alias . "->$link");
                    }

                    $model = $modelLinks[$link]['model'] . 'Model';
                    $alias .= "->$link";
                }
            }

            if (in_array($field, array_merge(array('id'),$model::getFields()))) {
                $orders["`$alias`.`$field`"] = str_replace("`$match`", "`$alias`.`$field`", trim($fieldOrder));
            } else if (in_array($field, $model::getFieldsLang())) {
                if ($this->id_lang) {
                    if (!$joins[$alias . '_lang']) {
                        $joins[$alias . "->$link"] = $this->joinLang($model, $alias);
                    }

                    $result["`${$alias}_lang`.`$field`"] = str_replace("`$match`", "`{$alias}_lang`.`$field`", trim($fieldOrder));
                } else {
                    throw new CollectionException("Can not use lang field '$match' in ORDER without specific language");
                }
            } else {
                throw new CollectionException("Can not use field '$match'. Field not exists in model.");
            }
        }

        return $orders;
    }

    private function buildQuery() {
        $selects = array();
        $joins = array();
        $orders = array();
        $model = $this->graph['model'];
        $alias = substr($model, 0, -5);

        $this->parseGraph($this->graph, $selects, $joins, $orders, $alias);
        $condition = $this->condition ? $this->parseMainCondition($joins) : '';
        $mainOrders = !empty($this->order) ? $this->parseMainOrder($joins) : array();
        $orders = array_merge($mainOrders, array_diff_key($orders, $mainOrders));
        $limit = $this->limit;

        $query = "SELECT " . implode(",\n\r", $selects) . "\n\r";
        $query .= "FROM `" . $model::TABLE . "` AS `$alias`\n\r";
        $query .= implode("\n\r", $joins);
        $query .= $condition ? "\n\rWHERE $condition" : '';
        $query .= !empty($orders) ? "\n\rORDER BY " . implode(", ", $orders) : '';
        $query .= $limit ? "\n\rLIMIT $limit" : '';

        return $query;
    }

    private function parseRow($row, &$items, $alias, $graph, $link = null, $model = null) {
        if ($id = $row[$alias . '->id']) {
            $linkedModel = $graph['model'];

            if (!isset($items[$alias][$id])) {
                $items[$alias][$id] = new $linkedModel(null, $this->id_lang);
                $items[$alias][$id]->setId($row[$alias . '->id']);

                foreach ($linkedModel::getFields() as $field) {
                    $items[$alias][$id]->{$field} = $row["{$alias}->{$field}"];
                }

                if ($this->id_lang && $linkedModel::hasLang()) {
                    foreach ($linkedModel::getFieldsLang() as $field) {
                        $items[$alias][$id]->{$field} = $row["{$alias}_lang->{$field}"];
                    }
                }
            }

            if ($model) {
                $model->link($link, $items[$alias][$id]);
            }

            foreach ($graph['links'] as $name => $link) {
                $this->parseRow($row, $items, $alias . "->$name", $link, $name, $items[$alias][$id]);
            }
        }
    }
}