# AltayarVIP - Project Details

## 1) Project Identity
- **Project Name:** AltayarVIP
- **Project Type:** Laravel web application for tour packages and booking management
- **Primary Purpose:** Display travel/tour content, manage bookings, support multiple user roles, and provide an admin dashboard for operations
- **Current Backend App Name:** `AltayarVIP`
- **Current Composer Package Name:** `altayarvip/altayervip`
- **Frontend Build Tool:** Vite

## 2) Technology Stack
- **Language:** PHP 8.2
- **Framework:** Laravel 10.x
- **Frontend Build:** Vite 4
- **Frontend Helpers:** Laravel Blade templates, Bootstrap-based UI, Font Awesome icons, responsive components
- **Common Backend Packages:**
  - `laravel/sanctum`
  - `laravel/socialite`
  - `laravel/ui`
  - `intervention/image`
  - `guzzlehttp/guzzle`
  - `stripe/stripe-php`
  - `razorpay/razorpay`
  - `twilio/sdk`
  - `vonage/client`
  - `coingate/coingate-php`
- **Developer Tools:** Laravel Pint, PHPUnit, Debugbar, Query Detector, Ignition

## 3) High-Level Architecture
The application is split into several main areas:

### Backend / Admin Side
Responsible for managing the whole platform, including:
- dashboard and profile management
- users and agents management
- tour packages and categories
- locations
- bookings
- deposits and payment gateways
- subscriber management
- notifications
- KYC settings
- content pages and CMS data
- support tickets
- reports

### Frontend / Public Site
Responsible for the public-facing website, including:
- home page
- pages and dynamic CMS pages
- tour package listing and details
- blog pages
- contact page
- subscription form
- cookie policy and policy pages
- image upload and placeholder assets
- language switching

### User Portal
A dedicated portal for end users with:
- registration and login
- profile management
- 2FA and verification flows
- booking history
- wishlists
- deposits and payment flows
- support tickets
- transaction history
- reviews and coupons

### Agent Portal
A dedicated portal for agents with:
- registration and login
- agent verification and KYC
- profile management
- tour package CRUD
- booking management
- withdrawals
- support tickets
- transactions and deposits

## 4) Main Functional Modules
### Tour Package System
- Create, edit, publish, search, and manage tour packages
- Track package status such as active, pending, expired, running, and canceled
- Attach images to packages
- Support agent-owned and admin-managed package flows

### Booking System
- Users can book tour packages
- Booking lifecycle includes pending, approved, and canceled states
- Admin can review booking lists and booking details
- Agents can track bookings related to their packages

### Payments and Deposits
- Deposit system available for users
- Payment gateway structure for automatic and manual methods
- Payment confirmation and deposit confirmation routes
- Gateway-related controllers live under the payment system

### Authentication and Security
- Separate authentication flows for admin, user, and agent roles
- Password reset flows
- Email and mobile verification
- Google-style 2FA flows
- Registration completion and status checks

### Support and Communication
- Support ticket system for users and agents
- Ticket creation, reply, close, and file download flows
- Subscriber email management
- Notification system for admin events

### CMS / Content Management
- Pages and policy pages
- Blog module
- Contact page and form submissions
- Footer content, company links, and important links
- Cookie policy content

### KYC and Compliance
- Admin KYC settings
- Agent KYC workflow
- KYC data, approval, and rejection flows

### User Engagement
- Reviews
- Wishlists
- Coupon application
- Social login support
- Language switching

## 5) Important Routes
### Public Routes
Defined mainly in [routes/web.php](routes/web.php):
- `/` home page
- `/browse` tour package list
- `/tour-package/{slug}/{id}` tour package details
- `/blog` blog list
- `/blog/{slug}/{id}` blog details
- `/contact` contact page
- `/policy/{slug}/{id}` policy pages
- `/cookie-policy` cookie policy
- `/subscribe` newsletter subscription
- `/tour-side-filter` package filtering
- dynamic pages through `/{slug}`

### User Routes
Defined mainly in [routes/user.php](routes/user.php):
- login, register, logout
- password reset and verification
- dashboard
- profile settings
- booking history and booking details
- deposits and payment actions
- wishlists and transactions
- support tickets and file download

### Agent Routes
Defined mainly in [routes/agent.php](routes/agent.php):
- login, register, logout
- password reset and verification
- dashboard
- profile settings
- KYC pages
- tour package management
- booking management
- support tickets
- withdrawals
- transactions and deposits

