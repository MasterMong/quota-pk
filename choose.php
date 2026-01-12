<?php
session_start();
require './config/db.php';
require './functions/Plan.php';
require './functions/Auth.php';
require './functions/Setting.php';

$settings = getSystemSettings($conn);

if (!isset($_SESSION['studentId'])) {
    header("Location: ./auth.php");
    exit();
}
$student = getStudent($conn, $_SESSION['studentId']);

if (!isset($_SESSION['agree'])) {
    header('location: ./account.php');
}

if (isset($student['plan']) && $student['plan'] !== '') {
    header("Location: ./info.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan = $_POST['plan'];
    
    // Handle question answers if provided
    if (isset($_POST['questions'])) {
        $questionAnswers = [];
        foreach ($_POST['questions'] as $questionId => $answer) {
            $questionAnswers[$questionId] = ($answer === 'true' || $answer === '1') ? 1 : 0;
        }
        saveQuestionAnswers($conn, $_SESSION['studentId'], $questionAnswers);
    }
    
    if (PickPlan($conn, $_SESSION['studentId'], $plan)) {
        header('location:./info.php');
        exit();
    }
}


$plans = getPlans($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกแผนการเรียน | โรงเรียนภูเขียว</title>
    <?php require 'helper/source/icon.php'; ?>
    <link rel="stylesheet" href="helper/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <script src="https://kit.fontawesome.com/5134196601.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="helper/style.css">
</head>

<body>
    <?php require('helper/source/header.php'); ?>
    <main>
        <div class="container">
            <div class="card-background text-center mb-3" data-aos="zoom-in" data-aos-duration="500">
                <div class="text-center">
                    <h3>กรุณาเลือกแผนการเรียนภายใน</h3>
                    <h3 class="text-danger" id="countdownDisplay"></h3>
                    <div style="color: #ff5656;" id="aboutTime">
                        <h4><i class="bi bi-heart-fill"></i></h4>
                        <h5 class="fw-bolder">สามารถตัดสินใจได้เพียง 1 ครั้ง</h5>
                    </div>

                    <?php
                    $registrationEndDate = $settings['registration_end_date'];
                    $registrationStartDate = $settings['registration_start_date'];
                    $registrationEnabled = $settings['registration_enabled'];
                    ?>

                    <script>
                        const registrationEndDate = new Date('<?php echo $registrationEndDate; ?>').getTime();
                        const registrationStartDate = new Date('<?php echo $registrationStartDate; ?>').getTime();
                        const registrationEnabled = <?php echo $registrationEnabled; ?>;

                        function startCountdown() {
                            const now = new Date().getTime();
                            const timeLeft = registrationEndDate - now;
                            const timeUntilStart = registrationStartDate - now;

                            if (registrationEnabled === 0 || timeUntilStart > 0) {
                                document.querySelector('h3').style.display = 'none';
                                document.querySelector('#aboutTime').style.display = 'none';
                                document.getElementById('countdownDisplay').innerHTML = "ปิดระบบรับสมัครแล้ว"; 
                            } else if (timeLeft < 0) {
                                // หากหมดเวลาการสมัคร
                                document.querySelector('h3').style.display = 'none';
                                document.querySelector('#aboutTime').style.display = 'none';
                                document.getElementById('countdownDisplay').innerHTML = "ปิดรับสมัครแล้ว";
                            } else {
                                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                                document.getElementById('countdownDisplay').innerHTML = `${days} วัน ${hours} ชั่วโมง ${minutes} นาที ${seconds} วินาที`;
                            }
                        }

                        startCountdown();

                        setInterval(startCountdown, 1000);
                    </script>
                </div>



            </div>
            <?php include './components/profileInfo.php'; ?>
            <hr>
            <form method="post" id="pickPlan" class="card-background" data-aos="zoom-in" data-aos-delay="400" data-aos-duration="1000">
                <h6 class="mb-5"><span>กรุณา<strong>เลือกแผนการเรียน</strong>ที่ต้องการสมัคร</span></h6>
                <div class="row">
                    <?php foreach ($plans as $plan) : ?>
                        <?php

                        $plan;
                        include './components/planCard.php';

                        ?>
                    <?php endforeach; ?>
            </form>
        </div>
    </main>
    
    <!-- Modal for Yes/No Questions -->
    <div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="questionModalLabel">คำถามเพิ่มเติม</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="questionContent">
                    <!-- Questions will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="submitAnswers">ยืนยันการเลือกแผนการเรียน</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php require 'helper/source/footer.php' ?>
    <?php require 'helper/source/script.php' ?>
    <script>
        AOS.init();
        
        let selectedPlanCode = null;
        let planQuestions = {};
        
        // Fetch questions for all plans on page load
        <?php foreach ($plans as $plan) : ?>
        <?php 
            $planQuestions = getPlanQuestions($conn, $plan['code']); 
            if (!empty($planQuestions)) :
        ?>
        planQuestions['<?php echo $plan['code']; ?>'] = <?php echo json_encode($planQuestions); ?>;
        <?php endif; ?>
        <?php endforeach; ?>
        
        function confirmForm(planCode) {
            selectedPlanCode = planCode;
            
            // Check if this plan has questions
            if (planQuestions[planCode] && planQuestions[planCode].length > 0) {
                // Show modal with questions
                showQuestionsModal(planCode);
            } else {
                // No questions, proceed with confirmation
                if (confirm('คุณแน่ใจหรือไม่ที่จะเลือกแผนการเรียนนี้?\nคุณจะสามารถเลือกได้เพียง 1 ครั้งเท่านั้น')) {
                    submitPlanChoice(planCode, {});
                }
            }
        }
        
        function showQuestionsModal(planCode) {
            const questions = planQuestions[planCode];
            let html = '<div class="mb-3"><p class="text-warning">กรุณาตอบคำถามต่อไปนี้ก่อนยืนยันการเลือกแผนการเรียน</p></div>';
            
            questions.forEach((question, index) => {
                html += `
                    <div class="mb-4 p-3 border rounded">
                        <p class="fw-bold mb-3">${index + 1}. ${question.question} ${question.required ? '<span class="text-danger">*</span>' : ''}</p>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="question_${question.id}" id="question_${question.id}_yes" value="1" ${question.required ? 'required' : ''}>
                            <label class="form-check-label" for="question_${question.id}_yes">
                                ใช่
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="question_${question.id}" id="question_${question.id}_no" value="0" ${question.required ? 'required' : ''}>
                            <label class="form-check-label" for="question_${question.id}_no">
                                ไม่ใช่
                            </label>
                        </div>
                    </div>
                `;
            });
            
            document.getElementById('questionContent').innerHTML = html;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('questionModal'));
            modal.show();
        }
        
        document.getElementById('submitAnswers')?.addEventListener('click', function() {
            const questions = planQuestions[selectedPlanCode];
            const answers = {};
            let allAnswered = true;
            
            // Collect answers
            questions.forEach(question => {
                const selectedAnswer = document.querySelector(`input[name="question_${question.id}"]:checked`);
                if (selectedAnswer) {
                    answers[question.id] = selectedAnswer.value;
                } else if (question.required) {
                    allAnswered = false;
                }
            });
            
            if (!allAnswered) {
                alert('กรุณาตอบคำถามที่จำเป็นทั้งหมด');
                return;
            }
            
            // Close modal and submit
            const modal = bootstrap.Modal.getInstance(document.getElementById('questionModal'));
            modal.hide();
            
            submitPlanChoice(selectedPlanCode, answers);
        });
        
        function submitPlanChoice(planCode, answers) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            
            // Add plan code
            const planInput = document.createElement('input');
            planInput.type = 'hidden';
            planInput.name = 'plan';
            planInput.value = planCode;
            form.appendChild(planInput);
            
            // Add answers
            for (const [questionId, answer] of Object.entries(answers)) {
                const answerInput = document.createElement('input');
                answerInput.type = 'hidden';
                answerInput.name = `questions[${questionId}]`;
                answerInput.value = answer;
                form.appendChild(answerInput);
            }
            
            document.body.appendChild(form);
            form.submit();
        }
    </script>

</body>

</html>