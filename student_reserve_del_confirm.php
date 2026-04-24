<?php
require_once __DIR__ . '/functions/functions.php';

// index.php から受け取る
$selected_date = $_GET['selected_date'] ?? '';
$time = $_GET['time'] ?? '';

// 一致する予約を探す

?>
<!doctype html>
<html lang="ja">

<head>
	<title>予約追加・変更</title>
	<link rel="stylesheet" href="./css/style.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>
	<?php include('./inc/student_header.php'); ?>
	<?php
	$login_student = $_SESSION['student_id'];
	if (!isset($_SESSION['student_id'])) {
		header('location:./inc/login.php');
		exit();
	} else {
		$student = get_student($login_student);
	}

	$target_booking = null;

	if (!empty($student['bookings'])) {
		foreach ($student['bookings'] as $booking) {
			if ($booking['cc_date'] === $selected_date && $booking['cc_time'] === $time) {
				$target_booking = $booking;
				break;
			}
		}
	}
	?>
	<!doctype html>
	<html lang="ja">

	<head>
		<title>予約追加・変更</title>
		<link rel="stylesheet" href="./css/style.css">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=notifications" />
	</head>

	<body>
		<main class="container py-5">
			<form action="./php_do/reserve_del_do.php" method="GET">
				<section class="student-reservation-detail-section">
					<div class="row justify-content-center">
						<div class="col-12 col-md-10 col-lg-8">

							<h1 class="student-reservation-detail-title text-center mb-5">予約詳細</h1>

							<?php if ($target_booking): ?>
								<div class="student-reservation-detail-body mx-auto">
									<div class="row mb-4">
										<div class="col-12 col-sm-4 text-sm-end fw-bold">クラス：</div>
										<div class="col-12 col-sm-8">
											<?php echo htmlspecialchars($student['course_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
										</div>
									</div>

									<div class="row mb-4">
										<div class="col-12 col-sm-4 text-sm-end fw-bold">名前：</div>
										<div class="col-12 col-sm-8">
											<?php echo htmlspecialchars($student['student_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
										</div>
									</div>

									<div class="row mb-4">
										<div class="col-12 col-sm-4 text-sm-end fw-bold">日時：</div>
										<div class="col-12 col-sm-8">
											<?php echo htmlspecialchars($target_booking['cc_date'], ENT_QUOTES, 'UTF-8'); ?>
											<?php echo htmlspecialchars(substr($target_booking['cc_time'], 0, 5), ENT_QUOTES, 'UTF-8'); ?>
										</div>
									</div>

									<div class="row mb-5">
										<div class="col-12 col-sm-4 text-sm-end fw-bold">方法：</div>
										<div class="col-12 col-sm-8">
											<?php echo htmlspecialchars($target_booking['cc_style_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
										</div>
									</div>
								</div>

								<div class="student-reservation-detail-buttons d-flex justify-content-center gap-3">
									<input type="hidden" name="booking_id" value="<?= h($booking['booking_id']); ?>">
									<input type="hidden" name="selected_date" value="<?= h($selected_date); ?>">
									<input type="hidden" name="time" value="<?= h($time); ?>">
									<button type="submit" class="btn btn-warning student-reservation-detail-cancel-btn">
										キャンセル申請
									</button>
									<a href="./index.php" class="btn btn-secondary">
										戻る
									</a>
								</div>

							<?php else: ?>
								<p class="text-center text-danger">該当する予約が見つかりませんでした。</p>
								<div class="text-center">
									<a href="./index.php" class="btn btn-secondary">戻る</a>
								</div>
							<?php endif; ?>

						</div>
					</div>
				</section>
			</form>
		</main>


		<script src="./js/script.js"></script>
	</body>

	</html>