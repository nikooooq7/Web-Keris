-- ====================================================================
-- DATABASE GALERI KERIS (MySQL / MariaDB)
-- --------------------------------------------------------------------
-- File ini dijalankan OTOMATIS oleh installer (buka alamat /install).
-- Anda tidak perlu mengimpornya manual kecuali ingin membuat tabel sendiri
-- lewat phpMyAdmin (tab "Import").
-- ====================================================================

-- Akun admin ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username      VARCHAR(60)  NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  display_name  VARCHAR(100) NOT NULL DEFAULT '',
  created_at    DATETIME     NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengaturan website (nama toko, teks beranda, foto profil, dll.) ----
CREATE TABLE IF NOT EXISTS settings (
  setting_key   VARCHAR(100) NOT NULL,
  setting_value TEXT NULL,
  PRIMARY KEY (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kategori produk ----------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(100) NOT NULL,
  slug        VARCHAR(120) NOT NULL,
  description VARCHAR(255) NOT NULL DEFAULT '',
  sort_order  INT NOT NULL DEFAULT 0,
  created_at  DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Produk / barang koleksi -------------------------------------------
CREATE TABLE IF NOT EXISTS products (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id    INT UNSIGNED NULL,
  name           VARCHAR(200) NOT NULL,
  slug           VARCHAR(160) NOT NULL,
  price          BIGINT UNSIGNED NULL,
  status         VARCHAR(20)  NOT NULL DEFAULT 'tersedia',
  is_featured    TINYINT(1)   NOT NULL DEFAULT 0,
  is_active      TINYINT(1)   NOT NULL DEFAULT 1,
  origin         VARCHAR(150) NOT NULL DEFAULT '',
  era            VARCHAR(150) NOT NULL DEFAULT '',
  material       VARCHAR(150) NOT NULL DEFAULT '',
  dimension      VARCHAR(150) NOT NULL DEFAULT '',
  condition_note VARCHAR(255) NOT NULL DEFAULT '',
  description    TEXT NULL,
  video_url      VARCHAR(255) NOT NULL DEFAULT '',
  views          INT UNSIGNED NOT NULL DEFAULT 0,
  created_at     DATETIME NOT NULL,
  updated_at     DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_products_slug (slug),
  KEY idx_products_category (category_id),
  KEY idx_products_listing (is_active, status),
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Foto & video milik produk -----------------------------------------
CREATE TABLE IF NOT EXISTS product_media (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id INT UNSIGNED NOT NULL,
  type       VARCHAR(10)  NOT NULL DEFAULT 'image',
  file_path  VARCHAR(255) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_media_product (product_id, type, sort_order),
  CONSTRAINT fk_media_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pesan dari formulir Kontak ----------------------------------------
CREATE TABLE IF NOT EXISTS messages (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name       VARCHAR(100) NOT NULL,
  email      VARCHAR(150) NOT NULL DEFAULT '',
  phone      VARCHAR(40)  NOT NULL DEFAULT '',
  subject    VARCHAR(150) NOT NULL DEFAULT '',
  body       TEXT NOT NULL,
  is_read    TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_messages_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
