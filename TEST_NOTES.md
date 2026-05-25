# Test Contact Number Validation & Form Flows

## Request Barangay Admin (/request/barangay-admin)

1. Load page
2. Try type letters → blocked
3. Special chars → blocked
4. Type 12 digits → stops at 11
5. Paste 'abc091234567891' → becomes '09123456789'
6. <11 digits → red, submit disabled
7. Exactly 11 → green, enabled
8. Submit valid → success msg on login

## Register (/register)

1. Fill form, contact validation works (strips non-digits)
2. Submit → 'Registered successfully! Please login...' → login page
