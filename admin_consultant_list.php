<!-- コンサルタント一覧 -->
<?php require_once __DIR__ . '/functions/functions.php';
?>
<?php
/////////////////////////////////////////////////
/////////////////////GET通信処理/////////////////
/////////////////////////////////////////////////
if (isset($_GET['cc_date'])) {
    $cc_date = $_GET['cc_date'];
}
?>

<?php
/////////////////////////////////////////////////////
/////////////////////データベース処理/////////////////
////////////////////////////////////////////////////
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $db = new PDO($dsn, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // SQL
    $sql = 'SELECT id, CONCAT(first_name,last_name) AS name FROM m_consultants';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $cc_teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('キャリアコンサルタント達の取得に失敗しました: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/destyle.css@4.0.1/destyle.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
    <link rel="stylesheet" href="./css/style.css">
    <title>コンサルタント一覧</title>
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
    <main>
        <div class="container-fluid px-4 py-4">
            <h1 class="h3 font-weight-bold text-center mb-4">コンサルタント一覧</h1>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 ad-index-table">
                            <thead>
                                <tr>
                                    <th class="ad-index-th">名前</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($cc_teachers)): ?>
                                    <?php foreach ($cc_teachers as $teacher): ?>
                                        <tr>
                                            <td class="ad-index-td"><?php echo htmlspecialchars($teacher['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td class="ad-index-td">登録されているコンサルタントはいません。</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="./admin_index.php" class="btn btn-secondary">トップへ戻る</a>
            </div>
        </div>
    </main>
    <script src="./js/script.js"></script>
    <script src="./js/hamburger.js"></script>
</body>

</html>
