# Spirit Production System Documentation

## Table of Contents
1. [Project Overview](#project-overview)
2. [Technology Stack](#technology-stack)
3. [Project Structure](#project-structure)
4. [Key Components](#key-components)
5. [Authentication & Authorization](#authentication--authorization)
6. [Database Schema](#database-schema)
7. [UI Components](#ui-components)
8. [API Routes](#api-routes)
9. [Styling Guide](#styling-guide)
10. [Development Guidelines](#development-guidelines)
11. [Recent Changes](#recent-changes)

## Project Overview
A production management system built with React and Laravel, featuring user management, production tracking, and live monitoring capabilities.

## Technology Stack
- **Frontend**: React.js with Inertia.js
- **Backend**: Laravel
- **Database**: MySQL
- **UI Framework**: Tailwind CSS with DaisyUI
- **State Management**: React Hooks + Inertia.js forms
- **Authentication**: Laravel Breeze

## Project Structure
spirit/ ├── app/ # Laravel backend code ├── resources/ │ ├── js/ │ │ ├── Components/ # Reusable React components │ │ ├── Layouts/ # Page layouts │ │ └── Pages/ # Page components │ └── css/ # Stylesheets ├── routes/ # API and web routes └── database/ # Database migrations and seeders


## Key Components

### Pages
1. **Dashboard ([Dashboard.jsx](cci:7://file:///c:/Spirit/sprt/resources/js/Pages/Dashboard.jsx:0:0-0:0))**
   - Main production overview
   - Dark theme with blue accents
   - SAP data display
   - File upload functionality

2. **Users Management ([Admin/Users.jsx](cci:7://file:///c:/Spirit/sprt/resources/js/Pages/Admin/Users.jsx:0:0-0:0))**
   - Staff listing and management
   - CRUD operations for users
   - Role-based access control
   - Modal forms for add/edit/delete operations

3. **Live Production ([LiveProduction.jsx](cci:7://file:///c:/Spirit/sprt/resources/js/Pages/LiveProduction.jsx:0:0-0:0))**
   - Real-time production monitoring
   - Production status updates

### UI Components
1. **Modal Components**
   - Standard styling:
     ```css
     Background: #1A1A2E
     Text: white
     Input fields: #0A0A29
     Primary buttons: #1e3a8a
     Hover state: #2e4a9a
     ```
   - Common modal structure:
     ```jsx
     <dialog className="modal">
       <div className="modal-box bg-[#1A1A2E] text-white">
         <h2 className="text-2xl font-bold mb-6">Title</h2>
         <div className="content">...</div>
         <div className="mt-6 flex justify-end space-x-2">
           <button className="bg-[#1e3a8a] ...">Action</button>
           <button className="bg-[#0A0A29] ...">Close</button>
         </div>
       </div>
     </dialog>
     ```

2. **Table Components**
   - Consistent styling with dark theme
   - Bordered cells
   - Centered content
   - Header styling with #1e3a8a background

### Forms
1. **User Management Forms**
   - Add/Edit Staff Form Fields:
     - Staff ID (required)
     - Role (user/admin)
     - Name (required)
     - Designation (optional)
   - Delete Confirmation Form:
     - User details display
     - Confirmation buttons

## Authentication & Authorization

### User Roles
1. **Admin**
   - Full system access
   - User management capabilities
   - Production data management

2. **User**
   - Limited system access
   - Production data viewing
   - Personal profile management

### Authentication Flow
1. Login (`Auth/Login.jsx`)
2. First-time login handling (`Auth/FirstTimeLogin.jsx`)
3. Profile management (`Profile/*`)

## Database Schema

### Key Tables
1. **hub_data**
   - Stores production data from the hub
   - Fields: id, joborder, partno, quality, timestamps

2. **hub_reportdata**
   - Stores production reports
   - Fields: reportid, joborder, partno, quality, timestamps

3. **comparison_results**
   - Stores comparison results between hub data and other systems
   - Fields: id, joborder, partno, timestamps

## Recent Changes

### PLC to Hub Renaming (June 2024)
- Renamed all PLC-related components to use 'Hub' terminology for consistency
- Updated database tables:
  - `plc_data` → `hub_data`
  - `plc_report_data` → `hub_reportdata`
- Updated model names and references:
  - `PLCController` → `HubController`
  - `PLCReportDataController` → `HubReportDataController`
  - `plcData` model → `HubData`
  - `PLCReportData` model → `HubReportData`
- Updated API routes:
  - `/plc-data` → `/hub-data`
  - `/plc-report-data` → `/hub-report-data`

### Frontend Updates
- Updated all API calls to use new endpoint names
- Maintained backward compatibility where needed
- Updated documentation to reflect changes

## UI Components
1. **Modal Components**
   - Standard styling:
     ```css
     Background: #1A1A2E
     Text: white
     Input fields: #0A0A29
     Primary buttons: #1e3a8a
     Hover state: #2e4a9a
     ```
   - Common modal structure:
     ```jsx
     <dialog className="modal">
       <div className="modal-box bg-[#1A1A2E] text-white">
         <h2 className="text-2xl font-bold mb-6">Title</h2>
         <div className="content">...</div>
         <div className="mt-6 flex justify-end space-x-2">
           <button className="bg-[#1e3a8a] ...">Action</button>
           <button className="bg-[#0A0A29] ...">Close</button>
         </div>
       </div>
     </dialog>
     ```

2. **Table Components**
   - Consistent styling with dark theme
   - Bordered cells
   - Centered content
   - Header styling with #1e3a8a background

## API Routes

### Production Data Endpoints
- `GET /hub-data` - Get unprocessed production data
- `GET /hub-report-data` - Get production reports with optional date filtering
- `POST /upload-sap` - Upload SAP data
- `GET /sap-data` - Get SAP data

### Comparison Endpoints
- `GET /comparison-results` - Get comparison results
- `POST /store-comparison` - Store comparison results

### User Management Endpoints
- `GET /users` - Get all users (admin only)
- `POST /user` - Create new user (admin only)
- `PATCH /user` - Update user (admin only)
- `DELETE /user` - Delete user (admin only)

Routes are defined in:
•	routes/web.php: Web routes
•	routes/admin.php: Admin-specific routes
•	routes/api.php: API endpoints

## Styling Guide

### Color Palette
```css
/* Primary Colors */
--primary-dark: #1A1A2E;    /* Modal/container background */
--primary-darker: #0A0A29;  /* Input fields */
--primary-blue: #1e3a8a;    /* Buttons, headers */
--primary-blue-hover: #2e4a9a;

/* Text Colors */
--text-primary: #ffffff;    /* Main text */
--text-secondary: #9ca3af;  /* Secondary text */
--text-error: #ef4444;      /* Error messages */

.primary-button {
  @apply bg-[#1e3a8a] text-white px-6 py-2 rounded hover:bg-[#2e4a9a];
}

.secondary-button {
  @apply bg-[#0A0A29] text-white px-6 py-2 rounded hover:bg-[#1A1A2E];
}

.input-field {
  @apply bg-[#0A0A29] text-white border-none w-full;
}

## Development Guidelines

Adding New Features
1.	Create component in appropriate directory
2.	Follow existing styling patterns
3.	Implement error handling
4.	Add form validation where needed
5.	Update routes if required

Code Style
1.	Use functional components with hooks
2.	Follow existing naming conventions
3.	Maintain consistent styling patterns
4.	Document complex logic
5.	Use TypeScript types/interfaces where possible

Best Practices
1.	Reuse existing components
2.	Follow modal pattern for forms
3.	Implement proper error handling
4.	Use consistent styling
5.	Add proper validation
6.	Document new features

Maintenance
1.	Regular dependency updates
2.	Database backups
3.	Error logging and monitoring
4.	Performance optimization
5.	Security updates

Testing
1.	Unit tests in tests/Unit
2.	Feature tests in tests/Feature
3.	Frontend component testing
4.	API endpoint testing


This documentation provides a comprehensive overview of the project structure, components, and development guidelines. Feel free to modify or expand it based on your specific needs!