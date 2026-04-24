 <?php
    require_once __DIR__ . '/functions/functions.php';
    ?>
 <?php
    // DBからデータ取得
    session_start();

    // 先に session の値があるか確認
    if (!isset($_SESSION['student_id'])) {
        header('Location: ./inc/login.php');
        exit();
    }

    // session の student_id を使ってDBから取得
    $student = get_student($_SESSION['student_id']);
    // $student = get_student(1);

    $cc_date = $_GET['cc_date'];
    $cc_time = $_GET['cc_time'];


    // 配列の中身確認
    check($cc_date);
    check($cc_time);
    check($student);
    ?>

 <!doctype html>
 <html lang="ja">


 <head>
     <title>予約一覧</title>
     <link rel="stylesheet" href="./css/style.css">
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
     <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />

 </head>

 <body>
     <?php include('./inc/student_header.php'); ?>
     <?php
        $student = get_student($_SESSION['student_id']);
        if (!isset($_SESSION['student_id'])) {
            header('location:./inc/login.php');
            exit();
        }
        check($student);
        ?>
     <main class="container py-5">
         <section class="reservation-detail-section">
             <div class="row justify-content-center">
                 <div class="col-12 col-md-10 col-lg-8">

                     <h1 class="text-center mb-5 fs-3">予約詳細</h1>

                     <div class="mx-auto" style="max-width: 720px;">
                         <div class="row mb-4">
                             <div class="col-12 col-sm-4 text-sm-end fw-bold">クラス：</div>
                             <div class="col-12 col-sm-8"><?= $student['course_name']; ?></div>
                         </div>

                         <div class="row mb-4">
                             <div class="col-12 col-sm-4 text-sm-end fw-bold">名前：</div>
                             <div class="col-12 col-sm-8"><?= $student['student_name']; ?></div>
                         </div>

                         <div class="row mb-4">
                             <div class="col-12 col-sm-4 text-sm-end fw-bold">日時：</div>
                             <div class="col-12 col-sm-8"><?= $cc_date . $cc_time; ?></div>
                         </div>

                         <div class="row mb-5">
                             <div class="col-12 col-sm-4 text-sm-end fw-bold">方法：</div>
                             <?php foreach ($student['bookings'] as $booking): ?>
                                 <?php if ($cc_date === $booking['cc_date'] && $cc_time === $booking['cc_time']): ?>
                                     <div class="col-12 col-sm-8">
                                         <?php echo h($booking['cc_style_name']); ?>
                                     </div>
                                 <?php endif; ?>
                             <?php endforeach; ?>
                         </div>
                     </div>

                     <div class="text-center d-flex justify-content-center gap-3">
                         <a href="./php_do/reserve_del_do.php?student_id=<?php echo urlencode($student['student_id']); ?>&booking_id=<?php echo urlencode($booking['booking_id']); ?>"
                             class="btn btn-warning fw-bold">
                             キャンセル申請
                         </a>
                         <a href="index.php" class="btn btn-secondary">戻る</a>
                     </div>

                 </div>
             </div>
         </section>
     </main>

     <script src="./js/script.js"></script>
 </body>

 </html>