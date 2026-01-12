# คู่มือการใช้งานระบบคำถามแผนการเรียน (Plan Questions Feature)

## ภาพรวม
ฟีเจอร์นี้ช่วยให้สามารถเพิ่มคำถามแบบใช่/ไม่ใช่ (Yes/No) สำหรับแต่ละแผนการเรียน เมื่อนักเรียนเลือกแผนการเรียนที่มีคำถาม ระบบจะแสดงคำถามในรูปแบบ Modal ให้นักเรียนตอบก่อนยืนยันการเลือก

## การติดตั้ง

### 1. อัพเดทฐานข้อมูล
รันคำสั่ง SQL ต่อไปนี้เพื่อสร้างตารางใหม่:

```sql
-- สร้างตารางสำหรับเก็บคำถาม
CREATE TABLE IF NOT EXISTS `plan_questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `plan_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `required` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `plan_code` (`plan_code`),
  CONSTRAINT `fk_plan_questions_plan` FOREIGN KEY (`plan_code`) REFERENCES `plans` (`code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- สร้างตารางสำหรับเก็บคำตอบของนักเรียน
CREATE TABLE IF NOT EXISTS `student_question_answers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `question_id` int NOT NULL,
  `answer` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `fk_student_answers_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_student_answers_question` FOREIGN KEY (`question_id`) REFERENCES `plan_questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2. เพิ่มคำถามตัวอย่าง
```sql
-- ตัวอย่างคำถามสำหรับแผนวิทยาศาสตร์ - คณิตศาสตร์
INSERT INTO `plan_questions` (`plan_code`, `question`, `order`, `required`) 
VALUES ('sci', 'คุณมีความสนใจในการเรียนวิทยาศาสตร์และคณิตศาสตร์หรือไม่?', 1, 1);

INSERT INTO `plan_questions` (`plan_code`, `question`, `order`, `required`) 
VALUES ('sci', 'คุณพร้อมที่จะเรียนเนื้อหาที่ท้าทายมากขึ้นหรือไม่?', 2, 1);

-- ตัวอย่างคำถามสำหรับแผนภาษาอังกฤษ - คณิตศาสตร์
INSERT INTO `plan_questions` (`plan_code`, `question`, `order`, `required`) 
VALUES ('eng', 'คุณมีพื้นฐานภาษาอังกฤษที่ดีหรือไม่?', 1, 1);

-- ตัวอย่างคำถามสำหรับแผน MOU
INSERT INTO `plan_questions` (`plan_code`, `question`, `order`, `required`) 
VALUES ('mou', 'คุณสนใจทำงานในธุรกิจค้าปลีกหรือไม่?', 1, 1);
```

## วิธีการใช้งาน

### การเพิ่มคำถามใหม่
```sql
INSERT INTO `plan_questions` (`plan_code`, `question`, `order`, `required`) 
VALUES ('รหัสแผน', 'คำถามของคุณ', ลำดับ, 1);
```

**พารามิเตอร์:**
- `plan_code`: รหัสแผนการเรียน (เช่น 'sci', 'eng', 'mou')
- `question`: ข้อความคำถาม
- `order`: ลำดับการแสดงผล (เริ่มจาก 1)
- `required`: 
  - `1` = บังคับตอบ
  - `0` = ไม่บังคับ

### การแก้ไขคำถาม
```sql
UPDATE `plan_questions` 
SET `question` = 'คำถามใหม่', `order` = ลำดับใหม่ 
WHERE `id` = รหัสคำถาม;
```

### การลบคำถาม
```sql
DELETE FROM `plan_questions` WHERE `id` = รหัสคำถาม;
```

### การดูคำตอบของนักเรียน
```sql
SELECT 
    s.name AS student_name,
    pq.question,
    sqa.answer,
    sqa.created_at
FROM student_question_answers sqa
JOIN students s ON sqa.student_id = s.id
JOIN plan_questions pq ON sqa.question_id = pq.id
WHERE s.id = รหัสนักเรียน;
```

## โครงสร้างการทำงาน

### 1. เมื่อนักเรียนเลือกแผนการเรียน
- ระบบตรวจสอบว่าแผนการเรียนนั้นมีคำถามหรือไม่
- ถ้ามี: แสดง Modal พร้อมคำถาม
- ถ้าไม่มี: แสดงข้อความยืนยันแบบปกติ

### 2. การตอบคำถาม
- นักเรียนต้องตอบคำถามทั้งหมดที่มีค่า `required = 1`
- แต่ละคำถามมีตัวเลือก "ใช่" หรือ "ไม่ใช่"
- คำตอบจะถูกบันทึกใน `student_question_answers`

### 3. การบันทึกข้อมูล
- คำตอบจะถูกบันทึกพร้อมกับการเลือกแผนการเรียน
- ถ้านักเรียนเลือกแผนการเรียนใหม่ คำตอบเก่าจะถูกลบและบันทึกคำตอบใหม่

## ไฟล์ที่เกี่ยวข้อง

1. **quota.sql** - สคริปต์สร้างตารางฐานข้อมูล
2. **functions/Plan.php** - ฟังก์ชัน:
   - `getPlanQuestions()` - ดึงคำถามของแผนการเรียน
   - `saveQuestionAnswers()` - บันทึกคำตอบ
3. **choose.php** - หน้าเลือกแผนการเรียนพร้อม Modal คำถาม
4. **components/planCard.php** - การ์ดแสดงข้อมูลแผนการเรียน

## ตัวอย่างการใช้งาน

### สถานการณ์ที่ 1: แผนการเรียนไม่มีคำถาม
- นักเรียนกดปุ่ม "สมัคร"
- ระบบแสดงข้อความยืนยัน
- นักเรียนกด "ตกลง" เพื่อยืนยัน

### สถานการณ์ที่ 2: แผนการเรียนมีคำถาม
- นักเรียนกดปุ่ม "สมัคร"
- ระบบแสดง Modal พร้อมคำถาม
- นักเรียนตอบคำถามทั้งหมด
- นักเรียนกดปุ่ม "ยืนยันการเลือกแผนการเรียน"
- ระบบบันทึกคำตอบและเลือกแผนการเรียน

## หมายเหตุ
- คำถามจะแสดงตามลำดับที่กำหนดใน field `order`
- สามารถมีคำถามได้หลายข้อต่อหนึ่งแผนการเรียน
- คำตอบจะถูกบันทึกเป็น `1` (ใช่) หรือ `0` (ไม่ใช่)
- ถ้าไม่ต้องการคำถามสำหรับแผนการเรียนใด ก็ไม่ต้องเพิ่มข้อมูลในตาราง `plan_questions`
