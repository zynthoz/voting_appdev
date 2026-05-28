# Election Voting System — AI Task Relay File

> **READ THIS FIRST.** This file is the single source of truth for all AI models working on this project.
> Before doing ANY work, read this entire file. After completing your assigned phase, update the checkboxes and the `CURRENT STATUS` section below, then STOP.

---

## CURRENT STATUS

```
CURRENT PHASE:    5
ASSIGNED MODEL:   🎨 DESIGN (Gemini 3.5 High + Impeccable)
STATUS:           NOT STARTED
LAST COMPLETED:   Phase 4
LAST UPDATED BY:  🏗️ ARCHITECT (Claude Sonnet 4.6)
LAST UPDATED AT:  2026-05-28T16:15:00+08:00
```

> **When you finish your phase**, update the block above:
> - Set `CURRENT PHASE` to the next phase number
> - Set `ASSIGNED MODEL` to the next model
> - Set `STATUS` to `NOT STARTED`
> - Set `LAST COMPLETED` to the phase you just finished
> - Set `LAST UPDATED BY` to your model name
> - Set `LAST UPDATED AT` to current time

---

## PROJECT OVERVIEW

**What**: A role-based web Election Voting System for cooperatives/classes.
**Stack**: PHP (vanilla), MySQL, HTML/CSS/JS, XAMPP, PHPMailer (manual install, no Composer).
**PRD**: See [PRD.md](PRD.md) in this same directory for the full product requirements document.

### Three user roles

| Role | Level | Can do |
|---|---|---|
| Admin | Top | Full CRUD on ALL tables including `tbl_users` and `tbl_logs` |
| Organizer | Mid | Full CRUD on all tables EXCEPT `tbl_users` and `tbl_logs` |
| Voter | Low | View candidates, cast vote, view own vote summary, view results |

### Database tables

| Table | Purpose |
|---|---|
| `tbl_users` | Login accounts (admin, organizer, voter) |
| `tbl_logs` | Action audit trail |
| `tbl_voters` | Voter personal info |
| `tbl_candidates` | Candidate info + party + position |
| `tbl_positions` | Election positions |
| `tbl_elections` | Election events |
| `tbl_votes` | Individual vote records |
| `tbl_vote_counts` | Aggregated vote tallies per candidate per election |

---

## MODEL ASSIGNMENTS

| Alias | Model | Scope | Phases |
|---|---|---|---|
| `⚙️ GENERAL` | **Gemini 3.5 Low** | Config, scaffolding, PHPMailer, email, seed data, docs | 1, 9, 11 |
| `🏗️ ARCHITECT` | **Claude Opus 4.6** | Database, PHP backend, auth, routing, CRUD, logic | 2, 4, 6, 8 |
| `🎨 DESIGN` | **Gemini 3.5 High + Impeccable Skill** | UI/UX, CSS, layouts, components, animations, polish | 3, 5, 7, 10 |

---

## RELAY RULES — READ CAREFULLY

1. **Check your identity.** Find your model alias in the table above.
2. **Check CURRENT STATUS.** Only proceed if `ASSIGNED MODEL` matches you.
3. **If it's not your turn**, say: *"Current phase [X] is assigned to [MODEL]. I am [YOUR MODEL]. Stopping."* — and do nothing.
4. **If it IS your turn**, execute all tasks in your phase. Check off each subtask as you complete it.
5. **When you finish your phase**, update the `CURRENT STATUS` block and STOP.
6. **Do NOT skip ahead** to future phases, even if you could do them.
7. **Do NOT redo** completed phases. Trust prior work.

---

## CRITICAL CODE RULES (from PRD Section 7)

These rules apply to ALL models writing PHP code:

