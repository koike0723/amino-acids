<!-------------------------------------------------------------------------------

キャリコンの日付の変更がまだできていません
update_course()で$courseに入れる配列が
$course = [    
            'name' => '新コース名',
            '???'  => '???',
            'cc'   => [
                        1 => ['2026-05-10', '2026-05-17'],
                        2 => ['2026-06-14'],
                      ],
          ];
みたいな形でできてはいるんだけど、ccの編集がうまくいかない。。。
代わりに
$test =  [    
            'name' => '新コース名',
            'cc'   => [
                        1 => ['2026-05-10', '2026-05-17'],
                        2 => ['2026-06-10', '2026-06-17'],
                        3 => ['2026-07-10', '2026-07-17'], 
                      ],
          ];
とかをupdate_course()に突っ込んでみたりしたけど動かなかった。お手上げ；；
// 1⃣デバッグ用に連想配列を出力させるものを入れているから、よかったら使ってください。

--------------------------------------------------------------------------------->

<!-- コース編集実行処理 -->
<?php
require_once __DIR__ . '/../functions/functions.php';


$course_id = $_POST["course_id"] ?? null;

//////////////////////////////////////////////////
//////////////////データベース処理/////////////////
//////////////////////////////////////////////////
try {
    $old_course = get_course($course_id);
} catch (PDOException $e) {
    exit('コース詳細の取得に失敗しました: ' . $e->getMessage());
}

// データの有無確認処理
if (
    !isset($_POST["course_id"], $_POST["course_name"], $_POST["room_id"], $_POST["category_id"], $_POST["course_start"], $_POST["course_finish"]) ||
    empty($_POST["course_id"]) ||
    empty($_POST["course_name"]) ||
    empty($_POST["room_id"]) ||
    empty($_POST["category_id"]) ||
    empty($_POST["course_start"]) ||
    empty($_POST["course_finish"])
) {
    header("Location: ../admin_course_edit.php?course_id=" . $course_id . "&status=error&message=no_data");
    exit;
}
if ($old_course["category_id"] == 1) {
    foreach ($old_course["cc"] as $cc_count => $dates) {
        foreach ($dates as $i => $date) {
            if ($date === "0000-00-00") {
                $old_course["cc"][$cc_count][$i] = "";
            }
        }
    }
}

// 送られてきたデータの取得成形
$course_name = $_POST["course_name"];
$start_date = $_POST["course_start"];
$finish_date = $_POST["course_finish"];
$room_id = $_POST["room_id"];
$category_id = $_POST["category_id"];
// cc{N}_1 / cc{N}_2 形式のPOSTデータを動的に収集
$raw_cc = [];
foreach ($_POST as $key => $value) {
    if (preg_match('/^cc(\d+)_([12])$/', $key, $matches)) {
        $raw_cc[(int)$matches[1]][(int)$matches[2]] = $value;
    }
}
ksort($raw_cc);

// $new_cc の組み立て・重複チェック用日付収集
$cc_days = [];
$new_cc = [];
foreach ($raw_cc as $cc_count => $slots) {
    $date1 = $slots[1] ?? '';
    $date2 = $slots[2] ?? '';
    $filtered = array_values(array_filter([$date1, $date2]));
    if (!empty($filtered)) {
        $new_cc[$cc_count] = $filtered;
    }
    if (!empty($date1)) $cc_days[] = $date1;
    if (!empty($date2)) $cc_days[] = $date2;
}
$course = [];

if ($old_course["course_name"] != $course_name) {
    $course["name"] = $course_name;
}

if ($old_course["start_date"] != $start_date) {
    $course["start_date"] = $start_date;
}

if ($old_course["end_date"] != $finish_date) {
    $course["end_date"] = $finish_date;
}

if ($old_course["room_id"] != $room_id) {
    $course["room_id"] = $room_id;
}

if ($old_course["category_id"] != $category_id) {
    $course["category_id"] = $category_id;
}

if ($category_id == 1) {
    if ($old_course["cc"] != $new_cc) {
        $course["cc"] = $new_cc;
    }
} else {
    if (!empty($old_course["cc"])) {
        $course["cc"] = [];
    }
}


// コース開始日と終了日の入力値チェック
if (strtotime($start_date) > strtotime($finish_date)) {
    header("Location: ../admin_course_edit.php?course_id=" . $course_id . "&status=error&message=error_date");
    exit;
}

//キャリコンの日がかぶっていないかチェック
if (count($cc_days) != count(array_unique($cc_days))) {
    header("Location: ../admin_course_edit.php?course_id=" . $course_id . "&status=error&message=error_cc_date");
    exit;
}

// 1⃣デバッグ用
// check($old_course);
// echo "---------------------------------------------";
// check($course);
// echo "---------------------------------------------";
// check($new_cc);
// exit;

// 訓練コースの編集
try {
    update_course($course_id, $course);
} catch (PDOException $e) {
    // exit('訓練コースの編集に失敗しました: ' . $e->getMessage());
    header("Location: ../admin_course_edit.php?course_id=" . $course_id . "&status=error&message=cant_db");
    exit;
}
header("Location: ../admin_course_detail.php?course_id=" . $course_id . "&status=success");
exit;
?>