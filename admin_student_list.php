<!-- 生徒画面 -->
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
    <title>-管理者- 生徒一覧</title>
</head>

<body>
    <?php require_once __DIR__ . '/inc/admin_header.php'; ?>
    <main>
        <p class="h1">生徒一覧</p>
        <div class="admin-student-list">
            <div class="admin-stuent-search-area">
                <div class="admin-student-flex">
                    <p class="ad-stu_search">コース</p>
                    <p class="ad-stu_search">名前</p>
                    <p class="ad-stu_search">状態</p>
                    <div class="admin-student-flex-btn">
                        <div class="search-btn">
                            <p class="ad-stu_search">詳細設定</p>
                        </div>
                        <div class="search-btn">
                            <p class="ad-stu_search">○</p>
                        </div>
                        <!-- 検索欄横の追加ボタン -->
                    </div>
                </div>
            </div>
        </div>
        <div class="admin-student-select">
            <div class="admin-add-flex">
                <label for="ad-stu_select">
                    <select name="ad-stu_select" id="ad-stu_select" class="cc-select_style">
                        <option value="1">クラス設定</option>
                        <option value="2">クラス表示</option>
                    </select>
                </label>
                <a href="#">
                    <button type="btn" class="add-btn">生徒追加</button>
                </a>
                <a href="#">
                    <button type="btn" class="add-btn">生徒一括追加</button>
                </a>
            </div>
        </div>
        <div class="table-area">
            <table class="ad-stu-list-table">
                <thead>
                    <tr class="ad-stu-headTr">
                        <th class="ad-stu-list-th">名前</th>
                        <th class="ad-stu-list-th">訓練名</th>
                        <th class="ad-stu-list-th">状態</th>
                        <th class="ad-stu-list-th">利用可</th>
                        <th class="ad-stu-list-th">操作</th>
                    </tr>
                </thead>
                <tbody class="ad-stu-list-tbody">
                    <tr class="ad-stu-list-tr">
                        <td class="ad-stu-list-td">梅崎さん</td>
                        <td class="ad-stu-list-td">6C/Webプログラミング科</td>
                        <td class="ad-stu-list-td">在籍中</td>
                        <td class="ad-stu-list-td">利用可</td>
                        <td class="ad-stu-list-td td-btn-flex">
                            <a href="#">
                                <button type="btn" class="controle-btn controle-edit">編集</button>
                            </a>
                            <a href="#">
                                <button type="btn" class="controle-btn controle-detail">詳細</button>
                            </a>
                            <a href="#">
                                <button type="btn" class="controle-btn controle-delete">削除</button>
                            </a>
                        </td>
                    </tr>
                    <!-- ダミーテーブル -->
                     <tr class="ad-stu-list-tr">
                        <td class="ad-stu-list-td">江原さん</td>
                        <td class="ad-stu-list-td">6C/Webプログラミング科</td>
                        <td class="ad-stu-list-td">在籍中</td>
                        <td class="ad-stu-list-td">利用可</td>
                        <td class="ad-stu-list-td td-btn-flex">
                            <a href="#">
                                <button type="btn" class="controle-btn controle-edit">編集</button>
                            </a>
                            <a href="#">
                                <button type="btn" class="controle-btn controle-detail">詳細</button>
                            </a>
                            <a href="#">
                                <button type="btn" class="controle-btn controle-delete">削除</button>
                            </a>
                        </td>
                     </tr>
                     <tr class="ad-stu-list-tr">
                        <td class="ad-stu-list-td">大古場さん</td>
                        <td class="ad-stu-list-td">6C/Webプログラミング科</td>
                        <td class="ad-stu-list-td">在籍中</td>
                        <td class="ad-stu-list-td">利用可</td>
                        <td class="ad-stu-list-td td-btn-flex">
                            <a href="#">
                                <button type="btn" class="controle-btn controle-edit">編集</button>
                            </a>
                            <a href="#">
                                <button type="btn" class="controle-btn controle-detail">詳細</button>
                            </a>
                            <a href="#">
                                <button type="btn" class="controle-btn controle-delete">削除</button>
                            </a>
                        </td>
                    </tr>
                    <tr class="ad-stu-list-tr">
                        <td class="ad-stu-list-td">小倉さん</td>
                        <td class="ad-stu-list-td">6C/Webプログラミング科</td>
                        <td class="ad-stu-list-td">在籍中</td>
                        <td class="ad-stu-list-td">利用可</td>
                        <td class="ad-stu-list-td td-btn-flex">
                            <a href="#">
                                <button type="btn" class="controle-btn controle-edit">編集</button>
                            </a>
                            <a href="#">
                                <button type="btn" class="controle-btn controle-detail">詳細</button>
                            </a>
                            <a href="#">
                                <button type="btn" class="controle-btn controle-delete">削除</button>
                            </a>
                        </td>
                    </tr>
                    <!-- ダミーテーブルここまで -->
                </tbody>
            </table>
        </div>
        <div class="previous-btn">
            <a href="#">
                <button type="btn" class="prev-btn">戻る</button>
            </a>
        </div>
    </main>
</body>

</html>