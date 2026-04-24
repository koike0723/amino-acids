<!-- 生徒側必須キャリコン詳細画面 -->
<?php
require_once __DIR__ . '/functions/functions.php';

$db = db_connect();
$sql = 'SELECT * FROM m_times';
$stmt = $db->prepare($sql);
$stmt->execute();
$times = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
	<?php include('./inc/student_header.php'); ?>
	<?php
	$login_student = $_SESSION['student_id'];
	$selected_date = $_GET['selected_date'];
	$booking_id = $_GET['booking_id'];
	if (!isset($_SESSION['student_id'])) {
		header('location:./inc/login.php');
		exit();
	} else {
		$student = get_student($login_student);
	}

	$cc_require_student = get_course_cc_bookings_by_student((int)$login_student,  $selected_date);

	?>

	<main class="container py-4">
		<section class="student-required-cc-section">
			<div class="row justify-content-center">
				<div class="col-12 col-md-10 col-lg-8 text-center">

					<h1 class="student-required-cc-title mb-4">必須キャリコン一覧</h1>

					<?php foreach ($cc_require_student as $key => $tbody): ?>
						<div class="student-required-cc-block mb-4">
							<h2 class="student-required-cc-date mb-3"><?= $key; ?></h2>
							<div class="table-responsive d-flex justify-content-center">
								<table class="table table-bordered student-required-cc-table w-auto align-middle text-center mb-0">
									<thead>
										<tr>
											<th>時間</th>
											<th>必須キャリコン1</th>
											<th>必須キャリコン2</th>
										</tr>
									</thead>

									<tbody>
										<?php foreach ($times as $time): ?>
											<tr>
												<td class="td-time">
													<?= $time['display_name']; ?>
												</td>
												<?php $data = $tbody[$time['display_name']] ?? ''; ?>
												<?php for ($i = 0; $i < 2; $i++): ?>
													<?php if (isset($data[$i])): ?>
														<td<?= $student['student_id'] === $data[$i]['student_id'] ? ' class="td-name"' : ' class="td-else"' ?>>
															<?php if ($student['student_id'] === $data[$i]['student_id']): ?>
																<style>
																	td.td-name {
																		border: 3px solid blue;
																		background-color: #cdeeffbc;
																		color: red;
																	}
																</style>
																<?= $data[$i]['student_name'] ?>
															<?php else: ?>
																<style>
																	td.td-else a{
																		color: black;
																		
																	}
																</style>
																<a href="./student_cc_edit_require.php?selected_date=<?= $selected_date ?>&login_booking_id=<?= $booking_id ?>&booking_id=<?= $data[$i]['booking_id'] ?>"><?= $data[$i]['student_name'] ?>
																</a>
															<?php endif; ?>
															</td>
														<?php else: ?>
															<td></td>
														<?php endif; ?>
													<?php endfor; ?>
											</tr>
										<?php endforeach; ?>
									</tbody>

								</table>
							</div>
						</div>
					<?php endforeach; ?>

					<!-- 10月16日 -->
					<!-- <div class="student-required-cc-block mb-5">
                        <h2 class="student-required-cc-date mb-3">10月16日</h2>
                        <div class="table-responsive d-flex justify-content-center">
                            <table class="table table-bordered student-required-cc-table w-auto align-middle text-center mb-0">
                                <thead>
                                    <tr>
                                        <th>時間</th>
                                        <th>必須キャリコン1</th>
                                        <th>必須キャリコン2</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１０：００〜</a>
                                        </td>
                                        <td>小池</td>
                                        <td>環</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１１：００〜</a>
                                        </td>
                                        <td>小池</td>
                                        <td>環</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１２：００〜</a>
                                        </td>
                                        <td>小池</td>
                                        <td>環</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１４：００〜</a>
                                        </td>
                                        <td>小池</td>
                                        <td>環</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１５：００〜</a>
                                        </td>
                                        <td>小池</td>
                                        <td>環</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="./student_cc_edit_require.php" class="student-required-cc-time-link">１６：００〜</a>
                                        </td>
                                        <td>小池</td>
                                        <td>環</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> -->

					<div class="mt-4">
						<a href="./index.php" class="btn btn-secondary">戻る</a>
					</div>
				</div>
			</div>
		</section>
	</main>



	<script src="./js/script.js"></script>
</body>

</html>