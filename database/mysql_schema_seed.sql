SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS outgoing_letters;
DROP TABLE IF EXISTS number_sequences;
DROP TABLE IF EXISTS classifications;
DROP TABLE IF EXISTS archive_types;
DROP TABLE IF EXISTS letter_sensitivities;
DROP TABLE IF EXISTS letter_types;
DROP TABLE IF EXISTS numbering_rules;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS work_teams;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE work_teams (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,name VARCHAR(150) NOT NULL UNIQUE,is_active TINYINT(1) NOT NULL DEFAULT 1,created_at DATETIME DEFAULT CURRENT_TIMESTAMP,updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB;
CREATE TABLE users (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120) NOT NULL,email VARCHAR(190) NOT NULL UNIQUE,password_hash VARCHAR(255) NOT NULL,role ENUM('ADMIN','USER') NOT NULL DEFAULT 'USER',work_team_id INT UNSIGNED NULL,is_active TINYINT(1) NOT NULL DEFAULT 1,created_at DATETIME DEFAULT CURRENT_TIMESTAMP,updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,CONSTRAINT fk_users_team FOREIGN KEY(work_team_id) REFERENCES work_teams(id)) ENGINE=InnoDB;
CREATE TABLE numbering_rules (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,code VARCHAR(50) NOT NULL UNIQUE,name VARCHAR(120) NOT NULL,unit_code VARCHAR(20) NOT NULL,pattern VARCHAR(200) NOT NULL,is_active TINYINT(1) NOT NULL DEFAULT 1) ENGINE=InnoDB;
CREATE TABLE letter_types (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,code VARCHAR(60) NOT NULL UNIQUE,name VARCHAR(160) NOT NULL,numbering_rule_id INT UNSIGNED NOT NULL,is_active TINYINT(1) NOT NULL DEFAULT 1,CONSTRAINT fk_type_rule FOREIGN KEY(numbering_rule_id) REFERENCES numbering_rules(id)) ENGINE=InnoDB;
CREATE TABLE letter_sensitivities (code VARCHAR(30) PRIMARY KEY,name VARCHAR(80) NOT NULL,prefix VARCHAR(10) NOT NULL,sort_order INT NOT NULL DEFAULT 0,is_active TINYINT(1) NOT NULL DEFAULT 1) ENGINE=InnoDB;
CREATE TABLE archive_types (code VARCHAR(30) PRIMARY KEY,name VARCHAR(80) NOT NULL,short_code CHAR(1) NOT NULL,is_active TINYINT(1) NOT NULL DEFAULT 1) ENGINE=InnoDB;
CREATE TABLE classifications (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,scope_key CHAR(2) NOT NULL,level TINYINT NOT NULL,parent_code VARCHAR(10) NULL,code VARCHAR(10) NOT NULL,name VARCHAR(180) NOT NULL,is_active TINYINT(1) NOT NULL DEFAULT 1,INDEX idx_cls(scope_key,level,parent_code,code)) ENGINE=InnoDB;
CREATE TABLE number_sequences (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,numbering_rule_id INT UNSIGNED NOT NULL,year SMALLINT UNSIGNED NOT NULL,current_number INT UNSIGNED NOT NULL DEFAULT 0,created_at DATETIME DEFAULT CURRENT_TIMESTAMP,updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,UNIQUE KEY uq_seq(numbering_rule_id,year),CONSTRAINT fk_seq_rule FOREIGN KEY(numbering_rule_id) REFERENCES numbering_rules(id)) ENGINE=InnoDB;
CREATE TABLE outgoing_letters (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,agenda_number VARCHAR(20) NOT NULL,letter_number VARCHAR(190) NOT NULL UNIQUE,year SMALLINT UNSIGNED NOT NULL,letter_type_id INT UNSIGNED NOT NULL,work_team_id INT UNSIGNED NOT NULL,system_type ENUM('SRIKANDI','NON_SRIKANDI') NOT NULL,letter_date DATE NOT NULL,sensitivity ENUM('BIASA','PENTING','RAHASIA','SANGAT_RAHASIA') NOT NULL,uses_budget ENUM('Y','T') NOT NULL,archive_type ENUM('FASILITATIF','SUBSTANTIF') NOT NULL,scope_key CHAR(2) NOT NULL,classification_parent VARCHAR(10) NOT NULL,classification_child VARCHAR(10) NOT NULL,classification_code VARCHAR(30) NOT NULL,recipient VARCHAR(255) NOT NULL,subject VARCHAR(255) NOT NULL,notes TEXT NULL,status ENUM('ACTIVE','CANCELLED') NOT NULL DEFAULT 'ACTIVE',requested_by INT UNSIGNED NOT NULL,cancellation_reason TEXT NULL,cancelled_by INT UNSIGNED NULL,cancelled_at DATETIME NULL,created_at DATETIME DEFAULT CURRENT_TIMESTAMP,updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,INDEX idx_letter_filters(year,work_team_id,sensitivity,status),CONSTRAINT fk_letter_type FOREIGN KEY(letter_type_id) REFERENCES letter_types(id),CONSTRAINT fk_letter_team FOREIGN KEY(work_team_id) REFERENCES work_teams(id),CONSTRAINT fk_letter_user FOREIGN KEY(requested_by) REFERENCES users(id),CONSTRAINT fk_letter_cancel_user FOREIGN KEY(cancelled_by) REFERENCES users(id)) ENGINE=InnoDB;
CREATE TABLE audit_logs (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id INT UNSIGNED NULL,action VARCHAR(80) NOT NULL,entity_type VARCHAR(80) NOT NULL,entity_id BIGINT UNSIGNED NULL,metadata_json JSON NULL,ip_address VARCHAR(60) NULL,created_at DATETIME DEFAULT CURRENT_TIMESTAMP,INDEX idx_audit_entity(entity_type,entity_id),CONSTRAINT fk_audit_user FOREIGN KEY(user_id) REFERENCES users(id)) ENGINE=InnoDB;

