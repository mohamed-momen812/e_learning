You are a senior Laravel architect.

Your task is to generate the initial backend project structure for a
teacher-centric SaaS E-Learning platform called "Encyclopedia LMS",
STRICTLY following the document:
"Encyclopedia LMS – Technical Business Brief (v2)".

⚠️ This is NOT a demo or tutorial project.
This is a production-ready, scalable Smart Monolith.

====================================
GENERAL CONSTRAINTS
====================================

- Laravel version: 12
- API-first architecture
- Backend only (no Blade views)
- MySQL only
- No tests required at this phase
- Clean Code & SOLID principles are mandatory
- Avoid over-engineering
- Everything must be extensible and maintainable

====================================
MULTI-TENANCY
====================================

- Use `stancl/tenancy`
- Separate database per tenant
- Central database:
  - tenants
  - domains
  - subscriptions
- Tenant databases:
  - courses, students, exams, attendance, reports, etc.
- All tenant requests must be tenant-aware
- No cross-tenant access under any condition

====================================
ARCHITECTURE STYLE
====================================

Apply a **Service-Based Smart Monolith** with a **Modular Architecture**.

Enforce this flow strictly:

Controller → Service → Repository → Model

Rules:
- Controllers are thin (no business logic)
- Services contain all business rules
- Repositories handle database access only
- Models contain relations, casts, simple scopes only

====================================
MODULE SYSTEM
====================================

Use `nwidart/laravel-modules`.

Each business domain must be a self-contained module.

Initial required modules:
- Courses
- Students
- Guardians
- Exams
- Attendance
- Reports
- Notifications
- Subscriptions
- Website

Each module MUST contain (when applicable):
- Http/Controllers
- Http/Requests
- Http/Resources
- Services
- Repositories
- DTOs
- Events
- Listeners
- Models
- Routes/api.php
- Database (migrations, seeders)

No module is allowed to directly access another module’s models.

====================================
AUTHENTICATION & AUTHORIZATION
====================================

- Use Laravel Sanctum
- Single unified guard
- Use `spatie/laravel-permission`

Roles:
Tenant roles:
- teacher (tenant admin)
- assistant
- student
- guardian

Platform role:
- super_admin (outside tenant context)

Authorization rules:
- Policies at controller level
- Critical permissions validated in services

====================================
DATA TRANSFER & API DESIGN
====================================

- Use `spatie/laravel-data` for DTOs
- Requests handle validation only
- Controllers convert Requests → DTOs
- Services accept DTOs, never Request objects
- API responses must use Resources

====================================
EVENT-DRIVEN DESIGN
====================================

Use Laravel Events & Listeners for side effects.

Events examples:
- StudentAttendedLesson
- StudentAbsent
- ExamCompleted
- ReportGenerated

Rules:
- Services dispatch events
- Listeners handle notifications, reports, logs
- No notifications sent directly from services

====================================
NOTIFICATIONS
====================================

- Use Laravel Notifications
- WhatsApp is the primary channel (manual provider for MVP)
- Email as fallback
- Notification sending must be asynchronous-ready

====================================
REPORTS & EXPORTS
====================================

- Use `maatwebsite/excel`
- Reports generated on-demand (MVP)
- Architecture must allow future pre-aggregated reports

====================================
SETTINGS & CONFIGURATION
====================================

- Use `spatie/laravel-settings`
- Tenant-specific settings:
  - attendance rules
  - exam rules
  - notification preferences

====================================
ACTIVITY LOGGING
====================================

- Use `spatie/laravel-activitylog`
- Log critical actions (exam submission, attendance, enrollment)

====================================
DELIVERABLES
====================================

Generate:

1. The recommended folder & module structure
2. Base abstract classes (if needed) for:
   - BaseService
   - BaseRepository
3. Example skeleton for ONE module (Courses):
   - Controller
   - Service
   - Repository
   - DTO
   - Event
   - Listener
4. Clear separation between central app and tenant modules
5. No business logic implementation yet — structure only

====================================
IMPORTANT
====================================

- Do NOT generate UI code
- Do NOT generate fake logic
- Do NOT skip tenant awareness
- Do NOT simplify architecture
- Follow Laravel best practices exactly

Think like a senior backend engineer building a long-term SaaS platform.

====================================
PHASES & PRIORITY
====================================

The project development is divided into distinct phases to ensure a structured and prioritized approach:

====================================
Phase 0: Foundation
====================================

1. **Basic Project Structure & Architecture**
   - Implement the Service-Based Smart Monolith pattern.
   - Set up the modular architecture using `nwidart/laravel-modules`.
   - Configure multi-tenancy with `stancl/tenancy`.
   - Establish tenant registration, roles, and permissions.

2. **Database and Infrastructure Setup**
   - Configure the central and tenant databases.
   - Ensure strict data isolation and multi-tenancy integrity.

3. **Core Authentication & Authorization**
   - Set up Laravel Sanctum for unified authentication.
   - Implement `spatie/laravel-permission` for role and permission management.

====================================
Phase 1: Teacher Dashboard & Tracker
====================================

1. **Teacher Dashboard Development**
   - Build the comprehensive dashboard for tenants (teachers).
   - Integrate modules for course management, student and guardian management, exams, and attendance.

2. **Tracker System**
   - Implement the attendance and progress tracker for students.
   - Enable notifications and alerts to guardians for attendance and performance.

3. **Core Features & Customization**
   - Allow tenants to customize their websites (colors, fonts, content) directly from the dashboard.
   - Integrate the tracker with the dashboard for real-time updates and notifications.

====================================
Phase 2: Student Interface & Advanced Features
====================================

1. **Student Interface**
   - Develop the student-facing platform where learners interact with course content.
   - Ensure a responsive and intuitive user experience.

2. **Mobile Integration (Phase 2)**
   - Prepare the architecture to support mobile applications in the future.

3. **Advanced Features**
   -