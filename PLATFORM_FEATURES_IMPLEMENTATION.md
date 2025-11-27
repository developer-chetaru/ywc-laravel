# YachtCrew Platform - Complete Feature Implementation Guide

## 📋 Table of Contents
1. [Landing Page & Waitlist](#landing-page--waitlist)
2. [Forum Main Thread Subscription](#forum-main-thread-subscription)
3. [Industry Review System](#industry-review-system)
4. [Captain Dashboard](#captain-dashboard)
5. [Work Log Tracking](#work-log-tracking)
6. [Itinerary Planner](#itinerary-planner)
7. [Crew Discovery](#crew-discovery)
8. [API Endpoints](#api-endpoints)

---

## 🎯 Landing Page & Waitlist

### Status: ✅ COMPLETED

### Files Created:
- `app/Models/Waitlist.php` - Waitlist model
- `app/Http/Controllers/LandingPageController.php` - Landing page controller
- `resources/views/landing.blade.php` - Landing page view
- `database/migrations/2025_11_27_073157_create_waitlists_table.php` - Waitlist migration

### Routes:
- `GET /` - Landing page (replaces login redirect)
- `POST /waitlist/join` - Web form submission
- `POST /api/waitlist/join` - API endpoint for mobile apps

### Features:
- ✅ Beautiful landing page with hero section
- ✅ Video placeholder for demo video
- ✅ Waitlist signup form with validation
- ✅ Google Analytics integration
- ✅ Features showcase section
- ✅ Responsive design

### Usage:
1. Visit `/` to see the landing page
2. Users can sign up with email, name, and role
3. All signups stored in `waitlists` table with status 'pending'
4. Admin can approve/invite users from waitlist

### Configuration:
Add to `.env`:
```
GOOGLE_ANALYTICS_ID=your-google-analytics-id
```

---

## 💬 Forum Main Thread Subscription

### Status: ⚠️ NEEDS IMPLEMENTATION

### Requirements:
- Create a main community announcement thread
- Auto-subscribe all existing users
- Auto-subscribe new users on registration
- Ensure notifications are sent for main thread posts

### Implementation Steps:

#### 1. Create Main Thread Seeder
```bash
php artisan make:seeder MainCommunityThreadSeeder
```

#### 2. Create Service for Auto-Subscription
```bash
php artisan make:service ForumSubscriptionService
```

#### 3. Hook into User Registration
Update `app/Actions/Fortify/CreateNewUser.php` to auto-subscribe new users.

#### 4. Create Command to Subscribe Existing Users
```bash
php artisan make:command SubscribeUsersToMainThread
```

### Database:
- Uses existing `forum_threads_read` table
- Main thread ID stored in config: `forum.main_thread_id`

---

## ⭐ Industry Review System

### Status: ⚠️ PARTIALLY IMPLEMENTED

### Current Implementation:
- ✅ Yachts reviews
- ✅ Marinas reviews
- ❌ Contractors reviews (NOT IMPLEMENTED)
- ❌ Brokers reviews (NOT IMPLEMENTED)
- ❌ Restaurants reviews (NOT IMPLEMENTED)

### Files to Create:

#### 1. Models:
- `app/Models/Contractor.php`
- `app/Models/ContractorReview.php`
- `app/Models/Broker.php`
- `app/Models/BrokerReview.php`
- `app/Models/Restaurant.php`
- `app/Models/RestaurantReview.php`

#### 2. Migrations:
- `create_contractors_table.php`
- `create_contractor_reviews_table.php`
- `create_brokers_table.php`
- `create_broker_reviews_table.php`
- `create_restaurants_table.php`
- `create_restaurant_reviews_table.php`

#### 3. Livewire Components:
- `app/Livewire/IndustryReview/ContractorReviewIndex.php`
- `app/Livewire/IndustryReview/BrokerReviewIndex.php`
- `app/Livewire/IndustryReview/RestaurantReviewIndex.php`

#### 4. Views:
- Update `resources/views/livewire/industry-review/industry-review-index.blade.php` to include new tabs

### Review Fields by Type:

#### Contractors:
- Overall rating
- Quality of work
- Timeliness
- Communication
- Pricing
- Professionalism
- Would recommend

#### Brokers:
- Overall rating
- Response time
- Transparency
- Deal negotiation
- Communication
- Professionalism
- Would recommend

#### Restaurants:
- Overall rating
- Food quality
- Service
- Ambiance
- Value for money
- Location
- Would recommend

---

## 👨‍✈️ Captain Dashboard

### Status: ⚠️ NEEDS IMPLEMENTATION

### Requirements:
- View all crew members on their yacht
- View crew certificates and expiry dates
- View crew work hours and compliance status
- Export reports for compliance

### Files to Create:

#### 1. Livewire Component:
- `app/Livewire/CaptainDashboard.php`

#### 2. View:
- `resources/views/livewire/captain-dashboard.blade.php`

#### 3. Route:
Add to `routes/web.php`:
```php
Route::get('/captain/dashboard', CaptainDashboard::class)
    ->name('captain.dashboard')
    ->middleware(['auth', 'role:captain']);
```

### Features:
1. **Crew List**
   - Filter by department (deck, interior, engineering, etc.)
   - Search by name
   - View profile photos

2. **Certificates Overview**
   - List all crew certificates
   - Highlight expiring certificates (30/60/90 days)
   - Certificate expiry calendar
   - Export certificate report

3. **Work Hours Dashboard**
   - Weekly hours summary per crew member
   - Compliance status (MLC 2006)
   - Overtime tracking
   - Rest period compliance
   - Export work hours report

4. **Quick Actions**
   - Send reminder for expiring certificates
   - View individual crew profile
   - Export compliance report

### Data Sources:
- `users` table (filtered by `current_yacht`)
- `documents` table (certificates)
- `work_logs` table (hours tracking)
- `certificates` table (certificate details)

---

## ⏰ Work Log Tracking

### Status: ✅ IMPLEMENTED

### Current Features:
- ✅ Daily work log entry
- ✅ Automatic hour calculations
- ✅ Rest periods tracking
- ✅ Compliance checking (daily & weekly limits)
- ✅ Location status (at sea, in port, shipyard)
- ✅ History view with date filtering
- ✅ Statistics & Reports

### Files:
- `app/Livewire/WorkLog/WorkLogIndex.php`
- `app/Models/WorkLog.php`
- `app/Models/WorkLogRestPeriod.php`
- `app/Services/WorkLog/ComplianceService.php`

### Usage:
1. Navigate to Work Log module
2. Select date and yacht
3. Enter work hours and rest periods
4. System automatically calculates compliance
5. View history and statistics

### API:
- `GET /api/work-logs` - List work logs
- `POST /api/work-logs` - Create work log
- `GET /api/work-logs/statistics` - Get statistics

---

## 🗺️ Itinerary Planner

### Status: ✅ IMPLEMENTED

### Current Features:
- ✅ Create and customize routes
- ✅ Real-time weather integration
- ✅ Export to PDF, CSV, GPX
- ✅ Route library with search
- ✅ Crew collaboration
- ✅ Reviews and ratings

### Files:
- `app/Livewire/Itinerary/RoutePlanner.php`
- `app/Livewire/Itinerary/RouteLibrary.php`
- `app/Models/ItineraryRoute.php`

### Usage:
1. Navigate to Itinerary Planner
2. Create new route or browse library
3. Add stops with coordinates
4. View weather forecast
5. Share with crew
6. Export for offline use

### API:
- `GET /api/itinerary/routes` - List routes
- `POST /api/itinerary/routes` - Create route
- `GET /api/itinerary/routes/{id}` - Get route details

---

## 👥 Crew Discovery

### Status: ✅ IMPLEMENTED

### Current Features:
- ✅ Location-based discovery
- ✅ Online crew members
- ✅ Connection requests
- ✅ Real-time messaging
- ✅ Privacy controls

### Files:
- `app/Livewire/CrewDiscovery.php`
- `app/Livewire/UserConnections.php`

### Usage:
1. Enable location sharing (opt-in)
2. Discover nearby crew members
3. Send connection requests
4. Chat with connected crew

---

## 📡 API Endpoints

### Profile API (Updated)
- `GET /api/profile` - Get user profile (includes all crew profile fields)
- `POST /api/profile` - Update profile (supports all crew profile fields)

### Waitlist API
- `POST /api/waitlist/join` - Join waitlist

### Work Log API
- `GET /api/work-logs` - List work logs
- `POST /api/work-logs` - Create work log
- `GET /api/work-logs/statistics` - Get statistics

### Itinerary API
- `GET /api/itinerary/routes` - List routes
- `POST /api/itinerary/routes` - Create route

---

## 🚀 Quick Start Guide

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Main Community Thread (when implemented)
```bash
php artisan db:seed --class=MainCommunityThreadSeeder
```

### 3. Subscribe Existing Users (when implemented)
```bash
php artisan forum:subscribe-main-thread
```

### 4. Access Features
- Landing Page: `/`
- Waitlist Admin: Create admin interface (TODO)
- Captain Dashboard: `/captain/dashboard` (when implemented)
- Industry Reviews: `/industry-review`
- Work Logs: `/work-logs`
- Itinerary: `/itinerary`

---

## 📝 Next Steps

### Priority 1 (High):
1. ✅ Landing page with waitlist - DONE
2. ⚠️ Forum main thread subscription - TODO
3. ⚠️ Industry review sections (contractors, brokers, restaurants) - TODO
4. ⚠️ Captain dashboard - TODO

### Priority 2 (Medium):
1. Waitlist admin management interface
2. Email notifications for main thread
3. Mobile app API enhancements

### Priority 3 (Low):
1. Signal K integration for itinerary
2. Advanced analytics
3. Export enhancements

---

## 🔧 Configuration

### Environment Variables:
```env
GOOGLE_ANALYTICS_ID=your-id-here
OPENWEATHER_API_KEY=your-key-here
```

### Forum Configuration:
Edit `config/forum/general.php` to set main thread ID after creation.

---

## 📞 Support

For questions or issues, refer to:
- API Documentation: `/api/documentation` (Swagger UI)
- Code comments in respective files
- This documentation file

---

**Last Updated:** November 27, 2025
**Version:** 1.0.0

