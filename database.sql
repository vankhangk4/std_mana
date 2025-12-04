# Database Queries Documentation

**Database:** onlinecourse

## Database Structure

### Tables Overview
1. **users** - User accounts with role-based access control
2. **categories** - Course categories for organization
3. **courses** - Main courses table with approval workflow
4. **enrollments** - Tracks student enrollment in courses
5. **lessons** - Stores lessons within courses
6. **materials** - Stores course materials (PDFs, documents, etc.)
7. **lesson_progress** - Tracks student progress in lessons
8. **course_approvals** - Tracks course approval requests

### Indexes Created
- `idx_users_email`, `idx_users_username`, `idx_users_role`
- `idx_courses_instructor`, `idx_courses_category`, `idx_courses_status`, `idx_courses_approval_status`
- `idx_enrollments_course`, `idx_enrollments_student`, `idx_enrollments_status`
- `idx_lessons_course`, `idx_lessons_order`
- `idx_materials_lesson`
- `idx_lesson_progress_student`, `idx_lesson_progress_course`, `idx_lesson_progress_completed`
- `idx_course_approvals_status`, `idx_course_approvals_instructor`, `idx_course_approvals_reviewed_by`

---

## 1. USERS Table
Stores user accounts with role-based access control

**Table Structure:**
- `id` (INT, PK, Auto-increment)
- `username` (VARCHAR 255, UNIQUE)
- `email` (VARCHAR 255, UNIQUE)
- `password` (VARCHAR 255)
- `fullname` (VARCHAR 255)
- `role` (INT, Default: 0) - 0: Student, 1: Instructor, 2: Admin, 17: Course Reviewer
- `created_at` (DATETIME, Default: CURRENT_TIMESTAMP)

**Common Queries:**

```sql
-- Get all users
SELECT * FROM users ORDER BY created_at DESC;

-- Get users by role
SELECT * FROM users WHERE role = 0 ORDER BY created_at DESC;  -- Students
SELECT * FROM users WHERE role = 1 ORDER BY created_at DESC;  -- Instructors
SELECT * FROM users WHERE role = 2 ORDER BY created_at DESC;  -- Admins
SELECT * FROM users WHERE role = 17 ORDER BY created_at DESC; -- Course Reviewers

-- Count users by role
SELECT 
    role,
    CASE 
        WHEN role = 0 THEN 'Student'
        WHEN role = 1 THEN 'Instructor'
        WHEN role = 2 THEN 'Admin'
        WHEN role = 17 THEN 'Course Reviewer'
    END as role_name,
    COUNT(*) as count
FROM users
GROUP BY role;

-- Get user by email/username
SELECT * FROM users WHERE email = 'user@example.com';
SELECT * FROM users WHERE username = 'username';

-- Get user by ID
SELECT * FROM users WHERE id = 1;

-- Get instructor info with course count
SELECT 
    u.id,
    u.username,
    u.email,
    u.fullname,
    COUNT(c.id) as total_courses
FROM users u
LEFT JOIN courses c ON u.id = c.instructor_id
WHERE u.role = 1
GROUP BY u.id
ORDER BY total_courses DESC;

-- Create new user
INSERT INTO users (username, email, password, fullname, role) 
VALUES ('username', 'email@example.com', 'hashed_password', 'Full Name', 0);

-- Update user
UPDATE users 
SET fullname = 'New Name', role = 1 
WHERE id = 1;

-- Delete user
DELETE FROM users WHERE id = 1;
```

---

## 2. CATEGORIES Table
Stores course categories for organization

**Default Categories Included:**
1. Lập trình Web - Học lập trình web từ cơ bản đến nâng cao
2. Khoa học dữ liệu - Phân tích dữ liệu, Machine Learning và AI
3. Thiết kế đồ họa - Thiết kế UI/UX, Photoshop, Illustrator
4. Kinh doanh - Quản trị kinh doanh, Marketing, Khởi nghiệp
5. Ngoại ngữ - Tiếng Anh, Tiếng Nhật, Tiếng Trung
6. Nhiếp ảnh - Kỹ thuật chụp ảnh và xử lý ảnh chuyên nghiệp

**Table Structure:**
- `id` (INT, PK, Auto-increment)
- `name` (VARCHAR 255)
- `description` (TEXT)
- `created_at` (DATETIME, Default: CURRENT_TIMESTAMP)

**Common Queries:**

