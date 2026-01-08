# Crime Data Analytics - Product Backlog

**Last Updated:** 2026-01-06
**Project:** Crime Data Analytics System
**Version:** 1.0

---

## 1. Dashboard

### 1.1 System Overview
**Status:** ✅ Completed

**Tasks:**
- [x] Create system-overview.php
- [x] Display total crime count (placeholder: 12,847)
- [x] Display recent crime activity cards
- [x] Show summary cards (Total Crimes, Crimes Today, Active Alerts)
- [x] Display trend indicators (up/down arrows with percentages)
- [x] Secure page using PHP session (middleware/auth.php)
- [x] Implement responsive design
- [x] Add breadcrumb navigation
- [ ] Connect to real crime database
- [ ] Implement real-time data fetching
- [ ] Add date range filters

**Files:**
- `frontend/admin-page/system-overview.php`
- `frontend/css/system-overview.css`

---

### 1.2 Analytics Summary
**Status:** ✅ Completed (UI only)

**Tasks:**
- [x] Create analytics-summary.php
- [x] Display page layout
- [x] Secure page using PHP session
- [ ] Show mini charts (trend snapshot)
- [ ] Display top crime types (bar chart)
- [ ] Display high-risk areas (map preview)
- [ ] Fetch data using PHP queries from database
- [ ] Render charts using Chart.js
- [ ] Add export functionality

**Files:**
- `frontend/admin-page/analytics-summary.php`

**Dependencies:**
- Chart.js library
- Crime database with real data

---

## 2. Crime Mapping & Heatmaps

### 2.1 Crime Mapping
**Status:** 🚧 In Progress (Placeholder)

**Tasks:**
- [x] Create crime-mapping.php
- [x] Add to sidebar navigation
- [ ] Integrate Google Maps API
- [ ] Display crime locations as markers
- [ ] Add marker clustering for high-density areas
- [ ] Implement click event for marker details
- [ ] Add filter controls (crime type, date range, severity)
- [ ] Show crime details in info window
- [ ] Add search by location
- [ ] Implement area boundary overlays

**Files:**
- `frontend/admin-page/crime-mapping.php`

**Dependencies:**
- Google Maps JavaScript API key
- Crime location coordinates in database

---

### 2.2 Heatmaps
**Status:** 🚧 In Progress (Placeholder)

**Tasks:**
- [x] Create heatmaps.php
- [x] Add to sidebar navigation
- [ ] Integrate Google Maps Heatmap layer
- [ ] Fetch crime density data from database
- [ ] Display intensity-based heatmap
- [ ] Add gradient color scale
- [ ] Implement time-based heatmap animation
- [ ] Add controls (radius, opacity, dissipating)
- [ ] Filter by crime type
- [ ] Export heatmap as image

**Files:**
- `frontend/admin-page/heatmaps.php`

**Dependencies:**
- Google Maps JavaScript API
- Crime geolocation data

---

## 3. Trend Analysis

### 3.1 Time-Based Trends
**Status:** 🚧 In Progress (Placeholder)

**Tasks:**
- [x] Create timebased-trend.php
- [x] Add to sidebar navigation
- [ ] Display hourly crime distribution (line chart)
- [ ] Show daily crime trends (area chart)
- [ ] Display weekly patterns (bar chart)
- [ ] Show monthly trends (line chart)
- [ ] Implement year-over-year comparison
- [ ] Fetch time-series data from database using PHP
- [ ] Render charts using Chart.js
- [ ] Add date range selector
- [ ] Export chart data (CSV/Excel)

**Files:**
- `frontend/admin-page/timebased-trend.php`

**Dependencies:**
- Chart.js library
- Crime timestamp data in database

---

### 3.2 Location Trends
**Status:** 🚧 In Progress (Placeholder)

