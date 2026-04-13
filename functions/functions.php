<?php
// DBへの接続情報
define('DB_HOST', 'localhost');
define('DB_NAME', 'gyozafes');
define('DB_USER', 'root');
define('DB_PASS', '');

/**
 * データベース接続開始
 * @return PDO データベース操作用のオブジェクト
 */
function db_connect()
{
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
    $db = new PDO($dsn, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    return $db;
}

//データベース接続終了

// デバックチェック関数
function check($str)
{
    echo "<pre>";
    var_dump($str);
    echo "</pre>";
}

