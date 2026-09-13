# 💰 CholoSave

**CholoSave** is a collaborative savings and investing platform that empowers friends or community members to form savings groups, invest together, request emergency funds, and make decisions democratically. Built with **Laravel**, **Tailwind CSS**, and a modular **frontend-backend API** design, it aims to revolutionize group-based financial planning.

---

## 🚀 Features

* 👥 Create or Join Savings Groups
* 🎯 Set Group Goals & Milestones
* 💬 Group Voting for Key Decisions (loan requests, membership, investments)
* 💸 Emergency Fund Request & Approval System
* 🧠 AI-Powered Investment Suggestions *(Bangladesh-focused)*
* 📟 Track Payments, Contributions & Investment Profits
* 📊 Leaderboard for Top Performing Groups
* 🛡️ Secure OTP-Verified Payment Gateway
* 🢑 Group Chat and Notifications
* 📄 Generate Group & Individual Investment Reports (PDF)
* 📚 Forum for Discussions on Finance & Investment

---

## 🧠 Project Architecture

```
Frontend (HTML, Tailwind, JS)
        |
     API Layer
        |
Backend (Laravel)
        |
Database (MySQL)
```

* **Frontend**: Clean and responsive UI built with HTML, TailwindCSS, and JavaScript.
* **Backend**: Laravel-powered REST API handling authentication, group logic, payments, and notifications.
* **Database**: MySQL (XAMPP) for user/group/payment records.

---

## 🛠️ Installation & Setup

### Prerequisites

* PHP 8.x
* Composer
* MySQL (e.g., XAMPP)
* Node.js & npm (for frontend assets)

### Clone the Repository

```bash
git clone https://github.com/yourusername/cholosave.git
cd cholosave
```

### Backend Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

* Configure your `.env` file with database and mail credentials.
* Run migrations:

```bash
php artisan migrate
php artisan db:seed  # Optional, if seeders available
```

### Frontend Setup

```bash
npm install
npm run dev
```

---

## 🌐 API Overview

CholoSave uses a REST API for communication between frontend and backend.

### Sample Endpoints

| Method | Endpoint                  | Description                       |
| ------ | ------------------------- | --------------------------------- |
| POST   | `/api/login`              | User login                        |
| GET    | `/api/groups`             | View all groups                   |
| POST   | `/api/groups`             | Create a new group                |
| POST   | `/api/payment/submit`     | Submit group payment              |
| POST   | `/api/emergency/request`  | Request emergency fund            |
| POST   | `/api/investment/suggest` | Get AI investment recommendations |

---

## 👨‍💻 Contributors

* **Montasir** – Team Lead, Backend & Payment Gateway
* **Nahin** – Frontend Developer
* **Mahi** – Group Features & UI
* **Imran** – AI Investment API Integration

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).

---

## 📌 To-Do / Roadmap

* [ ] Improve AI investment model accuracy
* [ ] Add mobile responsiveness to all views
* [ ] Deploy on shared server (e.g., cPanel or Vercel + Laravel backend on VPS)
* [ ] Add Two-Factor Authentication (2FA)
* [ ] Integrate real-time messaging for group chat
* [ ] Add user activity timeline and audit logs
* [ ] Build notification system (email + in-app)
* [ ] Admin dashboard for monitoring system health
* [ ] User tutorial or onboarding screens
* [ ] Add dark mode option
* [ ] API documentation with Swagger or Postman collection

---

## 🖼️ Screenshots

> *Coming soon!*

---

## 🌍 Demo / Deployment

> *Live demo link will be added here once deployed.*

---

## 🗣️ Feedback & Contributions

We welcome feedback, feature requests, and contributions! Feel free to fork this repo, create a branch, and submit a pull request.

For major changes, please open an issue first to discuss what you would like to change.

---

## 🙌 Support

If you find this project helpful, please give us a ⭐ on GitHub. It really helps!

Feel free to open issues for bugs or feature requests. We’d love your input!
