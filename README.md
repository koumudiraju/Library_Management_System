# The Stark Library - Library Management System

## 📌 Project Overview
The **Stark Library** is a Library Management System designed for efficient book management and student-librarian interactions. It allows students to search for books, check their status, and borrow them, while librarians can manage books, issue/return them, and approve student registrations.

## ✨ Features

### 🌐 **Public Features (Before Login)**
- **Home Page**: Search bar to look for books displaying title, author, publication, category, and stock status.
- **Navigation Bar**:
  - About Page: Information about the library.
  - Events Page: List of upcoming and past events.
  - Login/Register Page: Students can register but need librarian approval before login.

### 🎓 **Student Dashboard (After Login & Librarian Approval)**
- **Search Books**: Students can check book availability.
- **Navigation Links**:
  - **Notifications**: Alerts for issued/returned books.
  - **History**: View past and pending book transactions.
  - **Profile**: Update personal details.
  - **Logout**

### 📚 **Librarian Dashboard (Admin Panel)**
- **Search Books**: Search and manage books.
- **Manage Books**: Add, edit, or delete books.
- **Approve Students**: Accept new student registrations.
- **Search Students**: Find students by ID or name, issue books, and view history.
- **Issue & Return Books**: Track lending records.
- **Profile Update & Logout**

## 🛠️ Technology Stack
- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL

## ⚙️ Requirements
The project is built in PHP and requires a local server environment like:
- [XAMPP](https://www.apachefriends.org/download.html)
- [WAMP](https://www.wampserver.com/en/)
- [MAMP](https://www.mamp.info/en/)

## 🚀 Installation & Setup
1. **Download & Extract**
   - Clone this repository or download the ZIP.
   - Extract it inside the `www` (WAMP) or `htdocs` (XAMPP) directory.

2. **Database Setup**
   - Open `phpMyAdmin` and create a new database named `library_management`.
   - Import the `sql_queries.sql` file from the project directory.

3. **Configure Database Connection**
   - Open `db.php` and update the database credentials:
     ```php
     $servername = "localhost";
     $username = "root"; // Default XAMPP/WAMP user
     $password = ""; // Leave empty if no password is set
     $dbname = "library_management";
     ```

4. **Run the Project**
   - Start Apache and MySQL in XAMPP/WAMP.
   - Open a browser and go to: `http://localhost/{YOUR_FOLDER_NAME}`

## 🔑 Default Admin Credentials
- **Email**: `admin@library.com`
- **Password**: `admin123`

## 📩 Need Help?
If you have any questions, feel free to reach out:
📧 **Email**: sjahnavi369@gmail.com

---

### 📝 Notes
- Make sure MySQL and Apache are running.
- Modify book and student details based on your needs.
- Enjoy managing your library efficiently! 🎉