```sql
-- Get all categories
SELECT * FROM categories ORDER BY name ASC;

-- Get category by ID
SELECT * FROM categories WHERE id = 1;

-- Get category with course count
SELECT 
    c.id,
    c.name,
    c.description,
    COUNT(co.id) as total_courses
FROM categories c
LEFT JOIN courses co ON c.id = co.category_id
GROUP BY c.id
ORDER BY total_courses DESC;

-- Create category
INSERT INTO categories (name, description) 
VALUES ('Programming', 'Learn programming languages');

-- Update category
UPDATE categories 
SET name = 'Web Development', description = 'Learn web development' 
WHERE id = 1;

-- Delete category
DELETE FROM categories WHERE id = 1;
```

---

## 3. COURSES Table
Main courses table with approval workflow

**Table Structure:**
- `id` (INT, PK, Auto-increment)
- `title` (VARCHAR 255)
- `description` (TEXT)
- `instructor_id` (INT, FK → users.id)
- `category_id` (INT, FK → categories.id)
- `price` (DECIMAL 10,2)
- `duration_weeks` (INT)
- `level` (VARCHAR 50) - Beginner, Intermediate, Advanced
- `image` (VARCHAR 255)
- `status` (VARCHAR 50) - draft, published, archived
- `approval_status` (VARCHAR 50) - pending, approved, rejected
- `created_at` (DATETIME)
- `updated_at` (DATETIME)

**Common Queries:**

```sql
-- Get all published courses
SELECT c.*, u.fullname as instructor_name, cat.name as category_name 
FROM courses c
LEFT JOIN users u ON c.instructor_id = u.id
LEFT JOIN categories cat ON c.category_id = cat.id
WHERE c.status = 'published' AND c.approval_status = 'approved'
ORDER BY c.created_at DESC;

-- Get courses by instructor
SELECT * FROM courses 
WHERE instructor_id = 1 
ORDER BY created_at DESC;

-- Get courses with approval status
SELECT 
    c.id,
    c.title,
    u.fullname as instructor_name,
    c.status,
    c.approval_status,
    ca.status as approval_request_status
FROM courses c
LEFT JOIN users u ON c.instructor_id = u.id
LEFT JOIN course_approvals ca ON c.id = ca.course_id
ORDER BY c.created_at DESC;

-- Get pending approval courses
SELECT c.*, u.fullname as instructor_name, cat.name as category_name
FROM courses c
LEFT JOIN users u ON c.instructor_id = u.id
LEFT JOIN categories cat ON c.category_id = cat.id
WHERE c.approval_status = 'pending'
ORDER BY c.created_at DESC;

-- Get rejected courses
SELECT c.*, u.fullname as instructor_name, ca.notes as rejection_reason
FROM courses c
LEFT JOIN users u ON c.instructor_id = u.id
LEFT JOIN course_approvals ca ON c.id = ca.course_id
WHERE c.approval_status = 'rejected'
ORDER BY c.created_at DESC;

-- Count courses by status
SELECT 
    status,
    approval_status,
    COUNT(*) as count
FROM courses
GROUP BY status, approval_status;

-- Get course details with instructor and category
SELECT 
    c.id,
    c.title,
    c.description,
    c.price,
    c.level,
    c.duration_weeks,
    u.id as instructor_id,
    u.fullname as instructor_name,
    cat.name as category_name,
    COUNT(DISTINCT e.id) as total_enrolled,
    COUNT(DISTINCT l.id) as total_lessons,
    c.created_at
FROM courses c
LEFT JOIN users u ON c.instructor_id = u.id
LEFT JOIN categories cat ON c.category_id = cat.id
LEFT JOIN enrollments e ON c.id = e.course_id
LEFT JOIN lessons l ON c.id = l.course_id
WHERE c.id = 1
GROUP BY c.id;

-- Get course by ID
SELECT * FROM courses WHERE id = 1;

-- Create course (draft)
INSERT INTO courses (title, description, instructor_id, category_id, price, duration_weeks, level, status, approval_status)
VALUES ('Course Title', 'Description', 1, 1, 99.99, 8, 'Beginner', 'draft', 'pending');

-- Update course
UPDATE courses 
SET title = 'New Title', description = 'New Description', status = 'published' 
WHERE id = 1;

-- Change approval status
UPDATE courses 
SET approval_status = 'approved' 
WHERE id = 1;

-- Delete course
DELETE FROM courses WHERE id = 1;
```