### Admin Routes
Defined mainly in [routes/admin.php](routes/admin.php):
- admin login and password reset
- dashboard
- profile and password management
- users management
- agents management
- categories and locations
- subscribers
- tour package management
- booking management
- payment gateway management
- deposits
- notifications
- reports
- support-related tools
- KYC settings

## 6) Frontend Structure
The frontend Blade views are mainly under [resources/views/presets/default](resources/views/presets/default).

### Main Frontend Areas
- layouts
- home sections
- header and footer components
- blog components
- banner, FAQ, testimonials, offers, destinations, and other landing-page sections
- tour package listing and detail components
- cookie banner and notification components
- sidebar and mobile navigation

### Key Frontend Components
- [resources/views/presets/default/components/header.blade.php](resources/views/presets/default/components/header.blade.php)
- [resources/views/presets/default/components/footer.blade.php](resources/views/presets/default/components/footer.blade.php)
- [resources/views/presets/default/components/breadcrumb.blade.php](resources/views/presets/default/components/breadcrumb.blade.php)
- [resources/views/presets/default/components/loader.blade.php](resources/views/presets/default/components/loader.blade.php)
- [resources/views/presets/default/components/cookie.blade.php](resources/views/presets/default/components/cookie.blade.php)
- [resources/views/presets/default/components/blog.blade.php](resources/views/presets/default/components/blog.blade.php)

### Frontend Behavior Notes
- The site logo alt text uses `config('app.name')`
- The current active language is read from session state
- Public navigation includes pages, blog, contact, browse, and authentication links
- Footer content is driven by CMS-style content records

## 7) Backend Structure
### Controllers
The main controller folders are:
- [app/Http/Controllers/Admin](app/Http/Controllers/Admin)
- [app/Http/Controllers/Agent](app/Http/Controllers/Agent)
- [app/Http/Controllers/User](app/Http/Controllers/User)
- [app/Http/Controllers/Gateway](app/Http/Controllers/Gateway)
- [app/Http/Controllers/Api](app/Http/Controllers/Api)
- [app/Http/Controllers](app/Http/Controllers)

### Custom Libraries
The project includes custom helper/service classes under [app/Lib](app/Lib), such as:
- Captcha handling
- client info detection
- curl requests
- file management
- form processing
- Google Authenticator helpers
- intended route tracking
- social login helpers
- searchable helpers

### Models
The domain models live under [app/Models](app/Models) and cover:
- admin and agent accounts
- users and login history
- tour packages and images
- bookings and reviews
- deposits and withdrawals
- gateways and gateway currencies
- pages, frontends, languages, subscribers
- support tickets and messages
- notifications and templates
- locations and categories

## 8) Configuration Notes
### App Name
- The application name is configured in [config/app.php](config/app.php)
- `APP_NAME` should be set to `AltayarVIP`

### Session and Cache Naming
- Cache prefix uses `APP_NAME` in [config/cache.php](config/cache.php)
- Session cookie uses `APP_NAME` in [config/session.php](config/session.php)

### Services
- Third-party service credentials are prepared in [config/services.php](config/services.php)
- This includes common integrations like Mailgun, Postmark, and AWS

### Environment
Current workspace environment values observed in [.env](.env):
- `APP_NAME=AltayarVIP`
- `APP_URL=http://localhost/altayarbookingvp`
- `DB_DATABASE=u609073446_travela`

## 9) Project Metadata
### Composer
The Composer package metadata declares:
- package name: `altayarvip/altayervip`
- description: `AltayarVIP application.`

### NPM
The frontend package metadata declares:
- package name: `altayervip`
- build scripts: `dev` and `build`

## 10) Notes for Developers
- This repository is already a Laravel application, not a blank starter project.
- The public UI is Blade-based and organized by preset templates.
- Several admin and portal flows are duplicated by role and should be updated consistently when changing booking or account logic.
- Many text/content blocks are CMS-driven from database records rather than hardcoded in views.
- Some existing database export content still contains old branding strings in the SQL dump.

## 11) Remaining Old Branding That May Still Exist
If you want the project identity to be fully cleaned, check these sources too:
- [.env](.env) for `APP_URL` and database naming
- [Database/u609073446_travela.sql](../Database/u609073446_travela.sql) for old content strings inside seeded data
- any cached views or generated artifacts under `storage/framework/views`

## 12) Quick Start Summary
1. Install dependencies with Composer and NPM if needed.
2. Ensure `.env` has the correct `APP_NAME`, DB settings, and URL.
3. Run migrations or import the database dump if this project depends on seeded CMS content.
4. Build frontend assets with Vite.
5. Clear caches when branding or configuration changes.

---

This document is a workspace snapshot of the current AltayarVIP project structure and major behaviors.
