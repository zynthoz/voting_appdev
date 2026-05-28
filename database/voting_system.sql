CREATE DATABASE IF NOT EXISTS voting_system;
USE voting_system;

-- tbl_users
CREATE TABLE tbl_users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'organizer', 'voter') NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL
);

-- tbl_logs
CREATE TABLE tbl_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES tbl_users(user_id)
);

-- tbl_voters
CREATE TABLE tbl_voters (
    voter_id INT AUTO_INCREMENT PRIMARY KEY,
    voter_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    contact_information VARCHAR(255) NOT NULL
);

-- tbl_candidates
CREATE TABLE tbl_candidates (
    candidate_id INT AUTO_INCREMENT PRIMARY KEY,
    candidate_name VARCHAR(100) NOT NULL,
    party_affiliation VARCHAR(100) NOT NULL,
    election_position VARCHAR(100) NOT NULL
);

-- tbl_positions
CREATE TABLE tbl_positions (
    position_id INT AUTO_INCREMENT PRIMARY KEY,
    position_name VARCHAR(100) NOT NULL,
    description TEXT
);

-- tbl_elections
CREATE TABLE tbl_elections (
    election_id INT AUTO_INCREMENT PRIMARY KEY,
    election_name VARCHAR(100) NOT NULL,
    election_date DATE NOT NULL
);

-- tbl_votes
CREATE TABLE tbl_votes (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    voter_id INT NOT NULL,
    candidate_id INT NOT NULL,
    vote_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (voter_id) REFERENCES tbl_voters(voter_id),
    FOREIGN KEY (candidate_id) REFERENCES tbl_candidates(candidate_id)
);

-- tbl_vote_counts
CREATE TABLE tbl_vote_counts (
    vote_count_id INT AUTO_INCREMENT PRIMARY KEY,
    candidate_id INT NOT NULL,
    election_id INT NOT NULL,
    vote_count INT DEFAULT 0,
    FOREIGN KEY (candidate_id) REFERENCES tbl_candidates(candidate_id),
    FOREIGN KEY (election_id) REFERENCES tbl_elections(election_id)
);

-- Default admin account (password: admin123)
INSERT INTO tbl_users (full_name, role, username, password, email)
VALUES ('System Administrator', 'admin', 'admin', '$2y$10$MRZRgVryw3.71kZ9sCm4VulhhtREfx3KWvqlfskCPXNzhXpitS2XC', 'admin@election.local');
