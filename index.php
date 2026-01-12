<?php
require './config/db.php';
$regulation_url = $_ENV['REGULATION_URL'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบสมัครนักเรียน | โรงเรียนภูเขียว</title>
    <?php require 'helper/source/icon.php'; ?>
    <?php require 'helper/source/link.php'; ?>
    <link rel="stylesheet" href="helper/home.css">
    <script src="https://kit.fontawesome.com/5134196601.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="d-flex d-lg-flex align-items-center align-items-lg-center" id="main--home">
        <div class="container text-center" data-aos-duration="1000">
            <div class="animation-container">
                <div class="lightning-container">
                    <div class="lightning white"></div>
                    <div class="lightning red"></div>
                </div>
                <div class="boom-container">
                    <div class="shape circle big white"></div>
                    <div class="shape circle white"></div>
                    <div class="shape triangle big yellow"></div>
                    <div class="shape disc white"></div>
                    <div class="shape triangle blue"></div>
                </div>
                <div class="boom-container second">
                    <div class="shape circle big white"></div>
                    <div class="shape circle white"></div>
                    <div class="shape disc white"></div>
                    <div class="shape triangle blue"></div>
                </div>
            </div>
            <img src="helper/media/pklogo.png" data-aos="zoom-in-up" data-aos-delay="100">
            <h1 data-aos="zoom-in-up" data-aos-delay="150">ระบบรับสมัครนักเรียนรอบโควตา</h1>
            <p data-aos="zoom-in-up" data-aos-delay="200">สำหรับนักเรียนชั้นมัธยมศึกษาปีที่ 3 โรงเรียนภูเขียว</p>
            <p data-aos="zoom-in-up" data-aos-delay="250"><?= $_ENV['ACADEMIC_PERIOD'] ?></p>
            <div data-aos="fade-up" data-aos-duration="1000">
                <div>
                    <a id="btn-regis" class="btn btn-primary animated-button" role="button" href="./auth.php">
                        <i class="far fa-edit"></i>&nbsp;สมัครเลย
                    </a>
                    <a id="btn-students" class="btn btn-warning animated-button" role="button" href="./students.php">
                        <i class="bi bi-person-badge"></i>&nbsp;รายชื่อผู้สมัคร
                    </a>
                </div>
                <div>
                    <a id="btn-statistics" class="btn btn-danger animated-button" role="button" href="./statistics.php">
                        <i class="bi bi-bar-chart-steps"></i>&nbsp;สถิติการสมัครเรียน
                    </a>
                    <a id="btn-regulations" class="btn btn-info animated-button" role="button" href="<?php echo $regulation_url; ?>" target="_blank">
                        <i class="fas fa-book"></i>&nbsp;อ่านระเบียบการ
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>
