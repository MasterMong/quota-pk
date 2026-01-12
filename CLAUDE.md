# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Thai-language student registration system for Phukhieo School's quota admission program. Students authenticate using their student ID and national ID (CID), then select from available academic plans (Science-Math, English-Math, or Business Management). The system validates student eligibility based on GPA requirements, behavior scores, and grade criteria.

## Development Commands

### Setup
```bash
# Install PHP dependencies
composer install

# Configure environment
cp .env.example .env
# Edit .env with your database credentials

# Import database schema and seed data
# Access: install.php?confirm=yes (creates tables and inserts default plans)
```

### Development
This is a traditional PHP application without a build process. Use a PHP development server:

```bash
# Run local development server
php -S localhost:8000

# Or use with specific host
php -S 127.0.0.1:8000
```

### Dependencies
- PHP 8.0+
- MySQL 8.0+
- Composer packages:
  - `vlucas/phpdotenv` (environment configuration)
  - `symfony/var-dumper` (dev debugging)

## Architecture

### Core Flow
1. **Authentication** (`auth.php`) - Validates student via student_id + CID lookup
2. **Account Agreement** (`account.php`) - Shows terms, stores agreement in session
3. **Plan Selection** (`choose.php`) - Displays available plans with eligibility checks
   - **NEW:** Plans can now have yes/no questions that students must answer before confirming
4. **Confirmation** (`info.php`) - Shows selected plan details
5. **Public Views** (`students.php`, `statistics.php`) - List applicants and show statistics

### Database Schema

**students** table:
- Stores student records imported from school system
- Key fields: `student_id`, `cid`, `name`, `room`, `student_number`
- Academic data: `GPAX`, `GPA_MAT`, `GPA_SCI`, `GPA_ENG`, `GPA_Fail`, `behavior_pass`
- Selected plan: `plan` (foreign key to plans.code)

**plans** table:
- Academic plans students can choose from
- Requirements: `min_GPAX`, `min_GPA_MAT`, `min_GPA_SCI`
- Flags: `allow_ungrade`, `allow_not_meet_req`, `allow_behavior_fail`
- Display: `name`, `img_cover`, `color`, `order`

**system_settings** table:
- Controls registration window: `registration_enabled`, `registration_start_date`, `registration_end_date`

### Key Functions (functions/)

**Auth.php**:
- `Auth($conn, $studentId, $cid)` - Authenticates student by matching both IDs
- `getStudent($conn, $studentId)` - Fetches student with joined plan details

**Plan.php**:
- `getPlans($conn)` - Returns all plans with student counts
- `PickPlan($conn, $studentId, $planCode)` - Assigns plan to student (validates plan exists first)
- `getPlanStatistics($conn)` - Groups student counts by plan for charts

**Student.php**:
- `getStudentsWithPlans($conn, $selectedRoom)` - Lists all applicants with optional room filter

**Setting.php**:
- `getSystemSettings($conn)` - Retrieves registration window configuration

### Session Management
- `$_SESSION['studentId']` stores authenticated student's DB id (not student_id)
- `$_SESSION['agree']` tracks terms acceptance
- Flow redirects: auth → account → choose → info

### Component System
- `components/planCard.php` - Renders plan card with eligibility validation (included in loop)
- `components/profileInfo.php` - Shows student information banner
- `helper/source/` - Contains reusable HTML fragments (header, footer, scripts, icons, links)

### Frontend
- Bootstrap 5 for styling
- AOS (Animate On Scroll) library for animations
- Font Awesome and Bootstrap Icons
- Custom animations in `helper/home.css`
- Client-side countdown timer in `choose.php` validates registration window

## Configuration

### Environment Variables (.env)
```
DB_HOST=127.0.0.1
DB_NAME=quota
DB_USER=root
DB_PASS=your_password

# Optional Symfony var-dumper server
VAR_DUMPER_FORMAT=server
VAR_DUMPER_SERVER=127.0.0.1:9912

# External regulation document URL
REGULATION_URL=https://phukhieo.ac.th/68ns1/
```

### Database Connection (config/db.php)
- Sets timezone to Asia/Bangkok
- Checks for vendor directory, dies with Thai error if missing
- Loads environment variables via phpdotenv
- Creates PDO connection with UTF-8MB4 charset
- Sets error mode to exceptions

## Important Implementation Details

### Student Eligibility Validation
Plan eligibility is checked in `components/planCard.php`:
1. Registration period must be active (`system_settings`)
2. Student's GPAX ≥ plan's `min_GPAX`
3. Student's GPA_MAT ≥ plan's `min_GPA_MAT`
4. Student's GPA_SCI ≥ plan's `min_GPA_SCI`
5. Student must have `behavior_pass` === 1 (no behavior demerits over 100)
6. Student must have `GPA_Fail` === 0 (no failing grades: 0, ร, มส, มผ)

All conditions must pass for the apply button to appear.

### Plan Selection Restrictions
- Students can only select **one plan** per session
- Changes require submitting form นร.01.1 to school office
- Selection is permanent once submitted (controlled by session + DB state)

### Vendor Directory Check
The application explicitly checks for the `vendor/` directory in `config/db.php:5-7` and displays a Thai error message instructing users to run `composer install` if missing. Never commit vendor/ to git.

### Thai Language
All user-facing text, error messages, and database content are in Thai. Maintain this convention when adding features or messages.

### Asset Paths
- Plan images: `helper/plan/` (referenced by `plans.img_cover`)
- School media: `helper/media/` (logos)
- Favicon files: `favicon/`
- Bootstrap files: `helper/bootstrap/`

## Database Seeding
The `quota.sql` file includes INSERT statements for:
- 3 default academic plans (sci, eng, mou)
- Default system_settings record with 2024-12-09 to 2024-12-11 registration window
- **NEW:** Tables for plan questions (`plan_questions`) and student answers (`student_question_answers`)

When working with the database, maintain UTF-8MB4 encoding throughout.

## Recent Updates (2026-01-12)

### Plan Questions Feature
A new feature has been added to allow plans to have yes/no questions that students must answer before confirming their plan selection.

**Key Changes:**
1. **Database Tables:**
   - `plan_questions`: Stores questions for each plan
   - `student_question_answers`: Stores student responses

2. **New Functions** (in `functions/Plan.php`):
   - `getPlanQuestions($conn, $planCode)`: Retrieves questions for a specific plan
   - `saveQuestionAnswers($conn, $studentId, $answers)`: Saves student's question responses

3. **UI Updates** (in `choose.php`):
   - Bootstrap modal to display questions
   - JavaScript to handle question flow and validation
   - Integration with existing plan selection process

4. **Usage:**
   - Questions are optional per plan (plans without questions work as before)
   - Questions can be marked as required or optional
   - Answers are saved before plan selection is confirmed
   - See `PLAN_QUESTIONS_GUIDE.md` for detailed documentation

**To add questions to a plan:**
```sql
INSERT INTO `plan_questions` (`plan_code`, `question`, `order`, `required`) 
VALUES ('sci', 'คุณมีความสนใจในวิทยาศาสตร์หรือไม่?', 1, 1);
```
