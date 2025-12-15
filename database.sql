-- Create Database
CREATE DATABASE IF NOT EXISTS onlinecourse;
USE onlinecourse;

-- Table: users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    role INT NOT NULL DEFAULT 0 COMMENT '0: học viên, 1: giảng viên, 2: quản trị viên, 17: quản lý phê duyệt khóa học',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table: categories
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table: courses
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    instructor_id INT NOT NULL,
    category_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    duration_weeks INT NOT NULL,
    level VARCHAR(50) NOT NULL COMMENT 'Beginner, Intermediate, Advanced',
    image VARCHAR(255),
    status VARCHAR(50) DEFAULT 'draft' COMMENT 'draft, published, archived',
    approval_status VARCHAR(50) DEFAULT 'pending' COMMENT 'pending: chờ phê duyệt, approved: được phê duyệt, rejected: bị từ chối',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Table: enrollments
CREATE TABLE enrollments (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    student_id INT NOT NULL,
    enrolled_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) NOT NULL DEFAULT 'active'
        COMMENT 'active, completed, dropped',
    progress INT DEFAULT 0
        COMMENT 'completed (0-100)',

    CONSTRAINT chk_progress
        CHECK (progress >= 0 AND progress <= 100),

    FOREIGN KEY (course_id)
        REFERENCES courses(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    FOREIGN KEY (student_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    UNIQUE KEY unique_enrollment (course_id, student_id)
);


-- Table: lessons
CREATE TABLE lessons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT,
    video_url VARCHAR(255),
    `order` INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: materials
CREATE TABLE materials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lesson_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) COMMENT 'pdf, doc, ppt, v.v.',
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: lesson_progress (NEW)
CREATE TABLE lesson_progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    lesson_id INT NOT NULL,
    course_id INT NOT NULL,
    watched_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_completed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_lesson_progress (student_id, lesson_id)
);

-- Table: course_approvals (NEW)
-- Tracks all course approval requests from instructors to admins
CREATE TABLE course_approvals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL UNIQUE,
    instructor_id INT NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending' COMMENT 'pending: chờ phê duyệt, approved: được phê duyệt, rejected: bị từ chối',
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    reviewed_at DATETIME,
    reviewed_by INT,
    notes LONGTEXT COMMENT 'Notes or reason for approval/rejection',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Create Indexes for better query performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_courses_instructor ON courses(instructor_id);
CREATE INDEX idx_courses_category ON courses(category_id);
CREATE INDEX idx_courses_status ON courses(status);
CREATE INDEX idx_courses_approval_status ON courses(approval_status);
CREATE INDEX idx_enrollments_course ON enrollments(course_id);
CREATE INDEX idx_enrollments_student ON enrollments(student_id);
CREATE INDEX idx_enrollments_status ON enrollments(status);
CREATE INDEX idx_lessons_course ON lessons(course_id);
CREATE INDEX idx_lessons_order ON lessons(course_id, `order`);
CREATE INDEX idx_materials_lesson ON materials(lesson_id);
CREATE INDEX idx_lesson_progress_student ON lesson_progress(student_id);
CREATE INDEX idx_lesson_progress_course ON lesson_progress(course_id);
CREATE INDEX idx_lesson_progress_completed ON lesson_progress(student_id, course_id, is_completed);
CREATE INDEX idx_course_approvals_status ON course_approvals(status);
CREATE INDEX idx_course_approvals_instructor ON course_approvals(instructor_id);
CREATE INDEX idx_course_approvals_reviewed_by ON course_approvals(reviewed_by);

-- Insert default categories
INSERT INTO categories (name, description) VALUES
('Lập trình Web', 'Học lập trình web từ cơ bản đến nâng cao'),
('Khoa học dữ liệu', 'Phân tích dữ liệu, Machine Learning và AI'),
('Thiết kế đồ họa', 'Thiết kế UI/UX, Photoshop, Illustrator'),
('Kinh doanh', 'Quản trị kinh doanh, Marketing, Khởi nghiệp'),
('Ngoại ngữ', 'Tiếng Anh, Tiếng Nhật, Tiếng Trung'),
('Nhiếp ảnh', 'Kỹ thuật chụp ảnh và xử lý ảnh chuyên nghiệp');


