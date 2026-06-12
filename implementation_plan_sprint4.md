# Future Roadmap - Sprint 4 Integration Architecture

This document outlines the planned architectural roadmap for Sprint 4. These integrations build on the structured dynamic content base implemented in Sprints 2B and 3.

---

## 1. CRM & Lead Management System
- **Objective**: Manage inbound inquiries from booking forms directly inside WordPress Admin.
- **Workflow**:
  - All inquiries from homepage/contact page forms are saved to the custom `$wpdb` table `wp_jjwz_leads`.
  - Introduce lead status workflows: `New`, `Contacted`, `Quote Sent`, `Booked`, `Archived`.
  - Add admin interface for viewing, editing, and searching leads.
  - CSV export capability for custom marketing datasets.

---

## 2. Client Portal & Gallery Delivery
- **Objective**: Deliver high-resolution digital media packages to clients with strict privacy controls.
- **Workflow**:
  - Secure login access for couples via unique `Gallery Access Key`.
  - Front-end client dashboard showing active milestones (e.g. Wedding, Pre-Wedding shoots).
  - High-performance, lazy-loaded photo grid supporting full-screen lightboxes.
  - Granular download toggles: enable or disable high-resolution media downloading per client.
  - Watermark bypass: clients with valid login view watermarked previews but can download pristine images once marked paid.

---

## 3. Bookings, Invoices & Payments
- **Objective**: Handle contracts, scheduling, and billing payments.
- **Workflow**:
  - **Bookings Calendar**: Integration of client booking schedule calendar (showing booked dates, crew allocations).
  - **Invoices**: Create custom post type `jjwz_invoice` containing line items, tax details, discount structures, and status (`Pending`, `Paid`, `Overdue`).
  - **Payment Gateway**: Integration of Razorpay API for direct online payments. Auto-updates invoice status via webhook alerts.

---

## 4. WhatsApp Automation & Notifications
- **Objective**: Automate follow-ups and notifications for improved booking conversion.
- **Workflow**:
  - Integrate WhatsApp Business API to route triggers.
  - **Triggers**:
    - Lead Received: Auto-send thank you message with links to relevant location/service portfolio.
    - Milestone Scheduled: Send appointment reminders to couples.
    - Invoice Generated/Overdue: Auto-send payment links.
    - Gallery Ready: Send notifications with direct access keys.