- ❌ No input sanitization (no `htmlspecialchars`, no `intval`, no `trim`, no `strip_tags`)
- ❌ No SQL injection prevention beyond prepared statements where already used
- ❌ No ternary operators or shorthand — write explicit `if/else`
- ❌ No edge case handling — normal flow only
- ❌ No unnecessary wrapping — no `num_rows` check before a `while` loop
- ❌ No redundant null checks on query results
- ❌ No duplicate logic — fetch data once
- ✅ Verification and OTP logic is exempt — keep as-is
- ✅ Every line must be load-bearing — if removing it doesn't break anything, remove it

---

## TABLE DISPLAY RULES (from PRD Section 5)

- Date fields → sort descending (latest first)
- All tables have search bar + column sorting
- Foreign keys are NOT displayed — JOIN to show readable fields
- Every table has an Add button

---

## EMAIL TRIGGERS (from PRD Section 6)

| Trigger | Recipient | Content |
|---|---|---|
| New voter account created | Voter | Welcome email with login credentials |
| Vote successfully cast | Voter | Vote confirmation |
| Election results published | All voters | Results available notification |
| Password reset | Any user | Temporary password |

PHPMailer: Manual install (3 files in `includes/phpmailer/`). Reusable `send_email()` function in `includes/mailer.php`.

---

## DESIGN RULES (for 🎨 DESIGN model only)

**You MUST use the Impeccable skill** for all design work. Key constraints:

- Use **OKLCH** color space. No `#000` or `#fff`. Tint neutrals toward brand hue.
- Typography: Google Fonts (Inter recommended). Scale ratio ≥1.25 between steps.
- Transitions: **ease-out-quart/quint/expo** only. No bounce, no elastic.
- Register: **Product** (this is app UI, not marketing).

**Absolute bans** (Impeccable skill):
- ❌ Side-stripe borders (`border-left` > 1px as accent)
- ❌ Gradient text (`background-clip: text`)
- ❌ Glassmorphism as default
- ❌ Hero-metric template (big number + small label)
- ❌ Identical card grids (same-size icon + heading + text repeated)
- ❌ Modal as first thought (exhaust inline alternatives first)

---

## DIRECTORY STRUCTURE (target)

```
voting_appdev/
├── assets/
│   ├── css/
│   │   ├── variables.css
│   │   ├── reset.css
│   │   ├── base.css
│   │   ├── components.css
│   │   ├── layout.css
│   │   ├── login.css
│   │   └── main.css
│   ├── js/
│   └── img/
├── includes/
│   ├── phpmailer/
│   │   ├── Exception.php
│   │   ├── PHPMailer.php
│   │   └── SMTP.php
│   ├── db.php
│   ├── auth.php
│   ├── mailer.php
│   ├── header.php
│   ├── sidebar.php
│   └── footer.php
├── admin/
│   ├── dashboard.php
│   ├── users.php
│   ├── logs.php
│   ├── voters.php
│   ├── candidates.php
│   ├── positions.php
│   ├── elections.php
│   ├── votes.php
│   └── vote_counts.php
├── organizer/
│   ├── dashboard.php
│   ├── voters.php
│   ├── candidates.php
│   ├── positions.php
│   ├── elections.php
│   ├── votes.php
│   └── vote_counts.php
├── voter/
│   ├── dashboard.php
│   ├── candidates.php
│   ├── vote.php
│   └── results.php
├── database/
│   ├── voting_system.sql
│   └── seed.sql
├── index.php            ← Login page
├── logout.php
├── forgot_password.php
├── PRD.md               ← Product requirements (DO NOT MODIFY)
├── TASKS.md             ← THIS FILE (update checkboxes + status)
└── README.md
```

---

## PHASE EXECUTION ORDER & TASKS

### Phase 1 — Project Scaffolding & Configuration `⚙️ GENERAL`

- [x] **1.1 — Create project directory structure**
  - [x] 1.1.1 — Create the full directory tree as shown above (all folders + empty placeholder `.php` files)
  - [x] 1.1.2 — Add `.gitignore` (exclude `includes/phpmailer/`, IDE files, OS files)
  - [x] 1.1.3 — Create `README.md` with project name, tech stack, setup instructions placeholder

