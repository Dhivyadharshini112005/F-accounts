USE `f_taxi_accounts`;

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE incomes;
TRUNCATE TABLE expenses;
TRUNCATE TABLE advances;
TRUNCATE TABLE daily_accounts;
TRUNCATE TABLE daily_closings;
TRUNCATE TABLE ftaxi_monthly_accounts;

UPDATE drivers SET pending_amount = 0;

SET FOREIGN_KEY_CHECKS = 1;

-- Owner and Manager users are intentionally NOT changed.
-- Driver records are intentionally NOT deleted.
