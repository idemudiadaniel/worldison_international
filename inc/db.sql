-- =========================
-- Branches table
-- =========================
CREATE TABLE branches (
  branch_id INT AUTO_INCREMENT PRIMARY KEY,
  branch_name VARCHAR(150) NOT NULL,
  branch_address TEXT,
  branch_city VARCHAR(100),
  branch_state VARCHAR(100),
  branch_country VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- Users table
-- =========================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  staff_id VARCHAR(50) UNIQUE,                -- Editable for now, later auto-generated
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  phone VARCHAR(20),

  -- Personal & Origin Details
  gender ENUM('Male','Female','Other'),
  dob DATE,
  marital_status VARCHAR(50),
  state_of_origin VARCHAR(100),
  lga_of_origin VARCHAR(100),
  country_of_origin VARCHAR(100),
  address TEXT,

  -- Work Related
  department VARCHAR(100),
  job_title VARCHAR(100),
  hire_date DATE,
  employment_type ENUM('Full-time','Part-time','Contract'),
  work_location VARCHAR(100),
  previous_work_experience TEXT,
  branch_id INT,                              -- FK referencing branches table

  -- Identification & Tax
  national_id VARCHAR(50),
  tax_id VARCHAR(50),
  bank_account VARCHAR(100),

  -- Next of Kin / Guarantor
  next_of_kin VARCHAR(150),
  next_of_kin_contact VARCHAR(50),
  guarantor_details TEXT,

  -- Documents
  academic_certificate VARCHAR(255),          -- file path
  other_certificate VARCHAR(255),             -- file path
  staff_documents VARCHAR(255),               -- general staff docs (zip/pdf)
  document_picture VARCHAR(255),              -- special picture for documents

  -- Authentication
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,             -- hashed password
  role ENUM('ceo','manager','accountant','editor','admin','staff') DEFAULT 'staff',
  profile_picture VARCHAR(255) DEFAULT 'assets/images/faces/default.png',

  -- Employment Status
  status ENUM('active','terminated') DEFAULT 'active',

  -- Delete workflow
  delete_status ENUM('active','pending','deleted') DEFAULT 'active',
  delete_request_by VARCHAR(100) NULL,
  delete_requested_at TIMESTAMP NULL,
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- =========================
-- Customers table
-- =========================
CREATE TABLE customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id VARCHAR(50) NOT NULL UNIQUE,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) DEFAULT NULL,
  phone VARCHAR(20) DEFAULT NULL,
  address VARCHAR(255) DEFAULT NULL,
  service_rendered VARCHAR(255) NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  date_served DATE NOT NULL,
  staff_in_charge VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- Attendance table
-- =========================
CREATE TABLE attendance (
  id INT AUTO_INCREMENT PRIMARY KEY,
  staff_id VARCHAR(50) NOT NULL,        -- link to users.staff_id
  clock_type ENUM('in','out') DEFAULT 'in',
  resumption_time DATETIME NOT NULL,
  photo_path VARCHAR(255) NOT NULL,
  location_lat DECIMAL(10,8),
  location_long DECIMAL(11,8),
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (staff_id) REFERENCES users(staff_id)
);

-- =========================
-- Blogs table
-- =========================
CREATE TABLE blogs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  category VARCHAR(100) DEFAULT 'Uncategorized',
  content TEXT NOT NULL,
  image_url VARCHAR(255) DEFAULT 'assets/images/default.png',
  author VARCHAR(100) DEFAULT 'iceHRMInternational',
  status VARCHAR(20) NOT NULL DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================
-- Comments table
-- =========================
CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  blog_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  comment TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE
);

-- =========================
-- Bookings table
-- =========================
CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  location VARCHAR(255) NOT NULL,
  services TEXT NOT NULL,        -- stores comma-separated services
  date_needed DATE DEFAULT NULL, -- optional service date
  urgent ENUM('Yes','No') DEFAULT 'No',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- Projects table
-- =========================
CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  category VARCHAR(100) DEFAULT 'General',
  description TEXT,
  image_url VARCHAR(255) DEFAULT NULL, 
  video_url VARCHAR(255) DEFAULT NULL,
  status ENUM('draft','published') DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE payroll (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    basic_salary DECIMAL(10,2) NOT NULL,
    allowances DECIMAL(10,2) DEFAULT 0,
    deductions DECIMAL(10,2) DEFAULT 0,
    days_absent INT DEFAULT 0,
    bonuses DECIMAL(10,2) DEFAULT 0,
    net_pay DECIMAL(10,2) GENERATED ALWAYS AS 
        (basic_salary + allowances + bonuses - deductions) STORED,
    pay_date DATE DEFAULT CURRENT_DATE,   -- ✅ Added column for payroll date
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);



CREATE TABLE terminations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reason ENUM('resignation','abscondment','retirement','misconduct','layoff') NOT NULL,
    termination_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);


CREATE TABLE landing_visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    country VARCHAR(100) DEFAULT 'Unknown',
    city VARCHAR(100) DEFAULT 'Unknown',
    user_agent VARCHAR(255),
    visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Departments
CREATE TABLE departments (
  dept_id INT AUTO_INCREMENT PRIMARY KEY,
  dept_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Questions
CREATE TABLE appraisal_questions (
  question_id INT AUTO_INCREMENT PRIMARY KEY,
  dept_id INT NOT NULL,
  title VARCHAR(100) NOT NULL,    -- dropdown title
  content TEXT NOT NULL,          -- question text/content
  FOREIGN KEY (dept_id) REFERENCES departments(dept_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE appraisal_answers (
  answer_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  question_id INT NOT NULL,
  answer_text TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (question_id) REFERENCES appraisal_questions(question_id) ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE appraisal_comments (
  comment_id INT AUTO_INCREMENT PRIMARY KEY,
  answer_id INT NOT NULL,
  admin_id INT NOT NULL,
  comment TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (answer_id) REFERENCES appraisal_answers(answer_id) ON DELETE CASCADE,
  FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);


INSERT INTO departments (dept_name) VALUES
('General Staff'),
('Administrative Officer'),
('Operations Supervisor'),
('Cleaning Team Lead'),
('Fumigation Officer'),
('Safety Officer'),
('Customer Relations Officer'),
('Procurement Officer'),
('Training Instructor'),
('Storekeeper / Inventory Officer'),
('Technical / Maintenance Officer'),
('HR Assistant'),
('Accountant / Finance Officer'),
('Field Supervisor'),
('Receptionist / Front Desk Officer'),
('Driver / Logistics Staff'),
('Marketing / Business Development Officer'),
('Fire Extinguisher Technician'),
('IT / Digital Support Staff'),
('General Cleaner / Field Staff');

