# Event Registration System

A PHP and MySQL based Event Registration System developed as a server-side programming project.  
The system allows users to view events, search/filter available events, and register for them. Admins can manage events and registrations through a protected dashboard.

## Features

### User Features
- View all events
- Search events by title
- Filter only available events
- Register for events
- View remaining capacity
- See clear validation messages

### Admin Features
- Admin login system
- Create new events
- Edit existing events
- Delete events
- View registrations
- Edit registrations
- Remove registrations
- Audit log for admin actions

## Technologies Used

- PHP
- MySQL
- HTML
- CSS
- XAMPP
- phpMyAdmin

## Project Structure

```text
event-registration-system/
│
├── database/
│   └── it306_project.sql
│
├── docs/
│   ├── IT306_FINALREPORT.pdf
│   └── README.pdf
│
├── admin.php
├── create_events.php
├── dashboard.php
├── db.php
├── edit_event.php
├── edit_registration.php
├── index.php
├── logout.php
├── register.php
├── show_events.php
├── style.css
└── success.php