**Tasks:**
- [x] Create location-trend.php
- [x] Add to sidebar navigation
- [ ] Display top crime locations (bar chart)
- [ ] Show location-based distribution (pie chart)
- [ ] Display district-wise comparison (stacked bar chart)
- [ ] Show crime migration patterns
- [ ] Fetch location statistics from database
- [ ] Render charts using Chart.js
- [ ] Add location filter dropdown
- [ ] Implement drill-down by area
- [ ] Export location data

**Files:**
- `frontend/admin-page/location-trend.php`

**Dependencies:**
- Chart.js library
- Crime location/district data

---

### 3.3 Crime Type Trends
**Status:** 🚧 In Progress (Placeholder)

**Tasks:**
- [x] Create crime-type-trend.php
- [x] Add to sidebar navigation
- [ ] Display crime type distribution (pie/doughnut chart)
- [ ] Show crime type trends over time (line chart)
- [ ] Display crime severity breakdown (stacked bar chart)
- [ ] Show comparative analysis by crime type
- [ ] Fetch crime type statistics using PHP queries
- [ ] Render charts using Chart.js
- [ ] Add crime type filter
- [ ] Implement percentage calculations
- [ ] Export crime type data

**Files:**
- `frontend/admin-page/crime-type-trend.php`

**Dependencies:**
- Chart.js library
- Crime type/category data

---

## 4. Predictive Policing Tools

### 4.1 Crime Hotspots
**Status:** ⏳ Not Started

**Tasks:**
- [ ] Create hotspot-prediction.php
- [ ] Add to sidebar navigation
- [ ] Implement hotspot detection algorithm
- [ ] Display predicted hotspots on map
- [ ] Show probability scores
- [ ] Display historical vs predicted comparison
- [ ] Add time-based hotspot prediction
- [ ] Fetch historical crime data from database
- [ ] Run prediction model (Python/PHP ML library)
- [ ] Display results with confidence levels
- [ ] Export hotspot predictions

**Files:**
- `frontend/admin-page/hotspot-prediction.php`

**Dependencies:**
- Machine learning model
- Historical crime data (minimum 1 year)
- Python integration or PHP ML library

---

### 4.2 Risk Forecasting
**Status:** ⏳ Not Started

**Tasks:**
- [ ] Create risk-forecasting.php
- [ ] Add to sidebar navigation
- [ ] Implement risk scoring algorithm
- [ ] Display risk levels by area (color-coded map)
- [ ] Show risk trend over time (line chart)
- [ ] Display risk factors breakdown
- [ ] Fetch contributing factors from database
- [ ] Calculate risk scores using weighted formula
- [ ] Render risk visualization
- [ ] Add risk threshold alerts
- [ ] Export risk assessment reports

**Files:**
- `frontend/admin-page/risk-forecasting.php`

**Dependencies:**
- Risk calculation model
- Historical crime patterns
- Environmental factors data

---

### 4.3 Pattern Detection
**Status:** ⏳ Not Started

**Tasks:**
- [ ] Create pattern-detection.php
- [ ] Add to sidebar navigation
- [ ] Implement pattern matching algorithm
- [ ] Detect crime series/patterns
- [ ] Display pattern clusters on timeline
- [ ] Show pattern similarity scores
- [ ] Identify MO (Modus Operandi) patterns
- [ ] Fetch crime details from database
- [ ] Run pattern detection algorithm
- [ ] Display detected patterns with evidence
- [ ] Link related crimes
- [ ] Export pattern analysis reports

**Files:**
- `frontend/admin-page/pattern-detection.php`

**Dependencies:**
- Pattern detection algorithm
- Crime detail data (method, time, location, etc.)

---

## 5. Key Metrics Dashboard

### 5.1 Crime Rate Metrics
**Status:** ⏳ Not Started

