<!-- 必須キャリコン一覧 -->
<?php
require_once __DIR__ . '/functions/functions.php';
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/destyle.css@4.0.1/destyle.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <title>-管理者- 必須キャリコン一覧</title>
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
    <main>
        <div class="cc-title-flex">
            <p class="cc-req-list">4月&nbsp;必須キャリコン一覧</p>
            <a href="#">
                <button type="btn" class="add-bulk-page">
                    一括登録
                </button>
            </a>
        </div>
        <section class="cc-req-table-sec">
            <div class="cc-req-held-date">
                <p class="cc-req-day-text">
                    10月9日
                </p>
            </div>
            <div class="cc-req-table-area">
                <table class="cc-req-table">
                    <thead class="cc-req-list-thead">
                        <tr class="cc-req-list-headTr">
                            <th class="cc-req-list-th">時間</th>
                            <th class="cc-req-list-th">必須キャリコン1</th>
                            <th class="cc-req-list-th">必須キャリコン2</th>
                        </tr>
                    </thead>
                    <tbody class="cc-req-list-tbody">
                        <tr class="cc-req-list-tr">
                            <td class="cc-req-list-td">10:00～</td>
                            <td class="cc-req-list-td">梅崎</td>
                            <td class="cc-req-list-td">小松</td>
                        </tr>
                        <!-- ダミーテーブル -->
                        <tr class="cc-req-list-tr">
                            <td class="cc-req-list-td">11:00～</td>
                            <td class="cc-req-list-td">梅崎</td>
                            <td class="cc-req-list-td">小松</td>
                        </tr>
                        <tr class="cc-req-list-tr">
                            <td class="cc-req-list-td">12:00～</td>
                            <td class="cc-req-list-td">梅崎</td>
                            <td class="cc-req-list-td">小松</td>
                        </tr>
                        <tr class="cc-req-list-tr">
                            <td class="cc-req-list-td">13:00～</td>
                            <td class="cc-req-list-td">梅崎</td>
                            <td class="cc-req-list-td">小松</td>
                        </tr>
                        <!-- ダミーテーブルここまで -->
                    </tbody>
                </table>
            </div>
        </section>
        <section class="cc-req-table-sec">
            <div class="cc-req-held-day">
                <p class="cc-req-day-text">
                    10月9日
                </p>
            </div>
            <div class="cc-req-table-area">
                <table class="cc-req-table">
                    <thead class="cc-req-list-thead">
                        <tr class="cc-req-list-headTr">
                            <th class="cc-req-list-th">時間</th>
                            <th class="cc-req-list-th">必須キャリコン1</th>
                            <th class="cc-req-list-th">必須キャリコン2</th>
                        </tr>
                    </thead>
                    <tbody class="cc-req-list-tbody">
                        <tr class="cc-req-list-tr">
                            <td class="cc-req-list-td">10:00～</td>
                            <td class="cc-req-list-td">梅崎</td>
                            <td class="cc-req-list-td">小松</td>
                        </tr>
                        <!-- ダミーテーブル -->
                        <tr class="cc-req-list-tr">
                            <td class="cc-req-list-td">11:00～</td>
                            <td class="cc-req-list-td">梅崎</td>
                            <td class="cc-req-list-td">小松</td>
                        </tr>
                        <tr class="cc-req-list-tr">
                            <td class="cc-req-list-td">12:00～</td>
                            <td class="cc-req-list-td">梅崎</td>
                            <td class="cc-req-list-td">小松</td>
                        </tr>
                        <tr class="cc-req-list-tr">
                            <td class="cc-req-list-td">13:00～</td>
                            <td class="cc-req-list-td">梅崎</td>
                            <td class="cc-req-list-td">小松</td>
                        </tr>
                        <!-- ダミーテーブルここまで -->
                    </tbody>
                </table>
            </div>
        </section>
        <div class="cc-req-controle">
            <a href="#">
                <button type="btn" class="cc-req-controle-btn">キャンセル</button>
            </a>
            <a href="#">
                <button type="btn" class="cc-req-controle-btn">入れ替え</button>
            </a>
        </div>
    </main>
    <script src="./js/script.js"></script>
    <script src="./js/hamburger.js"></script>
</body>

</html>