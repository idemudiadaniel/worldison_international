<?php

/**
 * Ensure the blog schema exists before using blog pages.
 *
 * @param mysqli $conn
 * @return bool
 */
function ensureBlogSchema(mysqli $conn): bool {
    try {
        $dbName = '';
        $result = $conn->query("SELECT DATABASE() AS dbname");
        if ($result) {
            $row = $result->fetch_assoc();
            $dbName = $row['dbname'] ?? '';
            $result->free();
        }

        if ($dbName === '') {
            return false;
        }

        $tables = ['blogs', 'comments'];
        foreach ($tables as $table) {
            $stmt = $conn->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = ? AND table_name = ? LIMIT 1");
            $stmt->bind_param('ss', $dbName, $table);
            $stmt->execute();
            $stmt->store_result();
            $exists = $stmt->num_rows > 0;
            $stmt->close();

            if (!$exists) {
                if ($table === 'blogs') {
                    $conn->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS blogs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  category VARCHAR(100) NOT NULL DEFAULT 'General',
  author VARCHAR(150) NOT NULL DEFAULT 'Worldison International',
  content LONGTEXT,
  image_url VARCHAR(255) DEFAULT NULL,
  cover_url VARCHAR(255) DEFAULT NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
                    );
                }

                if ($table === 'comments') {
                    $conn->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS comments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  blog_id INT UNSIGNED NOT NULL,
  name VARCHAR(150) NOT NULL,
  comment TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
                    );
                }
            }
        }

        return true;
    } catch (mysqli_sql_exception $e) {
        error_log('Blog schema check failed: ' . $e->getMessage());
        return false;
    }
}
