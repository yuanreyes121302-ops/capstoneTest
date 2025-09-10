# UI/UX Fixes for Chat Interface and User Profiles

## 1. Message Inbox Layout Issues
- [ ] Fix avatar and profile name alignment in inbox sidebar
- [ ] Add online/offline status display near avatar
- [ ] Make message input field full width and fix text area visibility
- [ ] Ensure avatar, name, and status are visible in conversation header

## 2. Tenant Profile Layout
- [x] Move profile image and "choose file" button to side of text fields
- [x] Ensure clean display with avatar, name, gender, location
- [x] Remove unnecessary scrolling

## 3. Landlord Profile Layout
- [x] Remove DOB field from profile
- [x] Add contact number field and display next to avatar
- [x] Update profile display order: name, contact number, gender, email

## 4. Landlord Registration
- [x] Add contact number field as required for landlords
- [x] Update validation and creation logic

## 5. Backend Adjustments
- [x] Add contact_number column to users table
- [x] Update User model fillable array
- [x] Update controllers for contact number handling
- [ ] Implement online/offline status tracking system

## 6. CSS Updates
- [x] Update messages.css for better layout
- [x] Update tenant.css for side layout
- [x] Update landlord.css for new fields

## 7. Testing
- [ ] Test responsiveness on mobile and desktop
- [ ] Verify consistency across views
- [ ] Test online/offline status functionality
