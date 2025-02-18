# Employee Task Management System (ETMS)

The Employee Task Management System (ETMS) is a web-based platform designed to streamline task management for administrators and employees. Admins can manage employee records, assign tasks, and monitor progress. Employees can track their tasks, receive notifications, and update their task status.

## System Roles
- **Admin**: Manages employees, assigns tasks, and oversees the system.
- **Employee**: Views tasks, notifications, and updates task status.

## Admin Panel

1. **Dashboard** (`dashboard.php`)
   - View an overview of employee tasks and system activity.
   
2. **Employee Management**
   - **Add Employee** (`add_employee.php`): Register a new employee.
   - **Delete Employee** (`delete_employee.php`): Remove an employee from the system.
   
3. **Task Management**
   - **Add Task** (`add_task.php`): Create new tasks and assign them to employees.
   - **Assign Task** (`assign_task.php`): Allocate specific tasks to employees.
   - **Edit Task** (`edit_task.php`): Modify task details.
   - **Manage Tasks** (`manage_tasks.php`): View and update all tasks.
   
4. **Salary Management**
   - **Manage Salaries** (`manage_salaries.php`): Handle employee salary records.

## Employee Panel

1. **Dashboard** (`dashboard.php`)
   - View assigned tasks and recent activity.
   
2. **Task Management**
   - **Tasks** (`tasks.php`): View the list of tasks assigned to the employee.
   
3. **Notifications**
   - **Notifications** (`notifications.php`): View system messages and updates.

## Authentication & Security

- **Login** (`index.php`): Enter credentials to access the system.
- **Forgot Password** (`forgot_password.php`): Reset password if forgotten.
- **Reset Password** (`reset_password.php`): Change password securely.
- **Logout** (`logout.php`): Safely exit the system.

## System Configuration

- **Database** (`db.sql`): Contains the database schema and necessary tables.
- **Authentication** (`auth.php`): Handles user authentication.
- **Header & Footer** (`header.php` & `footer.php`): Manages the layout and navigation.

## How to Use the System

### For Admins:
1. **Login**: Use the default credentials:
   - **Username**: `admin`
   - **Password**: `password`
   
2. **Navigate to the Dashboard**: View system statistics, such as user activity and task completion status.

3. **Go to Add Employee**: Register new users (employees) by entering their details.

4. **Manage Tasks**: Create, assign, edit, or delete tasks for employees.

5. **Monitor Employee Progress**: Keep track of task completion via the dashboard.

6. **Manage Salaries**: Manage and update employee salaries under the 'Manage Salaries' section.

7. **Logout**: Click the logout button to securely exit the system.

### For Employees:
1. **Login**: Use the username and password provided by the admin.

2. **Check the Dashboard**: View your assigned tasks, deadlines, and overall progress.

3. **Click on Tasks**: Get detailed descriptions of each task and their respective deadlines.

4. **Update Task Progress**: Once a task is completed, update your progress in the system.

5. **View Notifications**: Stay updated on new tasks, deadlines, or any important information.

6. **Reset Password**: If you need to reset your password, use the reset link provided via email. You will be given a token that allows you to reset your password.

7. **Logout**: Ensure secure logout when you're done.

## Troubleshooting & Support

- Ensure proper database setup before using the system.
- If login fails, verify credentials or reset the password.
- If tasks are not visible, check employee assignment in the database.
- For system errors, check logs and database connections.