**Tasks:**
- [ ] Create crime-rate-metrics.php
- [ ] Add to sidebar navigation
- [ ] Calculate crime rate per 1000 population
- [ ] Display crime rate trends (line chart)
- [ ] Show crime rate by district (comparison chart)
- [ ] Calculate percentage changes
- [ ] Fetch crime counts and population data
- [ ] Perform rate calculations in PHP
- [ ] Render metrics using Chart.js
- [ ] Add population data input
- [ ] Export metrics report

**Files:**
- `frontend/admin-page/crime-rate-metrics.php`

**Dependencies:**
- Population data
- Crime count data

---

### 5.2 Clearance Rates
**Status:** ⏳ Not Started

**Tasks:**
- [ ] Create clearance-metrics.php
- [ ] Add to sidebar navigation
- [ ] Calculate clearance rate (solved/total crimes)
- [ ] Display clearance trends (area chart)
- [ ] Show clearance by crime type (bar chart)
- [ ] Display average clearance time
- [ ] Fetch solved/unsolved crime data
- [ ] Calculate clearance percentages
- [ ] Render clearance metrics
- [ ] Add case status tracking
- [ ] Export clearance reports

**Files:**
- `frontend/admin-page/clearance-metrics.php`

**Dependencies:**
- Crime status data (pending, investigating, solved, closed)

---

### 5.3 Response Times
**Status:** ⏳ Not Started

**Tasks:**
- [ ] Create response-metrics.php
- [ ] Add to sidebar navigation
- [ ] Calculate average response time
- [ ] Display response time trends (line chart)
- [ ] Show response time by priority (comparison)
- [ ] Display response time by district
- [ ] Fetch incident timestamps from database
- [ ] Calculate time differences in PHP
- [ ] Render response metrics
- [ ] Add response time benchmarks
- [ ] Export response time analysis

**Files:**
- `frontend/admin-page/response-metrics.php`

**Dependencies:**
- Crime report timestamps
- Response/arrival timestamps

---

## 6. Reports & Alerts

### 6.1 Report Builder
**Status:** ⏳ Not Started

**Tasks:**
- [ ] Create report-builder.php
- [ ] Add to sidebar navigation
- [ ] Design report template builder UI
- [ ] Add drag-and-drop components
- [ ] Implement data source selection
- [ ] Add filter/query builder
- [ ] Create report layout designer
- [ ] Generate PDF reports (TCPDF/FPDF)
- [ ] Generate Excel reports (PhpSpreadsheet)
- [ ] Save custom report templates
- [ ] Schedule report generation
- [ ] Email report distribution

**Files:**
- `frontend/admin-page/report-builder.php`

**Dependencies:**
- TCPDF or FPDF library
- PhpSpreadsheet library

---

### 6.2 Report History
**Status:** ⏳ Not Started

**Tasks:**
- [ ] Create report-history.php
- [ ] Add to sidebar navigation
- [ ] Display list of generated reports
- [ ] Show report metadata (date, type, user)
- [ ] Implement search/filter functionality
- [ ] Add download report button
- [ ] View report preview
- [ ] Delete old reports
- [ ] Fetch report records from database
- [ ] Display reports in table/grid
- [ ] Add pagination
- [ ] Export report list

**Files:**
- `frontend/admin-page/report-history.php`

**Dependencies:**
- Report storage system
- Database table for report metadata

---

### 6.3 Scheduled Reports
**Status:** ⏳ Not Started

**Tasks:**
- [ ] Create scheduled-reports.php
- [ ] Add to sidebar navigation
- [ ] Design scheduling UI
- [ ] Add frequency selector (daily, weekly, monthly)
- [ ] Implement time/date picker
- [ ] Add recipient email configuration
- [ ] Save scheduled report jobs
- [ ] Create cron job handler
- [ ] Implement report generation queue
- [ ] Send email with report attachment
- [ ] Display active schedules
- [ ] Enable/disable schedules

**Files:**
- `frontend/admin-page/scheduled-reports.php`
- `api/cron/generate-scheduled-reports.php`

**Dependencies:**
- Cron job setup
- Email system (PHPMailer)

