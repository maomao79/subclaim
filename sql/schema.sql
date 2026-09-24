CREATE TABLE IF NOT EXISTS company(
 company_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 company_name VARCHAR(150) NOT NULL,
 currency_code CHAR(3) NOT NULL DEFAULT 'GBP',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS app_user(
 user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 company_id INT UNSIGNED NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 display_name VARCHAR(120) NOT NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 FOREIGN KEY(company_id) REFERENCES company(company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS customer(
 customer_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 company_id INT UNSIGNED NOT NULL,
 customer_name VARCHAR(150) NOT NULL,
 email VARCHAR(190) NOT NULL DEFAULT '',
 phone VARCHAR(50) NOT NULL DEFAULT '',
 KEY(company_id),
 FOREIGN KEY(company_id) REFERENCES company(company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS project(
 project_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 company_id INT UNSIGNED NOT NULL,
 customer_id INT UNSIGNED NOT NULL,
 project_name VARCHAR(180) NOT NULL,
 reference_no VARCHAR(80) NOT NULL DEFAULT '',
 status ENUM('ACTIVE','ON_HOLD','COMPLETE') NOT NULL DEFAULT 'ACTIVE',
 KEY(company_id), KEY(customer_id),
 FOREIGN KEY(company_id) REFERENCES company(company_id),
 FOREIGN KEY(customer_id) REFERENCES customer(customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS contract(
 contract_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 company_id INT UNSIGNED NOT NULL,
 project_id INT UNSIGNED NOT NULL,
 contract_no VARCHAR(80) NOT NULL,
 contract_value DECIMAL(14,2) NOT NULL DEFAULT 0,
 retention_rate DECIMAL(6,3) NOT NULL DEFAULT 5,
 cis_rate DECIMAL(6,3) NOT NULL DEFAULT 20,
 KEY(company_id), KEY(project_id),
 FOREIGN KEY(company_id) REFERENCES company(company_id),
 FOREIGN KEY(project_id) REFERENCES project(project_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS claim(
 claim_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 company_id INT UNSIGNED NOT NULL,
 contract_id INT UNSIGNED NOT NULL,
 project_id INT UNSIGNED NOT NULL,
 claim_no VARCHAR(50) NOT NULL,
 period_to DATE NOT NULL,
 claimed_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
 certified_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
 retention_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
 cis_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
 paid_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
 status ENUM('DRAFT','SUBMITTED','CERTIFIED','PAID') NOT NULL DEFAULT 'DRAFT',
 UNIQUE KEY uq_claim(company_id,claim_no),
 KEY(company_id), KEY(contract_id), KEY(project_id),
 FOREIGN KEY(company_id) REFERENCES company(company_id),
 FOREIGN KEY(contract_id) REFERENCES contract(contract_id),
 FOREIGN KEY(project_id) REFERENCES project(project_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
