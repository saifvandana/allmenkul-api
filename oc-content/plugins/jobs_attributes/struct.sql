CREATE TABLE /*TABLE_PREFIX*/t_item_job_attr (
    fk_i_item_id INT UNSIGNED NOT NULL,
    e_relation ENUM('HIRE', 'LOOKING'),
    s_company_name VARCHAR(100) NOT NULL,
    e_position_type ENUM('UNDEF', 'PART', 'FULL'),
    s_salary_text TEXT NOT NULL DEFAULT '',
        PRIMARY KEY (fk_i_item_id),
        FOREIGN KEY (fk_i_item_id) REFERENCES /*TABLE_PREFIX*/t_item (pk_i_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';

CREATE TABLE /*TABLE_PREFIX*/t_item_job_description_attr (
    fk_i_item_id INT UNSIGNED NOT NULL,
    fk_c_locale_code CHAR(5) NOT NULL,
    s_desired_exp VARCHAR(255),
    s_studies VARCHAR(255),
    s_minimum_requirements TEXT,
    s_desired_requirements TEXT,
    s_contract VARCHAR(255),
    s_company_description TEXT,
        PRIMARY KEY (fk_i_item_id, fk_c_locale_code),
        FOREIGN KEY (fk_c_locale_code) REFERENCES /*TABLE_PREFIX*/t_locale (pk_c_code) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (fk_i_item_id) REFERENCES /*TABLE_PREFIX*/t_item (pk_i_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';