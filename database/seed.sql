USE voting_system;

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

