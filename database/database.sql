-- Base de datos fusionada: gestión_biblioteca_mvc
-- Combina la estructura existente con el sistema de usuarios MVC

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `gestion_biblioteca_mvc` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `gestion_biblioteca_mvc`;

-- ============================================
-- TABLA DE USUARIOS DEL SISTEMA (MVC)
-- ============================================
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'librarian', 'user') NOT NULL DEFAULT 'librarian',
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLAS DEL SISTEMA DE BIBLIOTECA
-- ============================================

-- Tabla: categorias (actualizada)
CREATE TABLE `categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: books (mejorada)
CREATE TABLE `books` (
  `isbn` VARCHAR(17) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(150) NOT NULL,
  `publisher` VARCHAR(100) NOT NULL,
  `publication_year` YEAR DEFAULT NULL,
  `pages` SMALLINT UNSIGNED DEFAULT NULL,
  `synopsis` TEXT DEFAULT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `language` VARCHAR(50) DEFAULT 'Español',
  `available_copies` INT NOT NULL DEFAULT 1,
  `total_copies` INT NOT NULL DEFAULT 1,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`isbn`),
  INDEX `idx_title` (`title`),
  INDEX `idx_author` (`author`),
  INDEX `idx_publisher` (`publisher`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: book_category (relación muchos a muchos)
CREATE TABLE `book_category` (
  `isbn` VARCHAR(17) NOT NULL,
  `category_id` INT(11) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`isbn`, `category_id`),
  FOREIGN KEY (`isbn`) REFERENCES `books` (`isbn`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: members (socios)
CREATE TABLE `members` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `member_code` VARCHAR(20) NOT NULL UNIQUE,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `birth_date` DATE DEFAULT NULL,
  `max_loans` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_member_code` (`member_code`),
  INDEX `idx_full_name` (`first_name`, `last_name`),
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: copies (copias físicas)
CREATE TABLE `copies` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `isbn` VARCHAR(17) NOT NULL,
  `copy_code` VARCHAR(50) NOT NULL UNIQUE,
  `status` ENUM('available', 'borrowed', 'reserved', 'maintenance', 'lost') NOT NULL DEFAULT 'available',
  `location` VARCHAR(100) DEFAULT 'General Shelf',
  `notes` TEXT DEFAULT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`isbn`) REFERENCES `books` (`isbn`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_isbn` (`isbn`),
  INDEX `idx_status` (`status`),
  INDEX `idx_copy_code` (`copy_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: loans (préstamos)
CREATE TABLE `loans` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `member_id` INT(11) NOT NULL,
  `copy_id` INT(11) NOT NULL,
  `loan_date` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `return_date` DATE DEFAULT NULL,
  `loan_days` TINYINT UNSIGNED NOT NULL DEFAULT 15,
  `status` ENUM('active', 'returned', 'overdue', 'lost') NOT NULL DEFAULT 'active',
  `fine` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (`copy_id`) REFERENCES `copies` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_member` (`member_id`),
  INDEX `idx_copy` (`copy_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_dates` (`loan_date`, `due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATOS INICIALES
-- ============================================

-- Usuarios del sistema (contraseña: 123456 para ambos)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Administrador Principal', 'admin@biblioteca.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Bibliotecario', 'librarian@biblioteca.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'librarian'),
('Usuario de Prueba', 'user_test@example.com', '$2y$10$0IErDRhTvLfBmFt/L8aqP.tcGvbZgiiPvCIJrMuz6P8JhuwRcT8Q6', 'user');

-- Categorías
INSERT INTO `categories` (`name`, `description`) VALUES
('Ciencia Ficción', 'Novelas de ciencia ficción y fantasía'),
('Literatura', 'Literatura clásica y contemporánea'),
('Ciencia', 'Libros científicos y técnicos'),
('Historia', 'Libros históricos y biografías'),
('Informática', 'Programación y tecnología');

-- Libros
INSERT INTO `books` (`isbn`, `title`, `author`, `publisher`, `publication_year`, `available_copies`, `total_copies`, `synopsis`) VALUES
('9788499086111', 'Dune', 'Frank Herbert', 'Ediciones B', 1965, 3, 3, 'Novela de ciencia ficción épica ambientada en el desértico planeta Arrakis'),
('9788466338141', 'Cien años de soledad', 'Gabriel García Márquez', 'Alfaguara', 1967, 2, 2, 'Obra maestra del realismo mágico'),
('9780141439518', '1984', 'George Orwell', 'Penguin Books', 1949, 1, 1, 'Novela distópica sobre vigilancia y control totalitario'),
('9788408185067', 'El señor de los anillos', 'J.R.R. Tolkien', 'Minotauro', 1954, 2, 2, 'Trilogía épica de fantasía');

