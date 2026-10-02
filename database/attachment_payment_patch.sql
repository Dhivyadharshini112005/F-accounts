-- Core PHP accounts dashboard: Attachment payment fields
-- Safe to run on MySQL 8.x. Run this once in phpMyAdmin for your accounts database.

ALTER TABLE incomes
    ADD COLUMN IF NOT EXISTS attachment_amount DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER fees_amount,
    ADD COLUMN IF NOT EXISTS attachment_payment_mode VARCHAR(20) NULL AFTER fees_payment_mode,
    ADD COLUMN IF NOT EXISTS attachment_upi_id VARCHAR(255) NULL AFTER fees_upi_id;

UPDATE incomes
SET attachment_amount = COALESCE(attachment_amount, 0),
    attachment_payment_mode = COALESCE(NULLIF(attachment_payment_mode, ''), 'cash')
WHERE attachment_amount IS NULL OR attachment_payment_mode IS NULL OR attachment_payment_mode = '';
