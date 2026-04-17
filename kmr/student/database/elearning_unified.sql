-- Canonical schema for the KMR student/professor app
-- Single-file version with the current app model.
-- Identity key: students are identified by CIN.

SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS support_messages;
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS reclamations;
DROP TABLE IF EXISTS certificates;
DROP TABLE IF EXISTS enrollments;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS suivi_lecons;
DROP TABLE IF EXISTS inscription;
DROP TABLE IF EXISTS certificaton;
DROP TABLE IF EXISTS lecon;
DROP TABLE IF EXISTS cours;
DROP TABLE IF EXISTS etudiant;
DROP TABLE IF EXISTS professeur;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE etudiant (
  CIN INT NOT NULL,
  nom VARCHAR(50) NOT NULL,
  prenom VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  data LONGBLOB DEFAULT NULL,
  type VARCHAR(50) DEFAULT NULL,
  name VARCHAR(100) DEFAULT NULL,
  date_inscription TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  preferred_language VARCHAR(255) NOT NULL,
  PRIMARY KEY (CIN),
  UNIQUE KEY uq_etudiant_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE professeur (
  CIN INT NOT NULL,
  nom VARCHAR(50) NOT NULL,
  prenom VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  data LONGBLOB DEFAULT NULL,
  name VARCHAR(255) DEFAULT NULL,
  type VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (CIN)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE cours (
  id INT NOT NULL AUTO_INCREMENT,
  nom_cours VARCHAR(255) NOT NULL,
  prix DECIMAL(10,2) NOT NULL,
  categorie ENUM('Premium','Free') NOT NULL,
  image_data LONGBLOB DEFAULT NULL,
  image_type VARCHAR(255) DEFAULT NULL,
  id_professeur INT NOT NULL,
  image_name VARCHAR(255) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_cours_professeur (id_professeur),
  CONSTRAINT fk_cours_professeur FOREIGN KEY (id_professeur) REFERENCES professeur (CIN) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE lecon (
  id_lecon INT NOT NULL AUTO_INCREMENT,
  titre VARCHAR(255) NOT NULL,
  description TEXT DEFAULT NULL,
  type_fichier VARCHAR(50) DEFAULT NULL,
  nom_fichier VARCHAR(255) NOT NULL,
  id_cours INT DEFAULT NULL,
  contenu_blob LONGBLOB NOT NULL,
  PRIMARY KEY (id_lecon),
  KEY idx_lecon_cours (id_cours),
  CONSTRAINT fk_lecon_cours FOREIGN KEY (id_cours) REFERENCES cours (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE certificaton (
  id_certificat INT NOT NULL AUTO_INCREMENT,
  code_verification VARCHAR(50) NOT NULL,
  id_etudiant INT NOT NULL,
  id_professeur INT NOT NULL,
  id_cours INT NOT NULL,
  date_obtention DATETIME DEFAULT CURRENT_TIMESTAMP,
  chemin_pdf VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id_certificat),
  UNIQUE KEY uq_certificaton_code (code_verification),
  KEY idx_certificaton_etudiant (id_etudiant),
  KEY idx_certificaton_professeur (id_professeur),
  KEY idx_certificaton_cours (id_cours),
  CONSTRAINT fk_certificaton_etudiant FOREIGN KEY (id_etudiant) REFERENCES etudiant (CIN),
  CONSTRAINT fk_certificaton_professeur FOREIGN KEY (id_professeur) REFERENCES professeur (CIN),
  CONSTRAINT fk_certificaton_cours FOREIGN KEY (id_cours) REFERENCES cours (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE inscription (
  id_inscription INT NOT NULL AUTO_INCREMENT,
  id_etudiant INT DEFAULT NULL,
  id_cours INT DEFAULT NULL,
  date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
  methode_paiement VARCHAR(50) DEFAULT 'Carte Bancaire',
  progression INT DEFAULT 0,
  statut_certificat ENUM('aucun','demande_envoyee','valide') DEFAULT 'aucun',
  PRIMARY KEY (id_inscription),
  KEY idx_inscription_etudiant (id_etudiant),
  KEY idx_inscription_cours (id_cours),
  CONSTRAINT fk_inscription_etudiant FOREIGN KEY (id_etudiant) REFERENCES etudiant (CIN) ON DELETE CASCADE,
  CONSTRAINT fk_inscription_cours FOREIGN KEY (id_cours) REFERENCES cours (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE suivi_lecons (
  id_suivi INT NOT NULL AUTO_INCREMENT,
  id_etudiant INT NOT NULL,
  id_lecon INT NOT NULL,
  id_cours INT NOT NULL,
  date_validation DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_suivi),
  UNIQUE KEY uq_suivi_lecons (id_etudiant, id_lecon),
  KEY idx_suivi_cours (id_cours),
  KEY idx_suivi_lecon (id_lecon),
  CONSTRAINT fk_suivi_etudiant FOREIGN KEY (id_etudiant) REFERENCES etudiant (CIN) ON DELETE CASCADE,
  CONSTRAINT fk_suivi_lecon FOREIGN KEY (id_lecon) REFERENCES lecon (id_lecon) ON DELETE CASCADE,
  CONSTRAINT fk_suivi_cours FOREIGN KEY (id_cours) REFERENCES cours (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE courses (
  id INT NOT NULL AUTO_INCREMENT,
  title VARCHAR(200) NOT NULL,
  category VARCHAR(100) NOT NULL,
  description TEXT DEFAULT NULL,
  progress_default TINYINT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_courses_title (title)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE enrollments (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  course_id INT NOT NULL,
  progress TINYINT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_enrollment (user_id, course_id),
  KEY idx_enrollments_course (course_id),
  CONSTRAINT fk_enrollments_user FOREIGN KEY (user_id) REFERENCES etudiant (CIN) ON DELETE CASCADE,
  CONSTRAINT fk_enrollments_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE certificates (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  issued_on DATE NOT NULL,
  file_path VARCHAR(500) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_certificates_user (user_id),
  CONSTRAINT fk_certificates_user FOREIGN KEY (user_id) REFERENCES etudiant (CIN) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE reclamations (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  subject VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  attachment_path VARCHAR(500) DEFAULT NULL,
  attachment_paths TEXT DEFAULT NULL,
  status ENUM('open','in_progress','resolved') NOT NULL DEFAULT 'open',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_reclamations_user (user_id),
  CONSTRAINT fk_reclamations_user FOREIGN KEY (user_id) REFERENCES etudiant (CIN) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE tasks (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  details TEXT DEFAULT NULL,
  due_date DATE DEFAULT NULL,
  priority ENUM('high','medium','low') NOT NULL DEFAULT 'medium',
  status VARCHAR(20) NOT NULL DEFAULT 'a_faire',
  is_completed TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_tasks_user (user_id),
  CONSTRAINT fk_tasks_user FOREIGN KEY (user_id) REFERENCES etudiant (CIN) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE support_messages (
  id INT NOT NULL AUTO_INCREMENT,
  student_cin INT NOT NULL,
  sender ENUM('student','admin') NOT NULL DEFAULT 'student',
  message TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_support_messages_student (student_cin),
  CONSTRAINT fk_support_messages_student FOREIGN KEY (student_cin) REFERENCES etudiant (CIN) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;
