<!-- http://localhost:8080/amino-acids/cc_detail.php -->
<?php
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////データベース処理/////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
    $sql = 'SELECT * FROM t_cc_slots';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $cc_slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('時間帯データ（m_times）の取得に失敗しました: ' . $e->getMessage());
}
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $db = new PDO($dsn, DB_USER, DB_PASS);
    // エラーモードを例外に設定
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // フェッチモードを連想配列形式に設定
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $sql = 'SELECT * FROM t_cc_slots WHERE is_cc_plus=:cc_flag';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":cc_flag", 1, PDO::PARAM_INT);
    $stmt->execute();
    $cc_plus_slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('時間帯データ（m_times）の取得に失敗しました: ' . $e->getMessage());
}

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $db = new PDO($dsn, DB_USER, DB_PASS);
    // エラーモードを例外に設定
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // フェッチモードを連想配列形式に設定
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $sql = 'SELECT students.first_name AS first_name, s.first_name AS first_name FROM  INNER JOIN students s ON sc.student_id = s.id INNER JOIN classes c ON sc.class_id = c.id;';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $times = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('データの取得に失敗しました: ' . $e->getMessage());
}
?>




<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ライン管理</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>

    <h1>キャリコン・ライン管理</h1>
    <!-- kan_to_do:データベースから持ってくるキャリコン実施日表示 -->
    <h2>2026年1月17日</h2>

    <a href="" class="btn btn-secondary">戻る</a>

    <table>
        <thead>
            <th>
            <td></td>
            </th>
        </thead>
    </table>
</body>
<script src="./js/drag_and_drop.js"></script>

</html>