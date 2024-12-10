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