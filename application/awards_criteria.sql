CREATE TABLE IF NOT EXISTS awards_criteria (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(150) NOT NULL,
    schoolyear_id   INT NOT NULL,
    excluded_subjects VARCHAR(255) NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_awards_criteria_schoolyear
        FOREIGN KEY (schoolyear_id) REFERENCES schoolyear(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

-- Migration for existing table:
-- ALTER TABLE awards_criteria CHANGE excluded_grades excluded_subjects VARCHAR(255) NULL;
