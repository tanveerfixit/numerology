# Project Directives & Workflow Rules

## GitHub Push Rule
- **DO NOT automatically push to GitHub on every file edit or feature creation.**
- Only push to GitHub when the user explicitly requests to push (e.g. "push to github", "update repo", "git push").
- Keep all local development and git commits local until requested.

## Tech Stack & Architecture Rules
- **Technology Stack**: Pure PHP, MySQL via PDO. No Node.js server.
- **Database**: MySQL (`u583652021_numerology` on Hostinger / `.env`). All tables and queries use MySQL with `utf8mb4_unicode_ci` charset. No SQLite.
- **Visual Theme**: Pure light theme ONLY (`#f8fafc`, `#ffffff`). No dark theme. Keep padding and margins minimal.
- **Radius Guidelines**: Zero border radius (`border-radius: 0;`) across all inputs, divs, cards, backgrounds, badges, and modals. Minimum 2px radius (`border-radius: 2px;`) strictly for buttons.
- **Public Calculator**: Calculator remains open to all users (including unauthenticated guests). Saving calculations and saved names history log (`saved.php`) are strictly reserved for Staff & Admin accounts.
- **Circumstance Q&A Chat**: Registered public users can submit questions and view admin responses in a single chat history thread. In ongoing consultation threads, only the Question/Message field is required, while optional context fields are collapsed/optional.
- **Access Rights**: Only Admin accounts have authority to delete chat messages, user chat history, manage user accounts, or configure elemental theme colors.

## Responsive Design Guidelines
- **Universal Mobile & Tablet Responsiveness**: Every page (`index.php`, `calculator.php`, `saved.php`, `view_name.php`, `profile.php`, `admin.php`) must be fluidly responsive across all device viewports (desktop, tablet, and mobile screens down to 320px).
- **Adaptive Layouts**:
  - Use flexible grid systems with `repeat(auto-fit, minmax(..., 1fr))` or responsive breakpoints (`@media (max-width: 768px)`, `@media (max-width: 640px)`, `@media (max-width: 480px)`).
  - Wrap desktop data tables in responsive containers (`overflow-x: auto;`) or provide mobile-friendly card alternatives.
  - Form controls and inputs must span 100% width on mobile viewports.
  - Modals and drawers must adapt to viewport dimensions (`max-height: 90vh; max-width: 95vw; overflow-y: auto;`).

## Built-in Virtual Urdu/Arabic Keyboard Directives
- **Universal Keyboard Access**: All text inputs and textareas that accept Arabic, Urdu, or Persian text (e.g. calculator input, profile target name, consultation questions, admin reply boxes) must support the built-in virtual Urdu keyboard.
- **Dynamic Target Focus Tracking**: The virtual keyboard must automatically detect which input field or textarea is currently focused/active (`activeInputField`) and insert characters directly into that field.
- **Placement & Ergonomics**:
  - Provide a dedicated `⌨️ Urdu Keyboard` toggle button in the header/label row immediately adjacent to the relevant input field.
  - Position the keyboard drawer directly adjacent or collapsible beneath the input area for frictionless accessibility.