---

### 6.4 Crime Cluster Alerts
**Status:** ⏳ Not Started

**Tasks:**
- [ ] Create crime-alerts.php
- [ ] Add to sidebar navigation
- [ ] Detect crime clusters (3+ crimes in proximity)
- [ ] Display active alerts list
- [ ] Show alert details (location, crimes, time)
- [ ] Implement alert dismissal
- [ ] Add alert severity levels
- [ ] Fetch crime data from database
- [ ] Run clustering algorithm
- [ ] Send push notifications for new alerts
- [ ] Log alert history
- [ ] Export alerts report

**Files:**
- `frontend/admin-page/crime-alerts.php`

**Dependencies:**
- Clustering algorithm
- Push notification system (already implemented)

---

### 6.5 High-Risk Notifications
**Status:** ⏳ Not Started

**Tasks:**
- [ ] Create risk-notifications.php
- [ ] Add to sidebar navigation
- [ ] Define risk criteria
- [ ] Display active risk notifications
- [ ] Show risk details and recommendations
- [ ] Implement notification acknowledgment
- [ ] Add notification priority levels
- [ ] Fetch risk assessment data
- [ ] Trigger notifications on threshold breach
- [ ] Send email/push notifications
- [ ] Track notification status
- [ ] Export notification log

**Files:**
- `frontend/admin-page/risk-notifications.php`

**Dependencies:**
- Risk scoring system
- Notification system

---

### 6.6 Alert Settings
**Status:** ⏳ Not Started

**Tasks:**
- [ ] Create alert-settings.php
- [ ] Add to sidebar navigation
- [ ] Design alert configuration UI
- [ ] Add threshold settings (crime count, time window)
- [ ] Configure notification preferences (email, push)
- [ ] Set alert frequency limits
- [ ] Add recipient management
- [ ] Save settings to database
- [ ] Load user-specific settings
- [ ] Validate alert criteria
- [ ] Test alert configuration
- [ ] Export/import settings

**Files:**
- `frontend/admin-page/alert-settings.php`

**Dependencies:**
- User settings table
- Notification system

---

## 7. Account Management

### 7.1 Profile Management
**Status:** ✅ Completed

**Tasks:**
- [x] Create profile.php
- [x] Add to sidebar navigation
- [x] Display user information
- [x] Implement profile editing
- [x] Add profile picture upload
- [x] Save additional info (phone, address, department, position, bio)
- [x] Fetch user data from database using PHP
- [x] Update user information in database
- [x] Display profile completion indicator
- [x] Add validation
- [x] Show success/error messages

**Files:**
- `frontend/admin-page/profile.php`
- `api/retrieve/profile-data.php`
- `api/action/update-profile.php`

---

### 7.2 Settings
**Status:** ✅ Completed

**Tasks:**
- [x] Create settings.php
- [x] Add to sidebar navigation
- [x] Implement two-factor authentication toggle
- [x] Add dark mode toggle
- [x] Save settings to database
- [x] Fetch user settings using PHP
- [x] Update settings in real-time
- [x] Display success notifications
- [x] Add settings validation

**Files:**
- `frontend/admin-page/settings.php`
- `api/retrieve/settings-data.php`
- `api/action/update-settings.php`

---

## 8. Authentication & Security

### 8.1 Login System
**Status:** ✅ Completed

**Tasks:**
- [x] Create login page (index.php)
- [x] Implement email/password authentication
- [x] Add Google OAuth integration
- [x] Validate credentials using PHP
- [x] Check account status (verified, locked)
- [x] Track failed login attempts (max 3)
- [x] Lock account after 3 failed attempts
- [x] Send unlock email with token
- [x] Create session on successful login
- [x] Redirect to dashboard
- [x] Display flash messages (error/success)

**Files:**
- `index.php`
- `api/action/login/login_process.php`
- `api/action/login/google-callback.php`

---

