<?php

namespace TAPI;

/**
 * test API :)
 */
final class Core
{

    /**
     * DB object
     */
    public static $db;

    /**
     * table name
     */
    public static $table = 'testapi';

    /**
     * The init
     */
    public static function init()
    {
        self::$db = new \PDO("mysql:dbname=cl36462_wpcraft;host=localhost", "cl36462_wpcraft", "wpcraft");
        self::route();
    }

    /**
     * Route requests
     */
    public static function route()
    {
        $data = [];

        if (isset($_GET['createTable'])) {
            self::createTable();
            exit;
        }

        if (empty($_GET)) {
            $data = self::read();
        }

        if ( ! empty($_GET)) {
            $data = self::read();
        }

        header('Content-type: application/json');
        header("Access-Control-Allow-Origin: *");
        echo json_encode($data);
        exit;
    }

    /**
     * read data
     */
    public static function read()
    {
        $offset = 0;
        $limit  = 20;

        if ( ! empty($_GET['limit']) && ! empty((int)$_GET['limit'])) {
            $limit = (int)$_GET['limit'];
        }

        if ( ! empty($_GET['offset']) && ! empty((int)$_GET['offset'])) {
            $offset = (int)$_GET['offset'];
        }

        $sql = sprintf("SELECT * FROM %s", self::$table);

        if ( ! empty($_GET['order'])) {
            $order = $_GET['order'];
            if ('oldest' == $order) {
                $sql .= ' ORDER BY created_at ASC';
            } else {
                $sql .= ' ORDER BY created_at DESC';
            }
        }

        if ($offset) {
            $sql .= ' OFFSET ' . $offset;
        }

        if ($limit) {
            $sql .= ' LIMIT ' . $limit;
        }

        $stmt = self::$db->query($sql);
        $data = [];
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        return $data;

    }

    /**
     * create table
     */
    public static function createTable()
    {
        $table = "testapi";
        try {

            self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);//Error Handling
            $sql = "CREATE TABLE IF NOT EXISTS $table (
     			ID INT AUTO_INCREMENT NOT NULL,
     			title VARCHAR( 50 ) NOT NULL,
     			content VARCHAR( 250 ) NOT NULL,
     			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				deleted_at TIMESTAMP,
				PRIMARY KEY (`ID`))
				CHARACTER SET utf8 COLLATE utf8_general_ci";

            self::$db->exec($sql);
            print("Created $table Table.\n");

        } catch (\PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }
}

Core::init();
