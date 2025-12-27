# SaaS E‑Learning Platform (Teacher‑Centric LMS)

## 1️⃣ Business Overview

### 1.1 Project Background
This project is a **teacher‑centric, multi‑tenant SaaS Learning Management System (LMS)** designed to enable individual teachers and educational institutions to operate their own fully branded online learning platforms.

Unlike generic e‑learning marketplaces, this system focuses on **empowering teachers as independent tenants**, giving them full ownership of their content, students, data, and business operations — without technical complexity.

The platform is designed to support the transition from traditional offline education centers to scalable, high‑performance digital learning environments.

---

### 1.2 Business Goals

• Enable teachers to sell and manage online courses under their own brand
• Replace or augment physical education centers with a scalable digital solution
• Provide teachers with tools to:
  - Manage courses and video content
  - Manage students, guardians, and enrollments
  - Create and manage exams with automatic correction
  - Track attendance, progress, and engagement
  - Generate student performance reports
• Operate under a subscription‑based SaaS model
• Support long‑term scalability and future feature expansion

---

### 1.3 Target Audience

**Primary Customers (Tenants)**
• Independent teachers
• Teachers operating private education centers
• Medium to large educational institutions

**End Users**
• Students enrolled in teacher courses
• Guardians (parents) linked to students

---

## 2️⃣ Platform Scope – MVP Functional Breakdown

The platform consists of two primary components:

1. Teacher Website (Student‑Facing)
2. Teacher Dashboard (Admin Panel)

---

## 2.1 Teacher Website (Student‑Facing)

Each subscribed teacher receives a **dedicated, isolated website** accessed via a subdomain‑based URL:

`teacher-name.platform-domain.com`

### Core Features

• Teacher landing page
• Course listing and course details pages
• Student registration and authentication
• Course enrollment
• Video lesson viewing (external hosting)
• Exam access and submission
• Viewing grades and exam results
• Lesson completion tracking
• Responsive design (desktop & mobile)

### Website Customization (MVP)

• Logo upload
• Primary and secondary color selection
• Basic theme/layout selection
• Font selection (optional)
• Editable public content:
  Hero section
  About section
  Contact information

No developer intervention is required for customization.
---

## 2.2 Teacher Dashboard (Admin Panel)

The dashboard allows teachers to manage their educational business end‑to‑end.

### 2.2.1 Course Management

• Create, update, and delete courses
• Upload and manage video lessons (external video providers)
• Organize lessons within courses
• Set course pricing
• Publish / unpublish courses

---

### 2.2.2 Student & Guardian Management

• View registered students
• Manually enroll students
• Assign students to courses
• View student profiles and progress
• Link guardians (parents) to students
• Manage guardian contact data

---

### 2.2.3 Exams & Assessments

• Create exams per course
• Multiple‑choice questions (MCQ) – MVP scope
• Configurable exam duration
• Configurable attempt limits per exam
• Automatic exam correction
• Time window support (start/end dates)
• View and export exam results (CSV, XLS, XLSX)

---

### 2.2.4 Attendance & Progress Tracking

• Track lesson completion per student
• Track exam participation and completion
• Attendance rules:
  - Late threshold
  - Absence detection
• Attendance reports per course

---

### 2.2.5 Reports & Analytics (Basic)

• Total students per tenant
• Active students
• Total courses
• Exam performance summaries
• Student‑level monthly performance reports

---

### 2.2.6 Website & SEO Management

• Manage website appearance
• Preview customization changes
• Basic SEO fields (title & description)

---

## 3️⃣ Notification & Tracking System

The platform includes an integrated **event‑driven tracking and notification system**.

### Notification Channels (MVP)

• WhatsApp (primary channel – manual provider integration)
• Email (fallback)

### Notification Triggers

• Student attendance events
• Student absence or late arrival
• Exam completion
• Exam results publishing
• Monthly performance summaries

Notifications can be sent to:
• Students
• Guardians
• Teachers

---

## 4️⃣ User Roles & Access Control

The system uses a **unified authentication system** with role‑based access control.

### Tenant‑Level Roles

• Teacher (Owner)
• Assistant / Moderator
• Student
• Guardian (Read‑only access + notifications)

### Platform‑Level Role

• Super Admin

Authorization is enforced through policies and permissions at the service layer.

---

## 5️⃣ Technical & Architectural Requirements

### 5.1 Multi‑Tenancy Model

• **Separate database per tenant**
• Strict data isolation at the infrastructure and application layers
• Each tenant maintains an independent lifecycle:
  - Onboarding
  - Activation
  - Suspension
  - Expiration

---

### 5.2 Architecture Style

The system must be implemented as a **Scalable (Smart) Monolith** using a **Service‑Based Architecture**.

#### Controllers
• Handle HTTP requests/responses
• Validate requests
• Authorize access
• Delegate execution to services

#### Services
• Contain all business logic
• Enforce tenant context
• Coordinate workflows across modules
• Dispatch domain events

#### Repositories
• Handle database access only
• Abstract persistence logic
• Never contain business rules

No business logic is allowed inside controllers or models.

---

### 5.3 Modular Architecture

Each business domain is implemented as an independent module.

Example modules:
• Courses
• Students
• Guardians
• Exams
• Attendance
• Reports
• Notifications
• Subscriptions
• Website Management

Each module contains its own:
• Controllers
• Services
• Models
• Requests
• Routes
• Repositories

Inter‑module communication occurs **only via service interfaces**.

---

### 5.4 Event‑Driven Internal Design

The system relies on internal domain events such as:
• StudentAttendedLesson
• StudentAbsent
• ExamCompleted
• ReportGenerated

Events are used for:
• Notifications
• Reports
• Attendance tracking
• Future integrations

---

### 5.5 Non‑Functional Requirements

• High performance with paginated queries
• Asynchronous processing for notifications and reports
• Strong tenant data isolation
• Centralized logging and audit trails
• API‑first design for future mobile and frontend clients

---

## 6️⃣ Subscription & SaaS Model

• Subscription‑based access per tenant
• Single plan for MVP
• Feature and usage limits configurable
• Grace period support
• Read‑only mode after expiration

---

## 7️⃣ Future Phases (Out of MVP)

• Live streaming sessions
• Video DRM and content protection
• Question randomization
• Advanced analytics
• Mobile applications
• Provider‑agnostic notification system

---

## 8️⃣ Estimation Request

The development team is requested to provide:

1. Estimated timeline
2. Estimated cost
3. Team size
4. Development milestones
5. Assumptions and risks

---

**This document serves as the authoritative technical and product reference for the MVP and future platform evolution.**