### 8.2 Two-Factor Authentication
**Status:** ✅ Completed

**Tasks:**
- [x] Create OTP verification page
- [x] Generate 6-digit OTP code
- [x] Send OTP via email using PHPMailer
- [x] Store OTP in database with expiry (2 minutes)
- [x] Implement OTP countdown timer
- [x] Validate OTP on submission
- [x] Mark OTP as used after verification
- [x] Create session after successful OTP
- [x] Implement IP-based 2FA trigger
- [x] Log 2FA activity

**Files:**
- `api/action/login/verify-otp.php`
- `api/action/login/verify-otp-controller.php`
- `api/helpers/otp-view-helper.php`

---

### 8.3 Password Reset
**Status:** ✅ Completed

**Tasks:**
- [x] Create forgot password page
- [x] Implement email input form
- [x] Generate password reset token
- [x] Send reset link via email
- [x] Create reset password page
- [x] Validate reset token
- [x] Check token expiry (1 hour)
- [x] Implement new password form
- [x] Hash password using password_hash()
- [x] Update password in database
- [x] Invalidate token after use
- [x] Display success message

**Files:**
- `frontend/user-page/forgot-password.php`
- `frontend/user-page/reset-password.php`
- `api/action/forgot-password.php`
- `api/action/reset-password.php`

---

### 8.4 Session Management
**Status:** ✅ Completed

**Tasks:**
- [x] Implement session timeout (1 hour)
- [x] Create authentication middleware
- [x] Check session validity on each page
- [x] Regenerate session ID on login
- [x] Store user data in session
- [x] Track last activity time
- [x] Implement logout functionality
- [x] Clear session on logout
- [x] Redirect to login on session expiry

**Files:**
- `api/middleware/auth.php`
- `api/action/logout.php`

---

## 9. Notification System

### 9.1 Push Notifications
**Status:** ✅ Completed (with limitations)

**Tasks:**
- [x] Install Minishlink Web Push library v9.0
- [x] Create push notification demo page
- [x] Generate VAPID keys
- [x] Configure VAPID in .env file
- [x] Register service worker
- [x] Request notification permission
- [x] Create push subscription
- [x] Save subscription to database
- [x] Send push notifications using PHP
- [x] Handle notification clicks
- [x] Display notification UI
- [ ] Fix OpenSSL configuration issue (production)

**Files:**
- `frontend/admin-page/notification-demo.php`
- `frontend/js/push-notification.js`
- `service-worker.js`
- `api/action/notifications/subscribe.php`
- `api/action/notifications/send-notification.php`
- `generate-vapid-keys.php`

**Known Issues:**
- OpenSSL configuration issue on XAMPP (using test VAPID keys)
- Browser must be open to receive notifications (free tier limitation)

---

## 10. Database & Backend

### 10.1 Database Tables
**Status:** ✅ Completed (Auth & User)

**Completed Tables:**
- [x] `crime_department_admin_users` - User accounts
- [x] `crime_department_admin_information` - Additional user info
- [x] `crime_department_user_settings` - User preferences
- [x] `crime_department_otp_verification` - OTP codes
- [x] `crime_department_activity_logs` - User activity
- [x] `crime_department_push_subscriptions` - Push subscriptions
- [x] `crime_department_notifications` - In-app notifications

**Pending Tables:**
- [ ] `crime_department_incidents` - Crime records
- [ ] `crime_department_crime_types` - Crime categories
- [ ] `crime_department_locations` - Location data
- [ ] `crime_department_reports` - Generated reports
- [ ] `crime_department_alerts` - Alert configurations
- [ ] `crime_department_predictions` - ML predictions

**Files:**
- `db.sql`

---

### 10.2 API Structure
**Status:** ✅ Completed (Architecture)

**Tasks:**
- [x] Create api/action folder (POST operations)
- [x] Create api/retrieve folder (GET operations)
- [x] Create api/helpers folder (Utility functions)
- [x] Implement URL helper functions
- [x] Create VAPID config helper
- [x] Create OTP view helper
- [x] Separate business logic from views
- [x] Implement MVC-like structure

