# Student Sequence Diagrams

This file groups the main sequence diagrams for the `kmr/student` module.

PlantUML separate files are available in:
- `kmr/student/plantuml/`

Main detailed diagrams:
- Detailed Diagram 1: student registration and authenticated session start
- Detailed Diagram 2: course enrollment and lesson progression

Other diagrams are intentionally lighter.

## Main Diagram 1: Registration And Session Start

Source flow:
- UI page: `pages/registre.php`
- Action: `backend/actions/register.php`
- Shared layer: `backend/includes/bootstrap.php`, `auth.php`, `session.php`, `helpers.php`

```mermaid
sequenceDiagram
    actor Student
    participant RegisterPage as Register Page\npages/registre.php
    participant RegisterAction as Register Action\nbackend/actions/register.php
    participant Auth as Auth Layer
    participant DB as MySQL DB\netudiant
    participant OffersPage as Offers Page\npages/offres.php

    Student->>RegisterPage: Open registration page
    RegisterPage-->>Student: Show form
    Student->>RegisterAction: Submit registration form
    RegisterAction->>RegisterAction: Validate input
    RegisterAction->>DB: Check if CIN already exists
    alt Invalid data or CIN already used
        RegisterAction-->>RegisterPage: Redirect back to registration
    else Valid registration
        RegisterAction->>DB: Insert new student
        RegisterAction->>Auth: Create student session
        RegisterAction-->>OffersPage: Redirect to offers page
        OffersPage-->>Student: Student enters platform
    end
```

## Main Diagram 2: Course Enrollment And Lesson Progression

Source flow:
- UI pages: `pages/offres.php`, `pages/cours.php`, `pages/lesson(a).php`, `pages/visualiser_leçon.php`
- Actions: `backend/actions/enroll_free_course.php`, `backend/actions/traitter_payement(a).php`, `backend/actions/valider_lesson.php`

```mermaid
sequenceDiagram
    actor Student
    participant OffersPage as Offers Page\npages/offres.php
    participant EnrollAction as Enrollment Action
    participant DB as MySQL DB
    participant CoursesPage as My Courses\npages/cours.php
    participant LessonsPage as Lessons Page
    participant ValidateAction as Validate Lesson

    Student->>OffersPage: View available courses
    OffersPage-->>Student: Show free and premium offers

    alt Free course
        Student->>EnrollAction: Send free enrollment request
        EnrollAction->>DB: Save enrollment
        EnrollAction-->>CoursesPage: Redirect to my courses
    else Premium course
        Student->>EnrollAction: Confirm payment
        EnrollAction->>DB: Save paid enrollment
        EnrollAction-->>CoursesPage: Redirect to my courses
    end

    CoursesPage->>DB: Load enrolled courses
    DB-->>CoursesPage: Student courses
    Student->>LessonsPage: Open course lessons
    Student->>ValidateAction: Mark lesson as completed
    ValidateAction->>DB: Save lesson progress
    ValidateAction-->>LessonsPage: Return updated lesson list
```

## Light Diagram 3: Login

```mermaid
sequenceDiagram
    actor Student
    participant LoginPage as Login Page
    participant LoginAction as Action login.php
    participant Bootstrap as Bootstrap
    participant DB as MySQL DB
    participant Auth as Auth Layer
    participant CoursesPage as My Courses

    Student->>LoginPage: Open login form
    Student->>LoginAction: Submit CIN + password
    LoginAction->>Bootstrap: Load shared environment
    LoginAction->>DB: Find student by CIN
    alt Invalid account or bad password
        LoginAction-->>LoginPage: Redirect back to login
    else Valid credentials
        LoginAction->>Auth: Create authenticated session
        LoginAction-->>CoursesPage: Redirect to `cours.php`
    end
```

## Light Diagram 4: Task Management

Grouped actions:
- `create_task.php`
- `update_task_status.php`
- `toggle_task.php`

```mermaid
sequenceDiagram
    actor Student
    participant TasksPage as Tasks Page
    participant TaskAction as Task Actions
    participant Bootstrap as Bootstrap
    participant DB as MySQL DB\ntasks

    Student->>TasksPage: Open task board
    TasksPage->>Bootstrap: Require authenticated session
    TasksPage->>DB: Load tasks by user_id
    DB-->>TasksPage: Tasks grouped by status
    Student->>TaskAction: Create task or move task to next state
    TaskAction->>Bootstrap: Validate authenticated request
    TaskAction->>DB: INSERT, UPDATE, or DELETE task
    TaskAction-->>TasksPage: Redirect back to board
    TasksPage-->>Student: Show refreshed columns
```

