# Database Design – Academic Competition Management System

This folder contains the **full PostgreSQL database dump** of the project.

## File Description

### `full_database_dump_postgresql.sql`

- Full database schema and sample data
- Includes:
  - 30+ normalized tables
  - Complex relationships (1–1, 1–N, N–N)
  - Strong data integrity using:
    - PRIMARY KEY
    - FOREIGN KEY
    - UNIQUE
    - CHECK constraints
- Covers full academic competition lifecycle:
  - Departments & lecturers
  - Students & teams
  - Competitions & rounds
  - Submissions, grading, results
  - Budgeting & rewards
  - Attendance & activity tracking

## 🚀 Usage

```bash
psql -U postgres -d academic_competition -f full_database_dump_postgresql.sql
