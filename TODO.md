# TODO: Implement Admin Approval/Rejection Functionality

-   [x] Add admin routes group with AdminMiddleware for peminjaman management (index, approve, reject)
-   [x] Add adminIndex(), approve(), and reject() methods to PeminjamanController with proper authorization
-   [x] Create admin view for listing pending peminjaman requests (resources/views/admin/peminjamans/index.blade.php)
-   [x] Add admin action buttons (approve/reject) to peminjaman show view for 'diajukan' requests
-   [x] Update admin menu links in navigation to point to actual admin routes
-   [x] Test admin approval/rejection workflow and authorization checks

## Implementation Steps

-   [x] Update navigation.blade.php: Change "Approve Peminjaman" link to route('admin.peminjamans.index')
-   [x] Update peminjamans/show.blade.php: Add approve/reject buttons for admins on 'diajukan' requests
-   [x] Add JavaScript functions for approve/reject in show view
-   [x] Test the admin workflow

## Testing Results ✅

-   Backend functionality verified: Admin can approve/reject peminjaman requests
-   Authorization checks working: Non-admin users blocked from admin routes
-   Status transitions correct: 'diajukan' → 'disetujui'/'ditolak'
-   Stock validation working: Insufficient stock prevents approval
