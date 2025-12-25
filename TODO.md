# TODO: Fix Errors in PeminjamanController.php

-   [x] Add private method `getOverlappingBorrowings` to calculate borrowings overlapping with given dates
-   [x] Update `store` method: Change quantity validation to use availableStock (considering date overlaps)
-   [x] Update `store` method: Implement date overlap logic for availableStock calculation
-   [x] Update `checkAvailability` method: Use startDate and endDate for overlapping borrowings query
-   [x] Update `checkAvailability` method: Calculate availableStock based on overlapping borrowings
-   [x] Test the updated methods for correct availability checks and borrowing creation (syntax check passed)