INSERT INTO work_teams(name) VALUES
('Umum, SAKIP, RB'),('Keuangan, SPIP'),('Kesra & Hansos'),('Kependudukan, PPP dan Desa Cantik'),('Neraca Wilayah Analisis Statistik'),('Distribusi, KTIP'),('PEK, TaPang, Horti'),('IPD'),('DLS, Humas dan PSS'),('SE2026 dan EPSS'),('Statistik Harga');

INSERT INTO numbering_rules(code,name,unit_code,pattern) VALUES
('MAIN_62710','Aturan Utama 62710 (prototype)','62710','{PREFIX}-{SEQ3}/{UNIT}/{KKA}/{YEAR}'),
('SUBBAG_62711','Aturan Subbag 62711 (prototype)','62711','{PREFIX}-{SEQ3}/{UNIT}/{KKA}/{YEAR}');

INSERT INTO letter_types(code,name,numbering_rule_id) VALUES
('SURAT_DINAS_DAERAH','Surat Dinas Daerah',(SELECT id FROM numbering_rules WHERE code='MAIN_62710')),
('SURAT_PERINTAH_TUGAS','Surat Perintah/Tugas Daerah',(SELECT id FROM numbering_rules WHERE code='MAIN_62710')),
('UNDANGAN_INTERNAL','Undangan Internal Daerah',(SELECT id FROM numbering_rules WHERE code='MAIN_62710')),
('NO_SUBBAG','No. Subbag (prototype)',(SELECT id FROM numbering_rules WHERE code='SUBBAG_62711'));

INSERT INTO letter_sensitivities(code,name,prefix,sort_order) VALUES
('BIASA','Biasa','B',1),('PENTING','Penting','P',2),('RAHASIA','Rahasia','R',3),('SANGAT_RAHASIA','Sangat Rahasia','S',4);
INSERT INTO archive_types(code,name,short_code) VALUES ('FASILITATIF','Fasilitatif','F'),('SUBSTANTIF','Substantif','S');

-- Working set / dummy classifications. Bukan master final BPS.
INSERT INTO classifications(scope_key,level,parent_code,code,name) VALUES
('YF',2,NULL,'KU','KEUANGAN'),('YF',2,NULL,'PR','PERENCANAAN'),('YF',2,NULL,'IF','INFORMATIKA'),
('TF',2,NULL,'PR','PERENCANAAN'),('TF',2,NULL,'IF','INFORMATIKA'),('TF',2,NULL,'RT','KERUMAHTANGGAAN'),
('YS',2,NULL,'VS','SURVEI'),('YS',2,NULL,'SS','SENSUS'),('YS',2,NULL,'KS','KONSOLIDASI DATA STATISTIK'),
('TS',2,NULL,'VS','SURVEI'),('TS',2,NULL,'SS','SENSUS'),('TS',2,NULL,'KS','KONSOLIDASI DATA STATISTIK'),
('YF',3,'KU','000','PELAKSANAAN ANGGARAN'),('YF',3,'KU','200','PENGELOLAAN PERBENDAHARAAN'),('YF',3,'KU','300','PENGELUARAN ANGGARAN'),
('YF',3,'PR','100','PENYUSUNAN RENCANA'),('YF',3,'PR','200','MONITORING RENCANA'),('YF',3,'IF','100','SISTEM INFORMASI'),
('TF',3,'PR','100','PENYUSUNAN RENCANA'),('TF',3,'IF','100','SISTEM INFORMASI'),('TF',3,'RT','100','LAYANAN RUMAH TANGGA'),
('YS',3,'VS','100','PERSIAPAN SURVEI'),('YS',3,'VS','300','PELAKSANAAN LAPANGAN'),('YS',3,'SS','100','PERSIAPAN SENSUS'),('YS',3,'KS','100','KONSOLIDASI DATA'),
('TS',3,'VS','100','PERSIAPAN SURVEI'),('TS',3,'VS','300','PELAKSANAAN LAPANGAN'),('TS',3,'SS','100','PERSIAPAN SENSUS'),('TS',3,'KS','100','KONSOLIDASI DATA');

-- Password demo: Admin123! dan User123!
INSERT INTO users(name,email,password_hash,role,work_team_id) VALUES
('Admin Prototype','admin@demo.local','$2y$12$thVd2VyO70X5pMSD/XKahesENG9hHC9yiNq7RlURDNeW6BE5V2NyC','ADMIN',1),
('User Prototype','user@demo.local','$2y$12$8fsTzc.oexEXWWynIVQtb.8nxijN35mfsKYRQBrWvPfvC7y4k7UKC','USER',2);
