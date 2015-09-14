<?php

class DatabaseException extends Exception {}

class Database {
    private static $connection;

    // Соединение с базой данных
    static public function connect ($host, $login, $password, $database, $charset){
        self::$connection = mysqli_connect($host, $login, $password, $database);
        if (!self::$connection) {
            throw new DatabaseException("Database connection error");
        }

        mysqli_set_charset(self::$connection, $charset);
    }

    // Выполнение любого запроса, возврат необработанного результата
    static public function query($query){
        $response = mysqli_query(self::$connection, $query);
        if (!$response) {
            throw new DatabaseException("Database query error: \n\r $query \n\r (" . mysqli_error(self::$connection) . ")");
        }

        return $response;
    }

    /*
     * Выполение SELECT запроса и возврат значения первого поля из первой строки
     * Используется когда нужно выполнить SELECT запрос на получение одного значения
     */
    static public function getValue($query){
        $response = self::query($query);

        if ($row = mysqli_fetch_array($response)) {
            return current($row);
        }

        return null;
    }

    /*
     * Выполение SELECT запроса и возврат первой строки в виде ассоциативного массива
     * Используется когда нужно выполнить SELECT запрос на получение одной строки
     */
    static public function getRow($query){
        $response = self::query($query);
        return mysqli_fetch_assoc($response);
    }

    /*
     * Выполение SELECT запроса и возврат всех строк в виде массива
     */
    static public function getTable($query, $index = null){
        $response = self::query($query);

        $array = array();
        while($row = mysqli_fetch_assoc($response)){
            if($index){
                $array[$row[$index]] = $row;
            }else{ $array[] = $row; }
        }

        return $array;
    }

    // Возврат автоматически сгенерированного базой id после выполнения INSERT запроса
    static public function getInsertId(){
        return mysqli_insert_id(self::$connection);
    }
}

// Константы типов связей между таблицами
class LinkType {
    const PRIMARY_KEY = 1;
    const FOREIGN_KEY = 2;
    const TABLE = 3;
}