## Light Diagram 5: Reclamation With Attachments

```mermaid
sequenceDiagram
    actor Student
    participant ReclamationPage as Reclamation Page
    participant ReclamationAction as create_reclamation.php
    participant Bootstrap as Bootstrap
    participant Filesystem as Upload Folder
    participant DB as MySQL DB\nreclamations

    Student->>ReclamationPage: Open reclamation form
    Student->>ReclamationAction: Submit subject, message, attachments
    ReclamationAction->>Bootstrap: Check authenticated POST request
    ReclamationAction->>ReclamationAction: Validate subject/message and file size
    opt Attachments provided
        ReclamationAction->>Filesystem: Save uploaded files
    end
    ReclamationAction->>DB: INSERT reclamation row with attachment paths
    ReclamationAction-->>ReclamationPage: Redirect back
    ReclamationPage-->>Student: Request appears as submitted
```

## Light Diagram 6: Support Chat Message

```mermaid
sequenceDiagram
    actor Student
    participant SupportPage as Support Page
    participant SupportAction as create_support_message.php
    participant Bootstrap as Bootstrap
    participant DB as MySQL DB\nsupport_messages

    Student->>SupportPage: Open support thread
    SupportPage->>DB: Load previous messages
    DB-->>SupportPage: Ordered discussion history
    Student->>SupportAction: Send new message
    SupportAction->>Bootstrap: Validate session and POST body
    SupportAction->>DB: INSERT message with sender = student
    SupportAction-->>SupportPage: Redirect back
    SupportPage-->>Student: Updated thread is displayed
```

## Light Diagram 7: Profile Update And Photo Management

Grouped actions:
- `update_profile_settings.php`
- `delete_photo.php`

```mermaid
sequenceDiagram
    actor Student
    participant ProfilePage as Profile Page
    participant ProfileAction as Profile Update Action
    participant PhotoDelete as Delete Photo Action
    participant Bootstrap as Bootstrap
    participant DB as MySQL DB\netudiant

    Student->>ProfilePage: Open profile page
    ProfilePage->>DB: Load student profile and photo data
    DB-->>ProfilePage: Current profile information

    alt Update profile
        Student->>ProfileAction: Submit names, language, optional image
        ProfileAction->>Bootstrap: Validate authenticated request
        ProfileAction->>DB: UPDATE text fields and preferred language
        opt New image uploaded
            ProfileAction->>DB: UPDATE binary image data and MIME type
        end
        ProfileAction-->>ProfilePage: Redirect back
    else Delete photo
        Student->>PhotoDelete: Request photo deletion
        PhotoDelete->>DB: Set photo columns to NULL
        PhotoDelete-->>ProfilePage: Redirect back
    end
```

## Light Diagram 8: Logout And Profile Deletion

Grouped actions:
- `logout.php`
- `delete_profile.php`

```mermaid
sequenceDiagram
    actor Student
    participant Sidebar as Sidebar / Profile UI
    participant LogoutAction as logout.php
    participant DeleteProfile as delete_profile.php
    participant Auth as Auth Layer
    participant DB as MySQL DB
    participant HomePage as Student Home / Login

    alt Logout
        Student->>Sidebar: Click logout or leave page
        Sidebar->>LogoutAction: POST or GET logout request
        LogoutAction->>Auth: Destroy session and cookie
        LogoutAction-->>HomePage: Redirect to login page or finish beacon request
    else Delete profile
        Student->>DeleteProfile: Confirm account deletion
        DeleteProfile->>DB: Remove related rows from tasks, reclamations, support, etc.
        DeleteProfile->>DB: DELETE student row from `etudiant`
        DeleteProfile->>Auth: Destroy session
        DeleteProfile-->>HomePage: Redirect to student index
    end
```

## Coverage Summary

These diagrams cover the main student-side flows present in `kmr/student`:
- registration
- login
- free enrollment
- premium enrollment/payment
- course access
- lesson validation and progression
- task management
- reclamation
- support messaging
- profile update
- photo deletion
- logout
- profile deletion