- [x] **1.2 — Database configuration file**
  - [x] 1.2.1 — Write `includes/db.php` with MySQLi connection (`localhost`, `root`, `""`, `voting_system`)
  - [x] 1.2.2 — Add `die()` on connection failure

- [x] **1.3 — PHPMailer setup**
  - [x] 1.3.1 — Download PHPMailer, copy `Exception.php`, `PHPMailer.php`, `SMTP.php` into `includes/phpmailer/`
  - [x] 1.3.2 — Write `includes/mailer.php` with `send_email($to_email, $to_name, $subject, $body)` per PRD Section 6
  - [x] 1.3.3 — Use placeholder SMTP credentials with `// TODO: Replace with actual credentials` comments

> 🛑 **PHASE 1 COMPLETE → Update CURRENT STATUS → Set Phase 2 / 🏗️ ARCHITECT → STOP**


---

### Phase 2 — Database Schema & Core Backend `🏗️ ARCHITECT`

- [x] **2.1 — Create database and tables**
  - [x] 2.1.1 — Write `database/voting_system.sql` with `CREATE DATABASE IF NOT EXISTS voting_system`
  - [x] 2.1.2 — `tbl_users`: `user_id` INT PK AUTO_INCREMENT, `full_name` VARCHAR(100), `role` ENUM('admin','organizer','voter'), `username` VARCHAR(50) UNIQUE, `password` VARCHAR(255), `email` VARCHAR(100)
  - [x] 2.1.3 — `tbl_logs`: `log_id` INT PK AUTO_INCREMENT, `user_id` INT FK→tbl_users, `action` VARCHAR(255), `datetime` DATETIME DEFAULT CURRENT_TIMESTAMP
  - [x] 2.1.4 — `tbl_voters`: `voter_id` INT PK AUTO_INCREMENT, `voter_name` VARCHAR(100), `date_of_birth` DATE, `gender` ENUM('Male','Female','Other'), `contact_information` VARCHAR(255)
  - [x] 2.1.5 — `tbl_candidates`: `candidate_id` INT PK AUTO_INCREMENT, `candidate_name` VARCHAR(100), `party_affiliation` VARCHAR(100), `election_position` VARCHAR(100)
  - [x] 2.1.6 — `tbl_positions`: `position_id` INT PK AUTO_INCREMENT, `position_name` VARCHAR(100), `description` TEXT
  - [x] 2.1.7 — `tbl_elections`: `election_id` INT PK AUTO_INCREMENT, `election_name` VARCHAR(100), `election_date` DATE
  - [x] 2.1.8 — `tbl_votes`: `vote_id` INT PK AUTO_INCREMENT, `voter_id` INT FK→tbl_voters, `candidate_id` INT FK→tbl_candidates, `vote_timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP
  - [x] 2.1.9 — `tbl_vote_counts`: `vote_count_id` INT PK AUTO_INCREMENT, `candidate_id` INT FK→tbl_candidates, `election_id` INT FK→tbl_elections, `vote_count` INT DEFAULT 0
  - [x] 2.1.10 — INSERT default admin: username `admin`, password hash of `admin123`

- [x] **2.2 — Authentication system**
  - [x] 2.2.1 — Write `includes/auth.php`: `session_start()`, `login($username, $password)` using `password_verify()`, `logout()`, `check_role($required_role)`, `is_logged_in()`
  - [x] 2.2.2 — Write `index.php`: login form (POST), call `login()`, redirect by role
  - [x] 2.2.3 — Write `logout.php`: call `logout()`, redirect to `index.php`

- [x] **2.3 — Role-based routing & access control**
  - [x] 2.3.1 — Add `check_role('admin')` at top of every `admin/*.php`
  - [x] 2.3.2 — Add `check_role('organizer')` at top of every `organizer/*.php`
  - [x] 2.3.3 — Add `check_role('voter')` at top of every `voter/*.php`
  - [x] 2.3.4 — Login redirect: admin→`admin/dashboard.php`, organizer→`organizer/dashboard.php`, voter→`voter/dashboard.php`

- [x] **2.4 — Logging system**
  - [x] 2.4.1 — Write `log_action($user_id, $action)` function
  - [x] 2.4.2 — Integrate into: login, logout, vote cast, record add/edit/delete

> 🛑 **PHASE 2 COMPLETE → Update CURRENT STATUS → Set Phase 3 / 🎨 DESIGN → STOP**

---

### Phase 3 — Design System & UI Foundation `🎨 DESIGN`

> **IMPORTANT: Use the Impeccable skill for ALL tasks in this phase.**

- [x] **3.1 — Impeccable setup**
  - [x] 3.1.1 — Run `impeccable teach` → create `PRODUCT.md` (election system, cooperative/class context, civic tone)
  - [x] 3.1.2 — Run `impeccable document` → generate `DESIGN.md` (OKLCH palette, type scale, spacing, elevation)
  - [x] 3.1.3 — Confirm register = **product**

- [x] **3.2 — Core CSS design system**
  - [x] 3.2.1 — `assets/css/variables.css`: OKLCH colors, font families (Google Fonts: Inter), size scale, spacing scale (4px base), border-radius, shadows, transition timings
  - [x] 3.2.2 — `assets/css/reset.css`: modern CSS reset
  - [x] 3.2.3 — `assets/css/base.css`: body defaults, links, focus states, scrollbar
  - [x] 3.2.4 — `assets/css/components.css`: `.btn`, `.input`, `.data-table`, `.modal`, `.alert`, `.badge`, `.search-bar`, `.pagination`
  - [x] 3.2.5 — `assets/css/layout.css`: `.app-shell` grid, `.sidebar`, `.main-content`, `.page-header`, responsive breakpoints
  - [x] 3.2.6 — `assets/css/main.css`: `@import` all above in order

- [x] **3.3 — Login page design**
  - [x] 3.3.1 — Design `index.php` login: centered card, system title, username/password fields, login button, error area, subtle background, micro-animations
  - [x] 3.3.2 — `assets/css/login.css` for login-specific styles
  - [x] 3.3.3 — Add Google Fonts `<link>` in `<head>`

- [x] **3.4 — Sidebar & navigation**
  - [x] 3.4.1 — `includes/sidebar.php`: role-conditional nav links (Admin: all 9 items, Organizer: 7 items, Voter: 4 items), user info + role badge, logout link, active page indicator
  - [x] 3.4.2 — `includes/header.php`: top bar, dynamic page title, welcome message, mobile hamburger toggle
  - [x] 3.4.3 — `includes/footer.php`: closing tags, JS includes
  - [x] 3.4.4 — Sidebar open/close transition for mobile

- [x] **3.5 — Data table & modal components**
  - [x] 3.5.1 — Data table: search bar, sortable column headers, row hover, action buttons (Edit/Delete), Add New button, empty state
  - [x] 3.5.2 — Add/Edit modal: overlay, centered form, dynamic fields, Save/Cancel, smooth animation (ease-out-quart, NO bounce)
  - [x] 3.5.3 — Delete confirmation dialog (inline preferred over modal per impeccable rules)

> 🛑 **PHASE 3 COMPLETE → Update CURRENT STATUS → Set Phase 4 / 🏗️ ARCHITECT → STOP**

---

### Phase 4 — Admin CRUD Backend `🏗️ ARCHITECT`

- [x] **4.1 — Users management** (Admin only)
  - [x] 4.1.1 — `admin/users.php`: SELECT with search (WHERE LIKE), column sorting (ORDER BY + ASC/DESC), INSERT (password_hash), UPDATE, DELETE
  - [x] 4.1.2 — On new voter creation → call `send_email()` with welcome + credentials
  - [x] 4.1.3 — Log all add/edit/delete actions

- [x] **4.2 — Logs viewer** (Admin only, read-only)
  - [x] 4.2.1 — `admin/logs.php`: SELECT JOIN tbl_users (show full_name), ORDER BY datetime DESC, search, sort. No add/edit/delete.

- [x] **4.3 — Voters management**
  - [x] 4.3.1 — `admin/voters.php`: full CRUD, search by voter_name, sort, ORDER BY date_of_birth DESC

- [x] **4.4 — Candidates management**
  - [x] 4.4.1 — `admin/candidates.php`: full CRUD, search by name/party, sort

- [x] **4.5 — Positions management**
  - [x] 4.5.1 — `admin/positions.php`: full CRUD, search by position_name, sort

- [x] **4.6 — Elections management**
  - [x] 4.6.1 — `admin/elections.php`: full CRUD, search, sort, ORDER BY election_date DESC

- [x] **4.7 — Votes viewer** (read-only)
  - [x] 4.7.1 — `admin/votes.php`: SELECT JOIN tbl_voters + tbl_candidates (readable names), ORDER BY vote_timestamp DESC, search, sort

- [x] **4.8 — Vote Counts management**
  - [x] 4.8.1 — `admin/vote_counts.php`: SELECT JOIN tbl_candidates + tbl_elections, full CRUD, search, sort

> 🛑 **PHASE 4 COMPLETE → Update CURRENT STATUS → Set Phase 5 / 🎨 DESIGN → STOP**

---

### Phase 5 — Admin Pages UI Integration `🎨 DESIGN`

> **IMPORTANT: Use the Impeccable skill for ALL tasks in this phase.**

- [ ] **5.1 — Admin Dashboard**
  - [ ] 5.1.1 — Design dashboard: summary stats (NOT hero-metric template), recent activity feed (last 10 logs), quick action links
  - [ ] 5.1.2 — Integrate sidebar + header + footer includes

- [ ] **5.2 — Admin CRUD pages UI**
  - [ ] 5.2.1 — Integrate data table + modal UI into `admin/users.php`
  - [ ] 5.2.2 — Integrate data table (read-only) into `admin/logs.php`
  - [ ] 5.2.3 — Integrate into `admin/voters.php`
  - [ ] 5.2.4 — Integrate into `admin/candidates.php`
  - [ ] 5.2.5 — Integrate into `admin/positions.php`
  - [ ] 5.2.6 — Integrate into `admin/elections.php`
  - [ ] 5.2.7 — Integrate into `admin/votes.php`
  - [ ] 5.2.8 — Integrate into `admin/vote_counts.php`
  - [ ] 5.2.9 — Add sidebar + header + footer includes to all admin pages
  - [ ] 5.2.10 — Active-page highlighting in sidebar for each admin page

> 🛑 **PHASE 5 COMPLETE → Update CURRENT STATUS → Set Phase 6 / 🏗️ ARCHITECT → STOP**

---

### Phase 6 — Organizer Backend `🏗️ ARCHITECT`

- [ ] **6.1 — Organizer CRUD backend**
  - [ ] 6.1.1 — `organizer/dashboard.php`: same as admin dashboard minus user/log stats
  - [ ] 6.1.2 — `organizer/voters.php`: duplicate admin/voters.php logic + `check_role('organizer')`
  - [ ] 6.1.3 — `organizer/candidates.php`: duplicate logic
  - [ ] 6.1.4 — `organizer/positions.php`: duplicate logic
  - [ ] 6.1.5 — `organizer/elections.php`: duplicate logic
  - [ ] 6.1.6 — `organizer/votes.php`: duplicate logic
  - [ ] 6.1.7 — `organizer/vote_counts.php`: duplicate logic

> 🛑 **PHASE 6 COMPLETE → Update CURRENT STATUS → Set Phase 7 / 🎨 DESIGN → STOP**

---

### Phase 7 — Organizer & Voter Pages UI `🎨 DESIGN`

> **IMPORTANT: Use the Impeccable skill for ALL tasks in this phase.**

- [ ] **7.1 — Organizer pages UI**
  - [ ] 7.1.1 — Integrate UI into all `organizer/*.php` pages (reuse Phase 5 components)
  - [ ] 7.1.2 — Verify organizer sidebar: no Users link, no Logs link
  - [ ] 7.1.3 — Active-page highlighting for organizer sidebar

- [ ] **7.2 — Voter Dashboard**
  - [ ] 7.2.1 — Design `voter/dashboard.php`: welcome message, current election info, quick links (Candidates / Vote / Results), vote status indicator

- [ ] **7.3 — Voter Candidates view**
  - [ ] 7.3.1 — `voter/candidates.php`: read-only, candidates with name + party + position, search/filter, clean layout (NOT identical card grids)

- [ ] **7.4 — Voting page** (most critical user-facing page)
  - [ ] 7.4.1 — `voter/vote.php`: positions list, candidate selection per position (radio/selectable), Submit Vote with inline confirmation (NOT modal), success message, disabled if already voted
  - [ ] 7.4.2 — Selection micro-animations
  - [ ] 7.4.3 — Post-vote confirmation view (summary of selections)

- [ ] **7.5 — Results page**
  - [ ] 7.5.1 — `voter/results.php`: grouped by position, vote counts, CSS-only vote bars, winner highlight, scannable layout

> 🛑 **PHASE 7 COMPLETE → Update CURRENT STATUS → Set Phase 8 / 🏗️ ARCHITECT → STOP**

---

### Phase 8 — Voter Backend Logic `🏗️ ARCHITECT`

- [ ] **8.1 — Voter backend functionality**
  - [ ] 8.1.1 — `voter/dashboard.php` backend: fetch current election, check if voted, fetch vote summary
  - [ ] 8.1.2 — `voter/candidates.php` backend: SELECT JOIN positions, search + sort
  - [ ] 8.1.3 — `voter/vote.php` backend: check already voted → show summary + disable form; on POST → INSERT tbl_votes, UPDATE tbl_vote_counts, call send_email() confirmation, log action
  - [ ] 8.1.4 — `voter/results.php` backend: SELECT JOIN vote_counts + candidates + elections, group by position, ORDER BY vote_count DESC

> 🛑 **PHASE 8 COMPLETE → Update CURRENT STATUS → Set Phase 9 / ⚙️ GENERAL → STOP**

---

### Phase 9 — Email Integration & Password Reset `⚙️ GENERAL`

- [ ] **9.1 — Email triggers**
  - [ ] 9.1.1 — Verify `send_email()` in user creation flow (welcome email)
  - [ ] 9.1.2 — Verify `send_email()` in vote cast flow (confirmation)
  - [ ] 9.1.3 — Add email trigger: election results published → notify all voters
  - [ ] 9.1.4 — Create inline-styled HTML email templates (welcome, vote confirmation, results published, password reset)

- [ ] **9.2 — Password reset flow**
  - [ ] 9.2.1 — `forgot_password.php`: email input form
  - [ ] 9.2.2 — Generate temp password, hash, update tbl_users
  - [ ] 9.2.3 — Send temp password via `send_email()`
  - [ ] 9.2.4 — Add "Forgot Password?" link to login page

> 🛑 **PHASE 9 COMPLETE → Update CURRENT STATUS → Set Phase 10 / 🎨 DESIGN → STOP**

---

### Phase 10 — Final Polish & Responsive `🎨 DESIGN`

> **IMPORTANT: Use the Impeccable skill for ALL tasks in this phase.**

- [ ] **10.1 — Responsive design pass**
  - [ ] 10.1.1 — Audit all pages at 375px, 768px, 1280px+
  - [ ] 10.1.2 — Fix breaks: sidebar collapse, table horizontal scroll, form stacking
  - [ ] 10.1.3 — Ensure 44px min touch targets on mobile

- [ ] **10.2 — Animation & micro-interaction pass** (run `impeccable animate`)
  - [ ] 10.2.1 — Page load fade-in for main content
  - [ ] 10.2.2 — Table row hover effects
  - [ ] 10.2.3 — Button press feedback
  - [ ] 10.2.4 — Modal open/close (ease-out-quart, NO bounce)
  - [ ] 10.2.5 — Sidebar link hover/active transitions
  - [ ] 10.2.6 — Form validation visual feedback

- [ ] **10.3 — UX critique & polish** (run `impeccable critique` then `impeccable polish`)
  - [ ] 10.3.1 — Full UX critique against impeccable heuristics
  - [ ] 10.3.2 — Fix issues: typography, contrast, spacing, hierarchy
  - [ ] 10.3.3 — Verify zero impeccable absolute bans present
  - [ ] 10.3.4 — Final consistency check across all 3 role dashboards

> 🛑 **PHASE 10 COMPLETE → Update CURRENT STATUS → Set Phase 11 / ⚙️ GENERAL → STOP**

---

### Phase 11 — Testing & Documentation `⚙️ GENERAL`

- [ ] **11.1 — Seed data**
  - [ ] 11.1.1 — `database/seed.sql`: 1 admin, 2 organizers, 10 voters (tbl_users + tbl_voters), 1 election, 5 positions, 10 candidates (2 per position), sample votes + vote_counts, sample logs

- [ ] **11.2 — Manual testing checklist**
  - [ ] 11.2.1 — Write checklist: login per role, admin CRUD all tables, organizer restricted access, voter full flow, emails, password reset, responsive

- [ ] **11.3 — Final README**
  - [ ] 11.3.1 — Update `README.md`: setup (XAMPP, DB import, PHPMailer config), default credentials, project structure, feature list

> ✅ **PROJECT COMPLETE**

---

## DECISIONS LOG

> Models: record any decisions you make here so future models have context.

| Date | Model | Decision |
|---|---|---|
| 2026-05-28 | Claude Opus 4.6 | Created initial plan. PRD's `send_email()` has `use` statements inside function body — this is invalid PHP. Will be corrected to file-level `use` + `require_once`. |
| 2026-05-28 | Claude Opus 4.6 | `tbl_users` and `tbl_voters` are treated as separate tables per PRD. No FK link added between them unless user specifies. |
| 2026-05-28 | Claude Opus 4.6 | Used real `password_hash()` output for admin default password. Login: `admin` / `admin123`. |
| 2026-05-28 | Claude Opus 4.6 | `log_action()` is inside `includes/auth.php` (not a separate file) since it's tightly coupled with auth events. Other phases can call it via `require_once auth.php`. |
| 2026-05-28 | Claude Opus 4.6 | All pages set `$page_title` variable — the DESIGN model can use this in headers/sidebar. |
| 2026-05-28 | Claude Opus 4.6 | MySQL import failed because XAMPP wasn't running. User must start Apache+MySQL in XAMPP before importing `database/voting_system.sql`. |
| | | |

---

## NOTES FOR SPECIFIC MODELS

### For `⚙️ GENERAL` (Gemini 3.5 Low)
- Keep code simple and functional. No over-engineering.
- Follow PRD code rules strictly (no sanitization, no ternaries, no edge cases).
- PHPMailer: download from GitHub, copy only the 3 source files. No Composer.

### For `🏗️ ARCHITECT` (Claude Opus 4.6)
- Use MySQLi with procedural style (not OOP) for consistency with typical XAMPP projects.
- Use `password_hash()` / `password_verify()` for passwords.
- All queries that touch user input should use prepared statements.
- Follow PRD code rules: explicit if/else, no ternaries, no unnecessary wrapping.

### For `🎨 DESIGN` (Gemini 3.5 High + Impeccable Skill)
- **You MUST use the Impeccable skill** (`impeccable teach`, `impeccable craft`, `impeccable document`, etc.).
- Read `.agents/skills/impeccable/SKILL.md` first if you haven't.
- Register = **product** (app UI, not marketing).
- Follow all impeccable shared design laws and absolute bans.
- Do NOT use TailwindCSS. Use vanilla CSS with OKLCH.
- Load Google Fonts via `<link>` tag (Inter for body).
