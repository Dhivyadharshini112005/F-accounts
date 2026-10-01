F-TAXI ADVANCE + CASH/A-C UPDATE

This is an update/patch package, not a complete Laravel project.
Copy the files into the matching locations in your existing taxi-accounts project.

Implemented:
1. Adds + Advance button beside + Add Expense.
2. Adds Employee Advances module.
3. Advance has employee name, amount, Cash/A-C, date and description.
4. Selecting A/C requires a manually entered UPI ID.
5. Advance is deducted from Cash balance when Cash is selected.
6. Advance is deducted from A/C balance when A/C is selected.
7. Expense also supports manually entered UPI ID for A/C.
8. Expense/Advance backend prevents saving when the selected balance is zero/negative or insufficient.
9. Create Expense page shows a warning popup when the selected Cash/A-C balance has no money.
10. Dashboard Cash/A-C balances subtract advances.
11. Negative Cash/A-C balance cards are shown in red in the included dashboard view.
12. Existing login route and logout route are preserved.
13. Existing Income calculation/fields are not changed.

INSTALL:

1. Back up your existing project.
2. Copy the files from this package into the matching project paths.
3. Run:

   php artisan migrate
   php artisan optimize:clear

4. Open Expenses and use + Advance.

IMPORTANT:
- The package expects the existing incomes table and its fees/gst payment-mode columns.
- The package creates a new advances table.
- The package adds upi_id to expenses only if that column does not already exist.
- The included dashboard view is the existing Cash/A-C balance-card version with the balance calculation updated for advances.