---

## 4. ENROLLMENTS Table
Tracks student enrollment in courses

**Table Structure:**
- `id` (INT, PK, Auto-increment)
- `course_id` (INT, FK → courses.id)
- `student_id` (INT, FK → users.id)
- `enrolled_date` (DATETIME, Default: CURRENT_TIMESTAMP)
- `status` (VARCHAR 50) - active, completed, dropped
- `progress` (INT, Default: 0) - 0-100
- UNIQUE constraint on (course_id, student_id)

**Common Queries:**

```sql
-- Get enrollments for a student
SELECT 
    e.id,
    e.course_id,
    c.title as course_title,
    c.price,
    u.fullname as instructor_name,
    e.progress,
    e.status,
    e.enrolled_date
FROM enrollments e
LEFT JOIN courses c ON e.course_id = c.id
LEFT JOIN users u ON c.instructor_id = u.id
WHERE e.student_id = 1
ORDER BY e.enrolled_date DESC;

-- Get students enrolled in a course
SELECT 
    e.student_id,
    u.fullname,
    u.email,
    e.progress,
    e.status,
    e.enrolled_date
FROM enrollments e
LEFT JOIN users u ON e.student_id = u.id
WHERE e.course_id = 1
ORDER BY e.enrolled_date DESC;

-- Get course statistics
SELECT 
    c.id,
    c.title,
    COUNT(e.id) as total_enrolled,
    SUM(CASE WHEN e.status = 'active' THEN 1 ELSE 0 END) as active_students,
    SUM(CASE WHEN e.status = 'completed' THEN 1 ELSE 0 END) as completed_students,
    AVG(e.progress) as average_progress
FROM courses c
LEFT JOIN enrollments e ON c.id = e.course_id
GROUP BY c.id;

-- Check if student enrolled in course
SELECT * FROM enrollments 
WHERE course_id = 1 AND student_id = 1;

-- Enroll student in course
INSERT INTO enrollments (course_id, student_id, status, progress)
VALUES (1, 1, 'active', 0);

-- Update enrollment progress
UPDATE enrollments 
SET progress = 50 
WHERE course_id = 1 AND student_id = 1;

-- Update enrollment status
UPDATE enrollments 
SET status = 'completed' 
WHERE course_id = 1 AND student_id = 1;

-- Get student progress in all courses
SELECT 
    u.id,
    u.fullname,
    c.title as course_title,
    e.progress,
    e.status
FROM users u
LEFT JOIN enrollments e ON u.id = e.student_id
LEFT JOIN courses c ON e.course_id = c.id
WHERE u.id = 1
ORDER BY e.enrolled_date DESC;

-- Delete enrollment
DELETE FROM enrollments WHERE course_id = 1 AND student_id = 1;
```

---

## 5. LESSONS Table
Stores lessons within courses

**Table Structure:**
- `id` (INT, PK, Auto-increment)
- `course_id` (INT, FK → courses.id)
- `title` (VARCHAR 255)
- `content` (LONGTEXT)
- `video_url` (VARCHAR 255)
- `order` (INT)
- `created_at` (DATETIME)

**Common Queries:**

```sql
-- Get all lessons in a course
SELECT * FROM lessons 
WHERE course_id = 1 
ORDER BY `order` ASC;

-- Get lesson by ID
SELECT * FROM lessons WHERE id = 1;

-- Get lesson with course info
SELECT 
    l.id,
    l.title,
    l.content,
    l.video_url,
    l.`order`,
    c.title as course_title
FROM lessons l
LEFT JOIN courses c ON l.course_id = c.id
WHERE l.id = 1;

-- Count lessons per course
SELECT 
    c.id,
    c.title,
    COUNT(l.id) as total_lessons
FROM courses c
LEFT JOIN lessons l ON c.id = l.course_id
GROUP BY c.id
ORDER BY total_lessons DESC;

-- Get lessons with video
SELECT * FROM lessons 
WHERE course_id = 1 AND video_url IS NOT NULL AND video_url != ''
ORDER BY `order` ASC;

-- Create lesson
INSERT INTO lessons (course_id, title, content, video_url, `order`)
VALUES (1, 'Lesson 1: Introduction', 'Content here', 'https://youtube.com/embed/abc123', 1);

-- Update lesson
UPDATE lessons 
SET title = 'New Title', content = 'New Content' 
WHERE id = 1;

-- Delete lesson
DELETE FROM lessons WHERE id = 1;
```

