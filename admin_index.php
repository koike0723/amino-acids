<!-- 管理者トップページ -->
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
    <title>-管理者- キャリコン管理</title>
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
    <main>
        <div class="wrapper">
            <p class="h1"><b>キャリコン管理</b></p>
            <div class="cc-area">
                <div class="cc-content_area">
                    <p class="cc-text">開催クラス</p>
                    <div class="cc-select">
                        <label for="cc-select_list">
                            <select name="cc-select_list" id="cc-select_list" class="cc-select_style">
                                <option value="1">すべて</option>
                                <option value="2">6A</option>
                            </select>
                        </label>
                    </div>
                </div>
                <div class="cc-content_area">
                    <p class="cc-text">キャリコン</p>
                    <label for="cc-type">
                        <select name="cc-type" id="cc-type" class="cc-select_style">
                            <option value="1">すべて</option>
                            <option value="2">必須</option>
                            <option value="3">任意</option>
                        </select>
                    </label>
                </div>
                <div class="cc-content_area">
                    <p class="cc-text">表示期間</p>
                    <div class="cc-select">
                        <label for="cc-select_list">
                            <select name="cc-select_list" id="cc-select_list" class="cc-select_style">
                                <option value="1">2カ月</option>
                                <option value="2">3カ月</option>
                            </select>
                        </label>
                    </div>
                </div>
                <div class="cc-content_area">
                    <p class="cc-text">表示開始日</p>
                    <div class="cc-select">
                        <label for="cc-select_list">
                            <select name="cc-select_list" id="cc-select_list" class="cc-select_style">
                                <option value="1">2025/12/31</option>
                                <option value="2">3カ月</option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="wrapper">
            <table class="ad-index-table">
                <thead class="ad-index-thead">
                    <tr class="ad-index-headTr">
                        <th class="ad-index-th">年</th>
                        <th class="ad-index-th">月</th>
                        <th class="ad-index-th">日</th>
                        <th class="ad-index-th">必須キャリコン開催コース</th>
                        <th class="ad-index-th">キャリコン+</th>
                        <th class="ad-index-th">使用教室</th>
                        <th class="ad-index-th">操作</th>
                    </tr>
                </thead>
                <tbody class="ad-index-tbody">
                    <tr class="ad-index-tr">
                        <td rowspan="4" class="ad-index-td line-bold td-year">2025</td>
                        <td rowspan="4" class="ad-index-td line-bold td-month">12</td>
                        <td class="ad-index-td line-bold va-middle">6</td>
                        <td class="ad-index-td line-bold va-middle">6A</td>
                        <td class="ad-index-td cc-plus-fz line-bold va-middle">
                            <label for="cc-plus-select">
                                <select name="cc-plus-select" id="cc-plus-select" class="cc-plus_select">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </label>
                        </td>
                        <td class="ad-index-td cc-plus-fz line-bold va-middle">8</td>
                        <td class="ad-index-td line-bold">
                            <a href="#" class="cc-plus-btn">
                                <button type="btn" class="ad-index-detailBtn">
                                    詳細
                                </button>
                            </a>
                        </td>
                    </tr>
                    <!-- ここからダミーテーブル -->
                    <!-- 1個目のテーブル -->
                    <tr class="ad-index-tr">
                        <td class="ad-index-td va-middle">6</td>
                        <td class="ad-index-td va-middle">6A</td>
                        <td class="ad-index-td cc-plus-fz">
                            <label for="cc-plus-select">
                                <select name="cc-plus-select" id="cc-plus-select">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </label>
                        </td>
                        <td class="ad-index-td cc-plus-fz va-middle">8</td>
                        <td class="ad-index-td">
                            <a href="#" class="cc-plus-btnva-middle">
                                <button type="btn" class="ad-index-detailBtn">
                                    詳細
                                </button>
                            </a>
                        </td>
                    </tr>
                    <tr class="ad-index-tr">
                        <td class="ad-index-td">6</td>
                        <td class="ad-index-td">6A</td>
                        <td class="ad-index-td cc-plus-fz">
                            <label for="cc-plus-select">
                                <select name="cc-plus-select" id="cc-plus-select">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </label>
                        </td>
                        <td class="ad-index-td cc-plus-fz">8</td>
                        <td class="ad-index-td">
                            <a href="#" class="cc-plus-btn">
                                <button type="btn" class="ad-index-detailBtn">
                                    詳細
                                </button>
                            </a>
                        </td>
                    </tr>
                    <tr class="ad-index-tr">
                        <td class="ad-index-td">6</td>
                        <td class="ad-index-td">6A</td>
                        <td class="ad-index-td cc-plus-fz">
                            <label for="cc-plus-select">
                                <select name="cc-plus-select" id="cc-plus-select">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </label>
                        </td>
                        <td class="ad-index-td cc-plus-fz">8</td>
                        <td class="ad-index-td">
                            <a href="#" class="cc-plus-btn">
                                <button type="btn" class="ad-index-detailBtn">
                                    詳細
                                </button>
                            </a>
                        </td>
                    </tr>
                    <!-- １個目のテーブルここまで -->
                    <!-- ダミーテーブルここまで -->

                    <!-- 2個目のテーブル -->
                <tbody class="ad-index-table">
                    <tr class="ad-index-tr">
                        <td rowspan="4" class="ad-index-td line-bold td-year">2026</td>
                        <td rowspan="4" class="ad-index-td line-bold td-month">1</td>
                        <td class="ad-index-td">6</td>
                        <td class="ad-index-td">6A</td>
                        <td class="ad-index-td cc-plus-fz">
                            <label for="cc-plus-select">
                                <select name="cc-plus-select" id="cc-plus-select">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </label>
                        </td>
                        <td class="ad-index-td cc-plus-fz">8</td>
                        <td class="ad-index-td">
                            <a href="#" class="cc-plus-btn">
                                <button type="btn" class="ad-index-detailBtn">
                                    詳細
                                </button>
                            </a>
                        </td>
                    </tr>
                    <!-- ここからダミーテーブル -->
                    <tr class="ad-index-tr">
                        <td class="ad-index-td">6</td>
                        <td class="ad-index-td">6A</td>
                        <td class="ad-index-td cc-plus-fz">
                            <label for="cc-plus-select">
                                <select name="cc-plus-select" id="cc-plus-select">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </label>
                        </td>
                        <td class="ad-index-td cc-plus-fz">8</td>
                        <td class="ad-index-td">
                            <a href="#" class="cc-plus-btn">
                                <button type="btn" class="ad-index-detailBtn">
                                    詳細
                                </button>
                            </a>
                        </td>
                    </tr>
                    <tr class="ad-index-tr">
                        <td class="ad-index-td">6</td>
                        <td class="ad-index-td">6A</td>
                        <td class="ad-index-td cc-plus-fz">
                            <label for="cc-plus-select">
                                <select name="cc-plus-select" id="cc-plus-select">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </label>
                        </td>
                        <td class="ad-index-td cc-plus-fz">8</td>
                        <td class="ad-index-td">
                            <a href="#" class="cc-plus-btn">
                                <button type="btn" class="ad-index-detailBtn">
                                    詳細
                                </button>
                            </a>
                        </td>
                    </tr>
                    <tr class="ad-index-tr">
                        <td class="ad-index-td">6</td>
                        <td class="ad-index-td">6A</td>
                        <td class="ad-index-td cc-plus-fz">
                            <label for="cc-plus-select">
                                <select name="cc-plus-select" id="cc-plus-select">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </label>
                        </td>
                        <td class="ad-index-td cc-plus-fz">8</td>
                        <td class="ad-index-td">
                            <a href="#" class="cc-plus-btn">
                                <button type="btn" class="ad-index-detailBtn">
                                    詳細
                                </button>
                            </a>
                        </td>
                    </tr>
                    <!-- ダミーテーブルここまで -->
                    <!-- 2個目のテーブルここまで -->
                </tbody>
            </table>
        </div>
    </main>
    <script src="./js/script.js"></script>
    <script src="./js/hamburger.js"></script>
</body>


</html>