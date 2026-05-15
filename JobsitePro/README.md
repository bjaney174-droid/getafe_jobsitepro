# Getafe Jobsite - Complete Job Portal System

A modern, professional job portal system built with PHP and MySQL for Getafe, Bohol.

## Features

### For Job Seekers
- 🔍 Advanced job search and filtering
- 📋 Apply for multiple jobs
- 💾 Save favorite jobs
- 👤 Complete user profiles
- 📊 Application tracking
- ⭐ View featured opportunities

### For Employers
- 📝 Post and manage job listings
- 📊 View application analytics
- 🎯 Featured job listings
- 👥 Manage applicants
- 💼 Company profiles
- 📈 Job performance tracking

### Admin Features
- 👥 User management (ban/unban)
- 📋 Job moderation
- 📊 Application management
- 📝 System activity logs
- ⚙️ Site settings configuration
- 💾 Database backups

### Technical Features
- 🔐 Secure password hashing (bcrypt)
- 🛡️ CSRF token protection
- 📧 Email notifications
- 📱 Fully responsive design
- ♿ Accessibility support
- 🚀 Performance optimized

## Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Apache**: with mod_rewrite enabled
- **Composer**: (optional)

## Installation

### 1. Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE getafe_jobsite_pro;
USE getafe_jobsite_pro;

# Import SQL file
SOURCE database/dump.sql;
EXIT;