---

## 6. MATERIALS Table
Stores course materials (PDFs, documents, etc.)

**Table Structure:**
- `id` (INT, PK, Auto-increment)
- `lesson_id` (INT, FK → lessons.id)
- `filename` (VARCHAR 255)
- `file_path` (VARCHAR 255)
- `file_type` (VARCHAR 50) - pdf, doc, ppt, etc.
- `uploaded_at` (DATETIME)

**Common Queries:**

```sql
-- Get all materials for a lesson
SELECT * FROM materials 
WHERE lesson_id = 1 
ORDER BY uploaded_at DESC;

-- Get materials for a course
SELECT 
    m.id,
    m.filename,
    m.file_path,
    m.file_type,
    l.title as lesson_title,
    m.uploaded_at
FROM materials m
LEFT JOIN lessons l ON m.lesson_id = l.id
WHERE l.course_id = 1
ORDER BY m.uploaded_at DESC;

-- Count materials per lesson
SELECT 
    l.id,
    l.title,
    COUNT(m.id) as total_materials
FROM lessons l
LEFT JOIN materials m ON l.id = m.lesson_id
GROUP BY l.id;

-- Get materials by type
SELECT * FROM materials 
WHERE file_type = 'pdf' 
ORDER BY uploaded_at DESC;

-- Create material
INSERT INTO materials (lesson_id, filename, file_path, file_type)
VALUES (1, 'guide.pdf', '/uploads/materials/guide.pdf', 'pdf');

-- Delete material
DELETE FROM materials WHERE id = 1;
```

---

## 7. LESSON_PROGRESS Table
Tracks student progress in lessons (NEW)

**Table Structure:**
- `id` (INT, PK, Auto-increment)
- `student_id` (INT, FK → users.id)
- `lesson_id` (INT, FK → lessons.id)
- `course_id` (INT, FK → courses.id)
- `watched_at` (DATETIME)
- `is_completed` (BOOLEAN, Default: FALSE)
- UNIQUE constraint on (student_id, lesson_id)

**Common Queries:**

```sql
-- Get student's lesson progress in a course
SELECT 
    l.id,
    l.title,
    l.`order`,
    lp.is_completed,
    lp.watched_at
FROM lessons l
LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id AND lp.student_id = 1
WHERE l.course_id = 1
ORDER BY l.`order` ASC;

-- Get completed lessons for a student
SELECT 
    l.id,
    l.title,
    lp.watched_at,
    lp.is_completed
FROM lesson_progress lp
LEFT JOIN lessons l ON lp.lesson_id = l.id
WHERE lp.student_id = 1 AND lp.is_completed = TRUE
ORDER BY lp.watched_at DESC;

-- Get course completion percentage
SELECT 
    COUNT(DISTINCT l.id) as total_lessons,
    COUNT(DISTINCT CASE WHEN lp.is_completed THEN l.id END) as completed_lessons,
    ROUND(COUNT(DISTINCT CASE WHEN lp.is_completed THEN l.id END) * 100.0 / COUNT(DISTINCT l.id), 2) as completion_percentage
FROM lessons l
LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id AND lp.student_id = 1
WHERE l.course_id = 1;

-- Mark lesson as completed
INSERT INTO lesson_progress (student_id, lesson_id, course_id, is_completed, watched_at)
VALUES (1, 1, 1, TRUE, NOW())
ON DUPLICATE KEY UPDATE is_completed = TRUE, watched_at = NOW();

-- Get lessons watched by student
SELECT 
    l.id,
    l.title,
    lp.watched_at
FROM lesson_progress lp
LEFT JOIN lessons l ON lp.lesson_id = l.id
WHERE lp.student_id = 1 AND lp.course_id = 1
ORDER BY lp.watched_at DESC;
```

---

## 8. COURSE_APPROVALS Table
Tracks course approval requests (NEW) - Two-table approval system

**Table Structure:**
- `id` (INT, PK, Auto-increment)
- `course_id` (INT, FK → courses.id, UNIQUE)
- `instructor_id` (INT, FK → users.id)
- `status` (VARCHAR 50) - pending, approved, rejected
- `submitted_at` (DATETIME)
- `reviewed_at` (DATETIME)
- `reviewed_by` (INT, FK → users.id)
- `notes` (LONGTEXT)
- `created_at` (DATETIME)

