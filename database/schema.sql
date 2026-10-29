-- Skema SIMINAT: autentikasi, master data, dan pemodelan ID3.
CREATE TABLE IF NOT EXISTS sekolah (
    id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(10) NOT NULL UNIQUE,
    nama VARCHAR(150) NOT NULL,
    alamat VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'guru_bk', 'siswa') NOT NULL DEFAULT 'guru_bk',
    sekolah_id TINYINT UNSIGNED NULL,
    kode_siswa VARCHAR(20) NULL UNIQUE,
    must_change_password TINYINT(1) NOT NULL DEFAULT 0,
    session_version INT UNSIGNED NOT NULL DEFAULT 1,
    last_login DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_sekolah FOREIGN KEY (sekolah_id) REFERENCES sekolah(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS login_attempts (
    ip_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
    attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    window_start INT UNSIGNED NOT NULL,
    INDEX idx_login_attempts_window (window_start)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Master data siswa, atribut ID3, model, evaluasi, dan prediksi.
CREATE TABLE IF NOT EXISTS siswa (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_siswa VARCHAR(20) NOT NULL UNIQUE,
    sekolah_id TINYINT UNSIGNED NOT NULL,
    kelas VARCHAR(10) NOT NULL,
    nama VARCHAR(150) NOT NULL,
    nis VARCHAR(30) NULL,
    nisn VARCHAR(20) NULL,
    jk ENUM('L', 'P') NULL,
    peminatan VARCHAR(20) NOT NULL,
    label ENUM('Saintek', 'Soshum', 'Campuran') NULL,
    sumber_label VARCHAR(100) NULL,
    detail_label VARCHAR(255) NULL,
    jurusan_diterima VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_siswa_sekolah_label (sekolah_id, label),
    CONSTRAINT fk_siswa_sekolah FOREIGN KEY (sekolah_id) REFERENCES sekolah(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS nilai_rata (
    siswa_id BIGINT UNSIGNED PRIMARY KEY,
    rata_agama DECIMAL(5,2) NULL,
    nilai_barab DECIMAL(5,2) NULL,
    rata_bahasa DECIMAL(5,2) NULL,
    nilai_mtk DECIMAL(5,2) NULL,
    rata_mipa DECIMAL(5,2) NULL,
    rata_ips DECIMAL(5,2) NULL,
    rata_umum DECIMAL(5,2) NULL,
    CONSTRAINT fk_nilai_rata_siswa FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS nilai_mapel (
    siswa_id BIGINT UNSIGNED NOT NULL,
    kode_mapel VARCHAR(10) NOT NULL,
    nilai TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (siswa_id, kode_mapel),
    CONSTRAINT fk_nilai_mapel_siswa FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS atribut (
    id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(30) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    deskripsi VARCHAR(255) NULL,
    aktif TINYINT(1) NOT NULL DEFAULT 1,
    urutan TINYINT UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS atribut_nilai (
    id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    atribut_id TINYINT UNSIGNED NOT NULL,
    nilai VARCHAR(30) NOT NULL,
    urutan TINYINT UNSIGNED NOT NULL,
    UNIQUE KEY uq_atribut_nilai (atribut_id, nilai),
    CONSTRAINT fk_atribut_nilai FOREIGN KEY (atribut_id) REFERENCES atribut(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS siswa_atribut (
    siswa_id BIGINT UNSIGNED NOT NULL,
    atribut_id TINYINT UNSIGNED NOT NULL,
    nilai VARCHAR(30) NOT NULL,
    PRIMARY KEY (siswa_id, atribut_id),
    INDEX idx_siswa_atribut_nilai (atribut_id, nilai),
    CONSTRAINT fk_sa_siswa FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
    CONSTRAINT fk_sa_atribut FOREIGN KEY (atribut_id) REFERENCES atribut(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS aturan_diskretisasi (
    id TINYINT UNSIGNED PRIMARY KEY,
    batas_tinggi DECIMAL(5,2) NOT NULL,
    batas_sedang DECIMAL(5,2) NOT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS model_id3 (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    skenario VARCHAR(20) NOT NULL,
    jumlah_data SMALLINT UNSIGNED NOT NULL,
    atribut_dipakai JSON NOT NULL,
    min_sampel TINYINT UNSIGNED NOT NULL DEFAULT 2,
    max_depth TINYINT UNSIGNED NULL,
    tree_json LONGTEXT NOT NULL,
    jumlah_node SMALLINT UNSIGNED NOT NULL,
    jumlah_daun SMALLINT UNSIGNED NOT NULL,
    kedalaman TINYINT UNSIGNED NOT NULL,
    akurasi_latih DECIMAL(6,4) NOT NULL,
    dibuat_oleh BIGINT UNSIGNED NULL,
    aktif TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_model_user FOREIGN KEY (dibuat_oleh) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS model_langkah (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    model_id INT UNSIGNED NOT NULL,
    urutan SMALLINT UNSIGNED NOT NULL,
    node_path VARCHAR(500) NOT NULL,
    jumlah SMALLINT UNSIGNED NOT NULL,
    distribusi JSON NOT NULL,
    entropy_parent DECIMAL(8,6) NOT NULL,
    detail_gain JSON NOT NULL,
    atribut_terpilih VARCHAR(30) NULL,
    CONSTRAINT fk_langkah_model FOREIGN KEY (model_id) REFERENCES model_id3(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS model_aturan (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    model_id INT UNSIGNED NOT NULL,
    no_aturan SMALLINT UNSIGNED NOT NULL,
    kondisi JSON NOT NULL,
    kelas VARCHAR(20) NOT NULL,
    support SMALLINT UNSIGNED NOT NULL,
    keyakinan DECIMAL(6,4) NOT NULL,
    CONSTRAINT fk_aturan_model FOREIGN KEY (model_id) REFERENCES model_id3(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS evaluasi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    model_id INT UNSIGNED NOT NULL,
    k TINYINT UNSIGNED NOT NULL,
    jumlah_data SMALLINT UNSIGNED NOT NULL,
    akurasi DECIMAL(6,4) NOT NULL,
    precision_macro DECIMAL(6,4) NOT NULL,
    recall_macro DECIMAL(6,4) NOT NULL,
    f1_macro DECIMAL(6,4) NOT NULL,
    confusion_json JSON NOT NULL,
    per_kelas_json JSON NOT NULL,
    detail_fold_json JSON NOT NULL,
    dibuat_oleh BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_evaluasi_model FOREIGN KEY (model_id) REFERENCES model_id3(id) ON DELETE CASCADE,
    CONSTRAINT fk_evaluasi_user FOREIGN KEY (dibuat_oleh) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS prediksi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    model_id INT UNSIGNED NOT NULL,
    sumber ENUM('form', 'massal') NOT NULL,
    siswa_id BIGINT UNSIGNED NULL,
    nama_input VARCHAR(150) NULL,
    input_json JSON NOT NULL,
    hasil VARCHAR(20) NOT NULL,
    keyakinan DECIMAL(6,4) NOT NULL,
    distribusi_json JSON NOT NULL,
    jalur_json JSON NOT NULL,
    dibuat_oleh BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_prediksi_created (created_at),
    CONSTRAINT fk_prediksi_model FOREIGN KEY (model_id) REFERENCES model_id3(id) ON DELETE CASCADE,
    CONSTRAINT fk_prediksi_siswa FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE SET NULL,
    CONSTRAINT fk_prediksi_user FOREIGN KEY (dibuat_oleh) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
