# Election Voting System — Full PRD
## Cooperatives / Classes

---

## 1. Overview

A role-based web Election Voting System for cooperatives or classes. Three access levels control what each user can see and do. PHPMailer handles all outgoing email notifications via SMTP.

---

## 2. Roles

| Role | Level |
|---|---|
| Admin | Top |
| Organizer | Mid |
| Voter | Low |

---

## 3. Database Schema

### Universal Tables

**tbl_users**
- user_id (Primary Key)
- full_name
- role (admin, organizer, voter)
- username
- password (hashed)
- email

**tbl_logs**
- log_id (Primary Key)
- user_id (Foreign Key → tbl_users.user_id)
- action
- datetime

---

### System Tables

**tbl_voters**
- voter_id (Primary Key)
- voter_name
- date_of_birth
- gender
- contact_information

**tbl_candidates**
- candidate_id (Primary Key)
- candidate_name
- party_affiliation
- election_position

**tbl_positions**
- position_id (Primary Key)
- position_name
- description

**tbl_elections**
- election_id (Primary Key)
- election_name
- election_date

**tbl_votes**
- vote_id (Primary Key)
- voter_id (Foreign Key → tbl_voters.voter_id)
- candidate_id (Foreign Key → tbl_candidates.candidate_id)
- vote_timestamp

**tbl_vote_counts**
- vote_count_id (Primary Key)
- candidate_id (Foreign Key → tbl_candidates.candidate_id)
- election_id (Foreign Key → tbl_elections.election_id)
- vote_count

---

## 4. Role Access

### Admin (Top)
- Can add, edit, and view ALL tables including tbl_users and tbl_logs
- Has access to all sidebar pages

### Organizer (Mid)
- Can add, edit, and view all tables EXCEPT tbl_users and tbl_logs
- Sidebar is a duplicate of Admin sidebar with Users and Logs links removed and not visible

### Voter (Low)
- Can view the candidates list
- Can view a summary of their own vote
- Can view the overall election results
- Can cast a vote for candidates

---

## 5. Table Display Rules

- Tables with date fields are displayed in descending order (latest first)
- All tables have a search bar and column sorting
- Foreign keys are not displayed directly — tables are joined to show readable fields
- Every table has an Add button for adding new records

---

## 6. PHPMailer Integration

### Installation (No Composer — Manual Copy-Paste)

Download PHPMailer and copy only these three files into your project folder, e.g. `includes/phpmailer/`:

```
src/Exception.php
src/PHPMailer.php
src/SMTP.php
```

### How to Load It

At the top of any PHP file that sends email:

```php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'includes/phpmailer/Exception.php';
require 'includes/phpmailer/PHPMailer.php';
require 'includes/phpmailer/SMTP.php';
```

### Reusable Send Function

Create one file `includes/mailer.php` and paste this function. Call it anywhere in the system:

```php
function send_email($to_email, $to_name, $subject, $body) {
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require_once 'includes/phpmailer/Exception.php';
    require_once 'includes/phpmailer/PHPMailer.php';
    require_once 'includes/phpmailer/SMTP.php';

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'youremail@gmail.com';
    $mail->Password   = 'your_app_password';
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;

    $mail->setFrom('youremail@gmail.com', 'Election System');
    $mail->addAddress($to_email, $to_name);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->send();
}
```

Replace `youremail@gmail.com` and `your_app_password` with your actual Gmail and App Password. To generate a Gmail App Password go to: Google Account → Security → 2-Step Verification → App Passwords.

### Where Email Is Sent in the System

| Trigger | Recipient | Email Content |
|---|---|---|
| New voter account created | Voter | Welcome email with login credentials |
| Vote successfully cast | Voter | Confirmation of their vote |
| Election results published | All voters | Notification that results are now available |
| Password reset | Any user | Reset link or temporary password |

### Example Usage

```php
send_email(
    $voter_email,
    $voter_name,
    'Your Vote Has Been Recorded',
    'Thank you for voting. Your vote has been successfully recorded.'
);
```

---

## 7. Code Rules

- No input sanitization of any kind — no htmlspecialchars, no intval, no trim, no strip_tags
- No SQL injection prevention beyond prepared statements where already used
- No ternary operators or shorthand syntax — write all logic out explicitly with if/else
- No edge case handling — if it works for the normal flow, it is done
- No unnecessary wrapping — if a while loop handles empty results naturally, do not add a num_rows check before it
- No redundant null checks on query results
- No duplicate logic — if data is already fetched once, do not fetch it again
- Verification and OTP logic is exempt from simplification — keep exactly as is
- Every line of code must be load-bearing — if removing it does not break anything, remove it

---

## 8. Out of Scope

- Mobile app
- Real-time live vote count updates
- Multi-election simultaneous support
- Candidate photo uploads
- Voter ID card generation