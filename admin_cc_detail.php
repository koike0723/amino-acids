<!-- 必須キャリコンをドラック&ドロップで管理できる管理者画面 -->
<?php require_once __DIR__ . '/functions/functions.php'; ?>

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
        <div class="cc-mgmt">
            <p class="cc-head-text">キャリコン・ライン管理</p>
            <p class="cc-head-date">2026年1月17日</p>
        </div>
        <div class="previous-btn cc-detail-btn-area">
            <a href="#">
                <button type="btn" class="prev-btn cc-detail-btn">戻る</button>
            </a>
        </div>
        <div class="cc-detail-table-area">
            <table class="cc-detail-table">
                <thead class="cc-detail-thead">
                    <tr class="cc-detail-headTr">
                        <th class="cc-detail-th">教室</th>
                        <th class="cc-detail-th">講師</th>
                        <th class="cc-detail-th">10:00</th>
                        <th class="cc-detail-th">11:00</th>
                        <th class="cc-detail-th">12:00</th>
                        <th class="cc-detail-th">13:00</th>
                        <th class="cc-detail-th">14:00</th>
                        <th class="cc-detail-th">15:00</th>
                        <th class="cc-detail-th">16:00</th>
                    </tr>
                </thead>
                <tbody class="cc-detail-tbody">
                    <tr class="cc-detail-tr">
                        <td class="cc-detail-td cc-detail-td-app va-middle">
                            <label for="cc-detail-select-class">
                                <select name="cc-detail-select-class" id="cc-detail-select-class" class="cc-detail_selectStyle">
                                    <option value="1">6A</option>
                                    <option value="2">6B</option>
                                </select>
                            </label>
                        </td>
                        <td class="cc-detail-td cc-detail-td-app va-middle">
                            <label for="cc-detail-select-teacher">
                                <select name="cc-detail-select-teacher" id="cc-detail-select-teacher" class="cc-detail_selectStyle">
                                    <option value="1">竹内百合子</option>
                                    <option value="2">福本祐介</option>
                                </select>
                            </label>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                    </tr>
                    <!-- ダミーテーブル -->
                    <tr class="cc-detail-tr">
                        <td class="cc-detail-td cc-detail-td-app va-middle">
                            <label for="cc-detail-select-class">
                                <select name="cc-detail-select-class" id="cc-detail-select-class" class="cc-detail_selectStyle">
                                    <option value="1">6A</option>
                                    <option value="2">6B</option>
                                </select>
                            </label>
                        </td>
                        <td class="cc-detail-td cc-detail-td-app va-middle">
                            <label for="cc-detail-select-teacher">
                                <select name="cc-detail-select-teacher" id="cc-detail-select-teacher" class="cc-detail_selectStyle">
                                    <option value="1">竹内百合子</option>
                                    <option value="2">福本祐介</option>
                                </select>
                            </label>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                    </tr>
                    <tr class="cc-detail-tr">
                        <td class="cc-detail-td cc-detail-td-app va-middle">
                            <label for="cc-detail-select-class">
                                <select name="cc-detail-select-class" id="cc-detail-select-class" class="cc-detail_selectStyle">
                                    <option value="1">6A</option>
                                    <option value="2">6B</option>
                                </select>
                            </label>
                        </td>
                        <td class="cc-detail-td cc-detail-td-app va-middle">
                            <label for="cc-detail-select-teacher">
                                <select name="cc-detail-select-teacher" id="cc-detail-select-teacher" class="cc-detail_selectStyle">
                                    <option value="1">竹内百合子</option>
                                    <option value="2">福本祐介</option>
                                </select>
                            </label>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                    </tr>
                    <tr class="cc-detail-tr">
                        <td class="cc-detail-td cc-detail-td-app va-middle">
                            <label for="cc-detail-select-class">
                                <select name="cc-detail-select-class" id="cc-detail-select-class" class="cc-detail_selectStyle">
                                    <option value="1">6A</option>
                                    <option value="2">6B</option>
                                </select>
                            </label>
                        </td>
                        <td class="cc-detail-td cc-detail-td-app va-middle">
                            <label for="cc-detail-select-teacher">
                                <select name="cc-detail-select-teacher" id="cc-detail-select-teacher" class="cc-detail_selectStyle">
                                    <option value="1">竹内百合子</option>
                                    <option value="2">福本祐介</option>
                                </select>
                            </label>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                    </tr>
                    <!-- ダミーテーブルここまで -->
                </tbody>
            </table>
        </div>

        <!-- 任意キャリコン分のテーブル -->
        <div class="cc-detail-table-area" style="padding: 50px 0; background-color: #CDEFFF;">
            <table class="cc-detail-table cc-detail-table-optional" style="background-color: white;">
                <thead class="cc-detail-thead">
                    <tr class="cc-detail-headTr">
                        <th class="cc-detailth cc-detail-th-size">
                            <p class="null-text" style="margin: 0;">&nbsp;</p>
                        </th>
                        <th class="cc-detail-th">10:00</th>
                        <th class="cc-detail-th">11:00</th>
                        <th class="cc-detail-th">12:00</th>
                        <th class="cc-detail-th">13:00</th>
                        <th class="cc-detail-th">14:00</th>
                        <th class="cc-detail-th">15:00</th>
                        <th class="cc-detail-th">16:00</th>
                    </tr>
                </thead>
                <tbody class="cc-detail-tbody">
                    <tr class="cc-detail-tr">
                        <td class="cc-detail-td">
                            <p class="null-text">&nbsp;</p>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                    </tr>
                    <!-- ダミーテーブル -->
                    <tr class="cc-detail-tr">
                        <td class="cc-detail-td">
                            <p class="null-text">&nbsp;</p>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                    </tr>
                    <tr class="cc-detail-tr">
                        <td class="cc-detail-td">
                            <p class="null-text">&nbsp;</p>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                    </tr>
                    <tr class="cc-detail-tr">
                        <td class="cc-detail-td">
                            <p class="null-text">&nbsp;</p>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                        <td class="cc-detail-td">
                            <div class="cc-detail-student-card">
                                <p class="cc-detail-student">6c</p>
                                <p class="cc-detail-student">リカレント太郎</p>
                                <p class="cc-detail-student">対面</p>
                            </div>
                        </td>
                    </tr>
                    <!-- ダミーテーブルここまで -->
                </tbody>
            </table>
        </div>
        <div class="cc-detail-optional-open">
            <?php require_once __DIR__ . '/inc/cc_table.php'; ?>
        </div>

    </main>
</body>

</html>