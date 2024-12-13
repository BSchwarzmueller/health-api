CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       name VARCHAR(100) NOT NULL,
                       type VARCHAR(50) NOT NULL
);

CREATE TABLE medications (
                             id INT AUTO_INCREMENT PRIMARY KEY,
                             user_id INT NOT NULL,
                             name VARCHAR(100) NOT NULL,
                             started_at DATETIME NOT NULL,
                             dosage INT NOT NULL,
                             note VARCHAR(500),
                             image LONGBLOB,
                             FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Benutzer erstellen
INSERT INTO users (name, type) VALUES ('Customer1', 'customer');
INSERT INTO users (name, type) VALUES ('Customer2', 'customer');
INSERT INTO users (name, type) VALUES ('Pharmacist1', 'pharmacist');

-- Medikamente für Customer1 erstellen
INSERT INTO medications (user_id, name, started_at, dosage, note) VALUES (1, 'Medication1', '2023-01-01 00:00:00', 10, 'Note1');
INSERT INTO medications (user_id, name, started_at, dosage, note) VALUES (1, 'Medication2', '2023-01-02 00:00:00', 20, 'Note2');
INSERT INTO medications (user_id, name, started_at, dosage, note) VALUES (1, 'Medication3', '2023-01-03 00:00:00', 30, 'Note3');
INSERT INTO medications (user_id, name, started_at, dosage, note) VALUES (1, 'Medication4', '2023-01-04 00:00:00', 40, 'Note4');
INSERT INTO medications (user_id, name, started_at, dosage, note) VALUES (1, 'Medication5', '2023-01-05 00:00:00', 50, 'Note5');

-- Medikamente für Customer2 erstellen
INSERT INTO medications (user_id, name, started_at, dosage, note) VALUES (2, 'Medication6', '2023-01-06 00:00:00', 60, 'Note6');
INSERT INTO medications (user_id, name, started_at, dosage, note) VALUES (2, 'Medication7', '2023-01-07 00:00:00', 70, 'Note7');
INSERT INTO medications (user_id, name, started_at, dosage, note) VALUES (2, 'Medication8', '2023-01-08 00:00:00', 80, 'Note8');
INSERT INTO medications (user_id, name, started_at, dosage, note) VALUES (2, 'Medication9', '2023-01-09 00:00:00', 90, 'Note9');
INSERT INTO medications (user_id, name, started_at, dosage, note) VALUES (2, 'Medication10', '2023-01-10 00:00:00', 100, 'Note10');