-- Run this once on an existing Core PHP F-Taxi Accounts database.
ALTER TABLE incomes
  ADD COLUMN attachment_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER fees_amount,
  ADD COLUMN attachment_payment_mode VARCHAR(20) NULL AFTER fees_payment_mode,
  ADD COLUMN attachment_upi_id VARCHAR(255) NULL AFTER fees_upi_id;
