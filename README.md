📓 Smart Journal

A private and modern web-based journaling application where users can write, reflect, track moods, and build writing habits.

Built using PHP, MySQL, HTML, CSS, and JavaScript.

✨ Features
🔒 Secure user authentication (login/register)
📝 Create, edit, and delete journal entries
😊 Mood tracking system
📊 Writing analytics & insights
🔥 Daily streak tracking
⭐ Favorite entries
🗂 Archive system
🗑 Trash with restore option
📷 Upload photos to entries
🌙 Clean UI (dark/light ready design)
💾 Export data (JSON/backup support)
🛠️ Tech Stack
Backend: PHP (PDO)
Database: MySQL
Frontend: HTML5, CSS3, JavaScript
Server: Apache (XAMPP recommended)
🚀 Installation Guide
1. Clone the project
git clone https://github.com/esrutina/smart-journal.git
2. Move to htdocs (if using XAMPP)
C:\xampp\htdocs\smart-journal
3. Create database
Open phpMyAdmin
Create database:
smart_journal
Import this file:
database/smart_journal.sql
4. Configure database connection

Edit config.php:

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smart_journal');
5. Run the project

Open in browser:

http://localhost/smart-journal
📁 Project Structure
smart-journal/
├── database/
│   └── smart_journal.sql
├── uploads/
├── config.php
├── index.php
├── login.php
├── register.php
├── dashboard.php
├── entries.php
├── new-entry.php
├── edit_entry.php
├── view_entry.php
├── analytics.php
├── style.css
└── README.md
🔐 Security Features
Password hashing (password_hash)
SQL injection protection (PDO prepared statements)
XSS protection (htmlspecialchars)
Session-based authentication
🎯 Future Improvements
Email password reset
Search & filtering system
Mobile app version
AI journal insights
Cloud backup system
👨‍💻 Author

Esrael Enyew
Software Engineering Student
Future Full Stack Developer

📌 Project Link
https://github.com/esrutina/smart-journal
