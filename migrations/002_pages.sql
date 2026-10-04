-- Pàgines de contingut editables des del Panell de Gestió (petit CMS).
-- Les pàgines legals porten una clau de sistema (`system_key`) perquè el web
-- les pugui enllaçar encara que se'n canviï l'adreça, i no es poden eliminar.

CREATE TABLE IF NOT EXISTS `pages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(120) NOT NULL,
  `system_key` VARCHAR(40) NULL,
  `title` VARCHAR(190) NOT NULL,
  `summary` VARCHAR(300) NULL,
  `body` MEDIUMTEXT NULL,
  `cover_image` VARCHAR(255) NULL,
  `published` TINYINT(1) NOT NULL DEFAULT 0,
  `show_in_menu` TINYINT(1) NOT NULL DEFAULT 0,
  `show_in_footer` TINYINT(1) NOT NULL DEFAULT 0,
  `show_on_home` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pages_slug` (`slug`),
  UNIQUE KEY `uq_pages_system` (`system_key`),
  KEY `idx_pages_published` (`published`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
