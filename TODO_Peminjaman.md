# TODO - Kelola Peminjaman Unification

## Task: Simplifikasi Menu Admin - Gabungkan "Approve Peminjaman" dan "Pengembalian"

## Files to Update:

### 1. AdminController.php

-   [ ] Refactor `peminjaman()` method with unified filtering
-   [ ] Keep action methods (approve, reject, return, calculateDenda, confirmDendaPaid)

### 2. index.blade.php (admin/peminjaman)

-   [ ] Create unified table with all columns (User, Barang, Tgl Pinjam, Tgl Kembali, Status, Aksi)
-   [ ] Implement dynamic action buttons based on status
-   [ ] Add status filter dropdown
-   [ ] Add return modal with denda calculation
-   [ ] Add reject modal with reason input

### 3. web.php (routes)

-   [ ] Clean up routes for peminjaman management
-   [ ] Use route resource or single GET route for admin/peminjamans

### 4. navigation.blade.php

-   [ ] Replace "Approve Peminjaman" and "Pengembalian" with single "Kelola Peminjaman" menu
-   [ ] Update both desktop and mobile menus

## Status Logic:

-   `diajukan` (pending): Show "Setujui" (✓) and "Tolak" (✗) buttons
-   `disetujui` (borrowed): Show "Kembalikan" button (blue)
-   `dikembalikan`/`ditolak` (completed): Show "Selesai" badge (no buttons)

## Progress:

-   [x] Plan created and confirmed
-   [x] AdminController.php updated
-   [x] index.blade.php updated
-   [x] web.php updated
-   [x] navigation.blade.php updated
-   [x] Completed ✅
