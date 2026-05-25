# Remove Barangay Admin Dashboard

## Steps to Complete:

### 1. [ ] Delete Files (6 files)

- [ ] `resources/views/dashboard/barangay.blade.php`
- [ ] `app/Http/Controllers/BarangayAdminRequestController.php`
- [ ] `resources/views/auth/request-barangay-admin.blade.php`
- [ ] `resources/views/admin/barangay-requests.blade.php`
- [ ] `app/Models/BarangayAdminRequest.php`
- [ ] `resources/views/admin/users/create.blade.php`

### 2. [ ] Modify `routes/web.php`

- Remove `BarangayAdminRequestController` import
- Remove `/request/barangay-admin` GET & POST routes
- Remove `/barangay/dashboard` route

- Load form: http://localhost/request-barangay-admin (or relevant route)
- Test invalid inputs: short, long, letters, specials → red/error
- Test valid 11 digits → no red
- Submit valid → success message
- Submit invalid → server validation error

### 4. [x] Mark Complete

- Update this TODO.md with [x] checkboxes
- Use attempt_completion
