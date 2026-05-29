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

-- Insert Users (Password is their role, no hashing)
INSERT INTO tbl_users (full_name, role, username, password, email) VALUES
('Admin 1', 'admin', 'admin1', 'admin', 'admin1@election.local'),
('Organizer 1', 'organizer', 'organizer1', 'organizer', 'organizer1@election.local'),
('Voter 1', 'voter', 'voter1', 'voter', 'voter1@election.local'),
('Voter 2', 'voter', 'voter2', 'voter', 'voter2@election.local');

-- Insert Voters matching Voter Users (for vote casting)
INSERT INTO tbl_voters (voter_name, date_of_birth, gender, contact_information) VALUES
('Voter 1', '2000-01-01', 'Male', 'voter1@election.local'),
('Voter 2', '2001-02-02', 'Female', 'voter2@election.local');

-- Insert Positions
INSERT INTO tbl_positions (position_name, description) VALUES
('President', 'Lead coordinator of the group'),
('Vice President', 'Assists the president in tasks'),
('Secretary', 'Handles documentations and meetings'),
('Treasurer', 'Manages group finances');

-- Insert Candidates
INSERT INTO tbl_candidates (candidate_name, party_affiliation, election_position) VALUES
('President 1', 'Party A', 'President'),
('President 2', 'Party B', 'President'),
('Vice President 1', 'Party A', 'Vice President'),
('Vice President 2', 'Party B', 'Vice President'),
('Secretary 1', 'Party A', 'Secretary'),
('Secretary 2', 'Party B', 'Secretary'),
('Treasurer 1', 'Party A', 'Treasurer'),
('Treasurer 2', 'Party B', 'Treasurer');

-- Insert Election
INSERT INTO tbl_elections (election_name, election_date) VALUES
('General Election 2026', '2026-06-01');

-- Insert Dummy Votes
INSERT INTO tbl_votes (voter_id, candidate_id) VALUES
(1, 1),
(1, 3),
(1, 5),
(1, 7),
(2, 1),
(2, 4),
(2, 5),
(2, 8);

-- Insert Vote Counts
INSERT INTO tbl_vote_counts (candidate_id, election_id, vote_count) VALUES
(1, 1, 2),
(2, 1, 0),
(3, 1, 1),
(4, 1, 1),
(5, 1, 2),
(6, 1, 0),
(7, 1, 1),
(8, 1, 1);