**Files:**
- `api/action/` - All action handlers
- `api/retrieve/` - All data retrieval
- `api/helpers/` - Helper functions
- `api/middleware/` - Authentication guards
- `api/config.php` - Database configuration

---

## 11. UI/UX Components

### 11.1 Global Styles
**Status:** ✅ Completed

**Tasks:**
- [x] Create global.css with CSS variables
- [x] Define color scheme (light/dark mode)
- [x] Implement typography system
- [x] Create reusable button styles
- [x] Add form input styles
- [x] Create card components
- [x] Add scrollbar styling
- [x] Implement responsive breakpoints
- [x] Add transition/animation utilities

**Files:**
- `frontend/css/global.css`

---

### 11.2 Navigation Components
**Status:** ✅ Completed

**Tasks:**
- [x] Create sidebar component
- [x] Add collapsible submenus
- [x] Implement active state management
- [x] Add mobile responsive sidebar
- [x] Create sidebar overlay
- [x] Add admin header component
- [x] Implement breadcrumb navigation
- [x] Add user menu dropdown
- [x] Create logout button

**Files:**
- `frontend/includes/sidebar.php`
- `frontend/includes/admin-header.php`
- `frontend/css/sidebar.css`
- `frontend/css/admin-header.css`

---

### 11.3 Error Pages
**Status:** ✅ Completed

**Tasks:**
- [x] Create custom 404 page
- [x] Design error page layout
- [x] Add navigation links
- [x] Implement .htaccess configuration
- [x] Style with theme colors

**Files:**
- `404.php`
- `frontend/css/404.css`
- `.htaccess`

---

## 12. Future Enhancements

### 12.1 User Management (Admin)
**Status:** ⏳ Not Started

**Tasks:**
- [ ] Create user management page
- [ ] Display all users in table
- [ ] Add user CRUD operations
- [ ] Implement role management (Admin, Super Admin, Viewer)
- [ ] Add permission system
- [ ] User search and filtering
- [ ] Bulk user actions
- [ ] Export user list

---

### 12.2 Data Import/Export
**Status:** ⏳ Not Started

**Tasks:**
- [ ] Create CSV import functionality
- [ ] Add Excel import support
- [ ] Implement data validation on import
- [ ] Create export functionality (CSV, Excel, PDF)
- [ ] Add bulk data operations
- [ ] Schedule automated exports

---

### 12.3 Performance Optimization
**Status:** ⏳ Not Started

**Tasks:**
- [ ] Implement database query caching
- [ ] Add Redis/Memcached integration
- [ ] Optimize large dataset queries
- [ ] Implement lazy loading
- [ ] Add CDN for static assets
- [ ] Database indexing optimization

---

## Legend

- ✅ **Completed** - Feature fully implemented and tested
- 🚧 **In Progress** - Feature partially completed
- ⏳ **Not Started** - Feature planned but not started
- 🐛 **Bug** - Known issue to be fixed
- 📝 **Documentation Needed** - Requires documentation

---

## Dependencies Summary

### PHP Libraries (via Composer)
- ✅ google/apiclient: 2.15.0
- ✅ phpmailer/phpmailer: ^7.0
- ✅ minishlink/web-push: ^9.0

### JavaScript Libraries (Needed)
- ⏳ Chart.js - Data visualization
- ⏳ Google Maps JavaScript API - Mapping
- ⏳ DataTables - Advanced table features

### Server Requirements
- ✅ PHP 8.1+
- ✅ MySQL 5.7+
- ✅ Apache/Nginx
- ✅ Composer
- ✅ OpenSSL extension

---

**Total Features:** 80+
**Completed:** 47 (58%)
**In Progress:** 6 (8%)
**Not Started:** 27 (34%)