**Common Queries:**

```sql
-- Get all pending approval requests
SELECT 
    ca.id,
    ca.course_id,
    c.title as course_title,
    u.fullname as instructor_name,
    u.email as instructor_email,
    ca.status,
    ca.submitted_at
FROM course_approvals ca
LEFT JOIN courses c ON ca.course_id = c.id
LEFT JOIN users u ON ca.instructor_id = u.id
WHERE ca.status = 'pending'
ORDER BY ca.submitted_at ASC;

-- Get pending approvals with course details
SELECT 
    ca.id,
    ca.course_id,
    c.title,
    c.description,
    c.price,
    c.level,
    u.fullname as instructor_name,
    cat.name as category_name,
    ca.submitted_at
FROM course_approvals ca
LEFT JOIN courses c ON ca.course_id = c.id
LEFT JOIN users u ON ca.instructor_id = u.id
LEFT JOIN categories cat ON c.category_id = cat.id
WHERE ca.status = 'pending'
ORDER BY ca.submitted_at ASC;

-- Get approval history
SELECT 
    ca.id,
    c.title as course_title,
    u.fullname as instructor_name,
    ca.status,
    admin.fullname as reviewed_by_name,
    ca.reviewed_at,
    ca.notes
FROM course_approvals ca
LEFT JOIN courses c ON ca.course_id = c.id
LEFT JOIN users u ON ca.instructor_id = u.id
LEFT JOIN users admin ON ca.reviewed_by = admin.id
WHERE ca.status IN ('approved', 'rejected')
ORDER BY ca.reviewed_at DESC;

-- Get approval stats
SELECT 
    ca.status,
    COUNT(*) as count
FROM course_approvals ca
GROUP BY ca.status;

-- Check if course has pending approval
SELECT * FROM course_approvals 
WHERE course_id = 1 AND status = 'pending';

-- Create approval request (auto on publish)
INSERT INTO course_approvals (course_id, instructor_id, status, submitted_at)
VALUES (1, 1, 'pending', NOW());

-- Approve course
UPDATE course_approvals 
SET status = 'approved', reviewed_by = 2, reviewed_at = NOW(), notes = 'Looks good'
WHERE course_id = 1;

-- Reject course
UPDATE course_approvals 
SET status = 'rejected', reviewed_by = 2, reviewed_at = NOW(), notes = 'Please revise content'
WHERE course_id = 1;

-- Get approved courses count by reviewer
SELECT 
    admin.fullname as reviewer_name,
    COUNT(*) as approved_count
FROM course_approvals ca
LEFT JOIN users admin ON ca.reviewed_by = admin.id
WHERE ca.status = 'approved'
GROUP BY ca.reviewed_by
ORDER BY approved_count DESC;

-- Get courses awaiting approval by instructor
SELECT 
    u.fullname as instructor_name,
    COUNT(*) as pending_count
FROM course_approvals ca
LEFT JOIN users u ON ca.instructor_id = u.id
WHERE ca.status = 'pending'
GROUP BY ca.instructor_id
ORDER BY pending_count DESC;
```

---

## Summary of Key Relationships

```
users (1) ──→ (many) courses [instructor_id]
users (1) ──→ (many) course_approvals [instructor_id]
users (1) ──→ (many) course_approvals [reviewed_by]
users (1) ──→ (many) enrollments [student_id]
users (1) ──→ (many) lesson_progress [student_id]

categories (1) ──→ (many) courses
courses (1) ──→ (many) enrollments
courses (1) ──→ (many) lessons
courses (1) ──→ (1) course_approvals

lessons (1) ──→ (many) materials
lessons (1) ──→ (many) lesson_progress
```

---

## Important Notes

1. **Approval Workflow**: Courses use both `course_approvals` and `courses.approval_status` columns:
   - When instructor publishes (status='published'), a pending approval request is created in `course_approvals`
   - Admin updates `approval_status` in courses table
   - The `course_approvals` table tracks full history with notes/reasons

2. **Lessons Progress**: Tracks individual lesson completion, not just course progress

3. **Indexes**: All foreign keys and frequently searched fields have indexes for performance

4. **Unique Constraints**:
   - `users.username`, `users.email` (prevent duplicates)
   - `course_approvals.course_id` (one approval per course)
   - `enrollments` and `lesson_progress` (prevent duplicate records)