-- Relaciones libro-categoría
INSERT INTO `book_category` (`isbn`, `category_id`) VALUES
('9788499086111', 1),  -- Dune -> Ciencia Ficción
('9788466338141', 2),  -- Cien años... -> Literatura
('9780141439518', 1),  -- 1984 -> Ciencia Ficción
('9780141439518', 3),  -- 1984 -> Ciencia
('9788408185067', 1);  -- El señor... -> Ciencia Ficción

-- Copias
INSERT INTO `copies` (`isbn`, `copy_code`, `status`) VALUES
('9788499086111', 'DUNE-001', 'available'),
('9788499086111', 'DUNE-002', 'available'),
('9788499086111', 'DUNE-003', 'available'),
('9788466338141', 'CIEN-001', 'available'),
('9788466338141', 'CIEN-002', 'available'),
('9780141439518', '1984-001', 'available'),
('9788408185067', 'ANILLOS-001', 'available'),
('9788408185067', 'ANILLOS-002', 'available');

-- Socios
INSERT INTO `members` (`member_code`, `first_name`, `last_name`, `email`, `phone`, `address`) VALUES
('MEM001', 'Pedro', 'García', 'pedro.garcia@email.com', '555-0101', 'Calle Principal 123'),
('MEM002', 'Ana', 'Martínez', 'ana.martinez@email.com', '555-0102', 'Avenida Central 456'),
('MEM003', 'Luis', 'Rodríguez', 'luis.rodriguez@email.com', '555-0103', 'Plaza Mayor 789'),
('MEM004', 'María', 'López', 'maria.lopez@email.com', '555-0104', 'Boulevard Norte 101');

-- Préstamos
INSERT INTO `loans` (`member_id`, `copy_id`, `loan_date`, `due_date`, `status`) VALUES
(1, 1, '2025-11-01', '2025-11-16', 'returned'),
(2, 4, '2025-11-03', '2025-11-18', 'active'),
(3, 6, '2025-11-02', '2025-11-17', 'active');

-- ============================================
-- VISTAS ÚTILES
-- ============================================

-- Vista: Libros con información completa
CREATE OR REPLACE VIEW `book_details` AS
SELECT 
    b.*,
    GROUP_CONCAT(DISTINCT c.name ORDER BY c.name SEPARATOR ', ') AS categories,
    COUNT(DISTINCT cp.id) AS total_copies_count,
    SUM(CASE WHEN cp.status = 'available' THEN 1 ELSE 0 END) AS available_copies_count
FROM books b
LEFT JOIN book_category bc ON b.isbn = bc.isbn
LEFT JOIN categories c ON bc.category_id = c.id
LEFT JOIN copies cp ON b.isbn = cp.isbn AND cp.active = 1
WHERE b.active = 1
GROUP BY b.isbn;

-- Vista: Préstamos activos con información
CREATE OR REPLACE VIEW `active_loans` AS
SELECT 
    l.*,
    m.first_name,
    m.last_name,
    m.member_code,
    b.title,
    b.author,
    cp.copy_code,
    DATEDIFF(CURDATE(), l.due_date) AS days_overdue
FROM loans l
JOIN members m ON l.member_id = m.id
JOIN copies cp ON l.copy_id = cp.id
JOIN books b ON cp.isbn = b.isbn
WHERE l.status = 'active';

-- Vista: Estadísticas del sistema
CREATE OR REPLACE VIEW `system_stats` AS
SELECT 
    (SELECT COUNT(*) FROM books WHERE active = 1) AS total_books,
    (SELECT COUNT(*) FROM members WHERE active = 1) AS total_members,
    (SELECT COUNT(*) FROM users WHERE active = 1) AS total_users,
    (SELECT COUNT(*) FROM loans WHERE status = 'active') AS active_loans,
    (SELECT COUNT(*) FROM loans WHERE status = 'overdue') AS overdue_loans,
    (SELECT COUNT(*) FROM copies WHERE status = 'available' AND active = 1) AS available_copies;

-- ============================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- ============================================
CREATE INDEX `idx_books_active` ON `books` (`active`);
CREATE INDEX `idx_members_active` ON `members` (`active`);
CREATE INDEX `idx_loans_status_due` ON `loans` (`status`, `due_date`);
CREATE INDEX `idx_copies_status_active` ON `copies` (`status`, `active`);

COMMIT;

-- ============================================
-- MENSAJE FINAL
-- ============================================
SELECT '✅ BASE DE DATOS CREADA EXITOSAMENTE' AS message;
SELECT '📊 Tablas creadas:' AS info;
SHOW TABLES;
SELECT '👤 Usuarios del sistema:' AS info;
SELECT name, email, role FROM users;