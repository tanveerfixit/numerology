# Project Directives & Workflow Rules

## GitHub Push Rule
- **DO NOT automatically push to GitHub on every file edit or feature creation.**
- Only push to GitHub when the user explicitly requests to push (e.g. "push to github", "update repo", "git push").
- Keep all local development and git commits local until requested.

## Tech Stack & Architecture Rules
- Technology Stack: Pure PHP, SQLite (`abjad.db`), PDO. No Node.js server.
- Database: Strict local SQLite file `abjad.db`. No online/external DB connection.
- Visual Theme: Pure light theme ONLY (`#f8fafc`, `#ffffff`). No dark theme. Keep padding and margins minimal.
- Public Calculator: Calculator remains open to all users (including unauthenticated guests). Saving calculations and saved names history log (`saved.php`) are strictly reserved for Staff & Admin accounts.
- Circumstance Q&A Chat: Registered public users can submit questions and view admin responses in a single chat history thread. Form inputs auto-clear upon submission.
- Access Rights: Only Admin accounts have authority to delete chat messages or user chat history.
