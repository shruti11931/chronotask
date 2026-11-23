# ⏱️ ChronoTask – Project Management & Task Tracking System

**ChronoTask** is a web-based project management and task tracking application designed to help teams organize projects, assign responsibilities, monitor progress, collaborate with team members, and manage deadlines efficiently.

It provides separate functionalities for Team Members, Team Leaders, and Faculty Mentors, along with an AI-powered project assistant that helps users understand project progress and task-related information.

## ✨ Key Features

### 1. User Authentication & Role Management
- User registration and login.
- Secure password hashing.
- Role-based accounts:
  - **Team Member:** Manage assigned tasks and submit completed work.
  - **Team Leader:** Create and manage projects, assign tasks, and monitor progress.
  - **Faculty Mentor:** Supervise projects and review submitted tasks.

### 2. Project Management
- Create and manage projects.
- View project details and status.
- Monitor project completion percentage.
- Track project progress based on completed tasks.
- Assign team members to projects.

### 3. Task Management
- Create and assign tasks to team members.
- Update task statuses.
- Track pending, in-progress, and completed tasks.
- Submit completed work for approval.
- Upload task submission files.
- Approve or review submitted tasks.

### 4. AI Project Assistant 🤖
- Integrated with the Groq API.
- Uses the Llama 3.3 70B Versatile model.
- Provides AI-generated project-related insights.
- Uses project information and task status as context.
- Supports project-specific conversations.

### 5. Team Collaboration
- Project-based messaging.
- View conversation history.
- Notifications and notification management.
- Share project-related updates.

### 6. Time Tracking
- Record hours spent on tasks.
- Add work descriptions and notes.
- View time logs.
- Track time spent across projects.

### 7. Analytics & Dashboard
- Project overview.
- Task distribution.
- Project completion statistics.
- Progress visualization.
- Recent activity tracking.

### 8. Additional Features
- Project timeline and roadmap.
- Mentor supervision dashboard.
- Deadline monitoring.
- Resource management.
- User settings and profile management.
- Email notification integration using PHPMailer.

## 🛠️ Technology Stack

| Category | Technologies |
|---|---|
| Frontend | HTML5, CSS3, JavaScript |
| UI Framework | Tailwind CSS |
| Backend | PHP |
| Database | MySQL |
| Database Connectivity | MySQLi |
| AI Integration | Groq API |
| AI Model | Llama 3.3 70B Versatile |
| Charts | Chart.js |
| Email Integration | PHPMailer |
| Local Server | XAMPP |
| Version Control | Git & GitHub |

## 📂 Project Structure

```text
ChronoTask/
│
├── ajax/
│   ├── ask_ai.php
│   └── track_session.php
│
├── pages/
│   ├── add_project.php
│   ├── add_task.php
│   ├── ai_assistant.php
│   ├── analytics.php
│   ├── assign_task.php
│   ├── chat_view.php
│   ├── dashboard_home.php
│   ├── mentor_dashboard.php
│   ├── project_details.php
│   ├── projects.php
│   ├── tasks.php
│   ├── timeline.php
│   ├── timelog.php
│   └── users.php
│
├── uploads/
│   ├── profiles/
│   └── submissions/
│
├── vendor/
│   └── phpmailer/
│
├── index.php
├── login.php
├── register.php
├── db.php
├── config.php
├── mail_helper.php
├── check_deadlines.php
│
└── README.md
```

## ⚙️ Installation & Setup

Follow these steps to run ChronoTask locally.

### Prerequisites

- XAMPP
- PHP 8.x recommended
- MySQL
- Web browser
- Groq API key (for AI assistant functionality)

### Step 1: Clone the Repository

```bash
git clone https://github.com/shruti11931/chronotask.git
```

### Step 2: Move Project to XAMPP

Move the project folder into:

```text
C:\xampp\htdocs\
```

### Step 3: Start XAMPP

Open XAMPP Control Panel and start:

- Apache
- MySQL

### Step 4: Configure Database

1. Open phpMyAdmin:

   http://localhost/phpmyadmin

2. Create a database named:

```sql
CREATE DATABASE promage_db;
```

3. Import your project database SQL file if available.

**Note:** The current repository does not include a database export. The required database tables must be created or imported before running the application.

### Step 5: Configure Database Connection

Open `db.php` and configure your MySQL credentials:

```php
$conn = new mysqli(
    "localhost",
    "root",
    "",
    "promage_db"
);
```

Update the credentials according to your local environment.

### Step 6: Configure Groq API

Generate an API key from:

https://console.groq.com/keys

Configure the `GROQ_API_KEY` environment variable for PHP/Apache.

Do not hardcode your API key or upload it to GitHub.

### Step 7: Run the Application

Open your browser and visit:

```text
http://localhost/chronotask/
```

Register an account and log in to access the application.

## 🔐 Security Considerations

- Passwords are stored using PHP password hashing.
- API keys should be stored outside source control.
- Database credentials should not be exposed publicly.
- Uploaded documents should be handled securely.
- Never commit `.env` files or private credentials.

## 🚀 Future Enhancements

- Real-time notifications using WebSockets.
- Advanced project reporting.
- Improved AI-based project recommendations.
- Automated task prioritization.
- Calendar integration.
- Enhanced mobile responsiveness.
- Advanced team performance analytics.

## 👩‍💻 Developer

**Shruti Jadhav**

Computer Engineering Student | PHP | MySQL | Python | AI/ML | Web Development

- GitHub: https://github.com/shruti11931
- LinkedIn: https://linkedin.com/in/shruti-jadhav-832659360

---

⭐ If you find this project interesting, consider giving the repository a star!

**ChronoTask – Plan Better. Track Smarter. Deliver Faster.**
