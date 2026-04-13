# Galaxi Migration Plan

## Goal
Rebuild `galaxiOld` in `galaxiNew` on Laravel with modern but simple implementation, while preserving critical functionality and UX parity.

## Core principles
- parity first, improvements later
- simple Laravel monolith
- avoid unnecessary complexity
- preserve operational UX density
- document decisions and progress in Git

## Product understanding
`galaxiOld` is a loyalty/CRM back-office system with operational admin workflows around cardholders, cards, shops, checks, points, gifts, services, and reports.

`galaxiNew` is currently a Laravel foundation and should become the clean successor implementation.

## Target architecture
- Laravel monolith
- Blade as the default UI layer
- Alpine.js only for light interactivity
- Livewire only where server-rendered CRUD becomes too clumsy
- no SPA by default
- no microservices
- no premature overengineering

## High-level phases
1. Foundation
   - auth
   - roles/permissions
   - admin shell
   - navigation
   - core entities baseline

2. Core operations
   - cardholders
   - cards
   - card types
   - shops
   - histories and status operations

3. Transactions
   - checks/purchases
   - points accrual
   - search by fiscal number

4. Rewards and services
   - gifts
   - services
   - service groups/rules

5. Reporting
   - operational reports
   - dashboards
   - inactive cards
   - work history

6. Secondary and polish
   - notifications
   - secondary reports
   - settings UI
   - media polishing

## Parity-critical areas
- auth and access model
- shop-scoped permissions
- sidebar/header/navigation map
- dense admin tables and filters
- cardholder/card workflows
- checks and points logic
- gifts issuance/removal
- blocking rules
- core reports
- historical data integrity

## Delivery model
- analysis and decisions are documented in docs
- completed work is committed in coherent increments
- progress is logged continuously
- 30-minute status reporting remains active
