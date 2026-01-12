<?php
session_start();
require './config/db.php';
require './functions/Auth.php';
require './functions/Plan.php';

if (!isset($_SESSION['studentId'])) {
    header("Location: ./auth.php");
    exit();
}

$student = getStudent($conn, $_SESSION['studentId']);


if (!isset($student['plan']) || $student['plan'] === '') {
    header("Location: ./account.php");
    exit();
}

// Get student's question answers if the plan has questions
$questionAnswers = getStudentQuestionAnswers($conn, $_SESSION['studentId'], $student['plan']);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สถานะการสมัคร | โรงเรียนภูเขียว</title>
    <?php require 'helper/source/icon.php'; ?>
    <link rel="stylesheet" href="helper/bootstrap/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/5134196601.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="helper/style.css">
</head>

<body>
    <?php require('helper/source/header.php'); ?>
    <main>
        <div class="container">
            <?php include './components/profileInfo.php'; ?>
            <hr>
            <div class="card-background mb-3" data-aos="zoom-in" data-aos-delay="150" data-aos-duration="1000">
                <h4 class="mb-5">แผนการเรียนที่สมัคร</h4>
                <div class="row mb-4">
                    <div class="col-md-5">
                        <div class="img-fluid">
                            <img class="img-fluid card-img-top2 info" src="helper/plan/<?php echo $student['plan_image']; ?>">
                        </div>
                    </div>
                    <div class="col-md-7 mt-3">
                        <p id="p-plan--info">แผนการเรียน<?php echo $student['plan_name']; ?> </p>
                        <ul>
                            <?php if ($student['plan_min_GPAX'] > 0) : ?>
                                <li>GPAX ตั้งแต่ <?php echo $student['plan_min_GPAX'] ?></li>
                            <?php endif; ?>
                            <?php if ($student['plan_min_GPA_MAT'] > 0) : ?>
                                <li>GPA วิชาคณิตศาสตร์ ตั้งแต่ <?php echo $student['plan_min_GPA_MAT'] ?></li>
                            <?php endif; ?>
                            <?php if ($student['plan_min_GPA_SCI'] > 0) : ?>
                                <li>GPA วิชาวิทยาศาสตร์ ตั้งแต่ <?php echo $student['plan_min_GPA_SCI'] ?></li>
                            <?php endif; ?>
                            <li>คะแนนความประพฤติไม่ติดลบ เกิน 100 คะแนน</li>
                            <li>ต้องไม่มีผลการเรียน ติด 0 ร มส มผ</li>
                        </ul>
                    </div>
                </div>
                
                <?php if (!empty($questionAnswers)) : ?>
                <div class="mt-4">
                    <hr>
                    <h5 class="mb-3">คำตอบของคุณ</h5>
                    <ul class="list-group">
                        <?php foreach ($questionAnswers as $index => $qa) : ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold"><?php echo ($index + 1) . '. ' . htmlspecialchars($qa['question']); ?></div>
                                        <span class="badge <?php echo $qa['answer'] == 1 ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $qa['answer'] == 1 ? 'ใช่' : 'ไม่ใช่'; ?>
                                        </span>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div>
                    <hr>
                    <p>หากต้องการเปลี่ยนแปลงแผนการเรียนให้นักเรียนส่งแบบฟอร์ม <a href="https://drive.google.com/file/d/17p5Unp99m6RwB53ny6BSSKGPxJb9n6fU/view?usp=sharing" target="_blank">นร.01.1</a> ที่ห้องวิชาการ โรงเรียนภูเขียว</p>
                </div>
            </div>
        </div>
    </main>
    <?php require 'helper/source/footer.php' ?>
    <?php require 'helper/source/script.php' ?>
    <script>
        AOS.init();
    </script>
</body>

</html>