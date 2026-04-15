<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'cc_user');
define('DB_PASS', 'password');
define('DB_NAME', 'career_consultant');

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $db = new PDO($dsn, DB_USER, DB_PASS);
    // エラーモードを例外に設定
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // フェッチモードを連想配列形式に設定
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $sql = 'SELECT * FROM m_admins';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('データの取得に失敗しました: ' . $e->getMessage());
}
?>