-- 1. TẠO VÀ SỬ DỤNG DATABASE 3AE (Khớp với file config PHP của bạn)
CREATE DATABASE IF NOT EXISTS `3ae` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `3ae`;

-- 2. XÓA BẢNG CŨ NẾU CÓ (Theo thứ tự khóa ngoại để không lỗi)
DROP TABLE IF EXISTS course_approvals, lesson_progress, materials, lessons, enrollments, courses, categories, users;

-- 3. TẠO CÁC BẢNG

-- Table: users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    role INT NOT NULL DEFAULT 0 COMMENT '0: học viên, 1: giảng viên, 2: quản trị viên',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table: categories
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table: courses
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    instructor_id INT NOT NULL,
    category_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    duration_weeks INT NOT NULL,
    level VARCHAR(50) NOT NULL COMMENT 'Beginner, Intermediate, Advanced',
    image VARCHAR(255),
    status VARCHAR(50) DEFAULT 'draft' COMMENT 'draft, published, archived',
    approval_status VARCHAR(50) DEFAULT 'pending' COMMENT 'pending, approved, rejected',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Table: enrollments
CREATE TABLE enrollments (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    student_id INT NOT NULL,
    enrolled_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) NOT NULL DEFAULT 'active' COMMENT 'active, completed, dropped',
    progress INT DEFAULT 0 COMMENT 'completed (0-100)',
    
    -- Đã sửa: Bỏ CONSTRAINT name để tránh lỗi cú pháp trên XAMPP cũ
    CHECK (progress >= 0 AND progress <= 100),

    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_enrollment (course_id, student_id)
);

-- Table: lessons
CREATE TABLE lessons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT,
    video_url VARCHAR(255),
    `order` INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: materials
CREATE TABLE materials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lesson_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) COMMENT 'pdf, doc, ppt',
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: lesson_progress
CREATE TABLE lesson_progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    lesson_id INT NOT NULL,
    course_id INT NOT NULL,
    watched_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_completed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_lesson_progress (student_id, lesson_id)
);

-- Table: course_approvals
CREATE TABLE course_approvals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL, -- Đã xóa UNIQUE để cho phép lưu lịch sử duyệt nhiều lần
    instructor_id INT NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    reviewed_at DATETIME,
    reviewed_by INT,
    notes LONGTEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- 4. TẠO INDEX (Tối ưu tốc độ tìm kiếm)
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_courses_instructor ON courses(instructor_id);
CREATE INDEX idx_courses_category ON courses(category_id);
CREATE INDEX idx_courses_status ON courses(status);
CREATE INDEX idx_enrollments_course ON enrollments(course_id);
CREATE INDEX idx_enrollments_student ON enrollments(student_id);
CREATE INDEX idx_lessons_course ON lessons(course_id);
CREATE INDEX idx_lesson_progress_student ON lesson_progress(student_id);
CREATE INDEX idx_course_approvals_status ON course_approvals(status);

-- 5. NẠP DỮ LIỆU MẪU (Bắt buộc để chạy Web)

-- Danh mục
INSERT INTO categories (name, description) VALUES
('Lập trình Web', 'Học lập trình web từ cơ bản đến nâng cao'),
('Khoa học dữ liệu', 'Phân tích dữ liệu, Machine Learning và AI'),
('Thiết kế đồ họa', 'Thiết kế UI/UX, Photoshop, Illustrator'),
('Kinh doanh', 'Quản trị kinh doanh, Marketing, Khởi nghiệp'),
('Ngoại ngữ', 'Tiếng Anh, Tiếng Nhật, Tiếng Trung');

-- Tài khoản Admin
-- Username: admin | Password: 123456
INSERT INTO users (username, email, password, fullname, role) VALUES 
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản Trị Viên', 2);
