# Design Document - Bank Accounts Module

## Overview

The Bank Accounts module is a sub-module within the CoffeeSoft accounting system that enables administrators to manage banks and their associated accounts across business units. The module follows the MVC architecture pattern established by CoffeeSoft framework, utilizing jQuery for frontend interactions, PHP for backend logic, and MySQL for data persistence.

## Architecture

### Database Schema

**Table: bank**

```sql
CREATE TABLE bank (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_bank_name (name)
);
```

**Table: bank_account**

```sql
CREATE TABLE bank_account (
    id INT AUTO_INCREMENT PRIMARY KEY,
    udn_id INT NOT NULL,
    bank_id INT NOT NULL,
    account_alias VARCHAR(100),
    last_4_digits CHAR(4) NOT NULL,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (udn_id) REFERENCES udn(id),
    FOREIGN KEY (bank_id) REFERENCES bank(id),
    INDEX idx_udn_id (udn_id),
    INDEX idx_bank_id (bank_id),
    INDEX idx_active (active)
);
```

## Components and Interfaces

### Frontend Component (banco.js)

**Class: AdminBankAccounts extends Templates**

**Key Methods:**

```javascript
class AdminBankAccounts extends Templates {
    constructor(link, div_modulo)
    render()                          // Initialize and render the module
    layout()                          // Create primary layout structure
    filterBar()                       // Render filter controls
    lsAccounts()                      // Display bank accounts table
    addBank()                         // Show add bank modal
    addAccount()                      // Show add account modal
    editAccount(id)                   // Show edit account modal
    toggleStatus(id, currentStatus)   // Activate/deactivate account
    jsonBank()                        // Return bank form fields
    jsonAccount()                     // Return account form fields
}
```

### Controller (ctrl-banco.php)

**Class: ctrl extends mdl**

**Methods:**

```php
class ctrl extends mdl {
    function init()                   // Load initial data (UDN list, banks)
    function lsAccounts()             // List accounts with filters
    function getAccount()             // Get single account by ID
    function addBank()                // Create new bank
    function addAccount()             // Create new account
    function editAccount()            // Update existing account
    function toggleStatus()           // Change account active status
}
```

### Model (mdl-banco.php)

**Class: mdl extends CRUD**

**Methods:**

```php
class mdl extends CRUD {
    function listAccounts($filters)           // Get accounts with filters
    function getAccountById($id)              // Get single account
    function createBank($data)                // Insert new bank
    function createAccount($data)             // Insert new account
    function updateAccount($data)             // Update account
    function existsBankByName($name)          // Check for duplicate banks
    function lsUDN()                          // Get business units for filter
    function lsBanks()                        // Get active banks for dropdown
}
```

## Data Models

### Field Descriptions

**bank table:**
- `id`: Primary key
- `name`: Bank name (e.g., "BBVA", "Santander")
- `active`: Status flag (1=active, 0=inactive)
- `created_at`: Record creation timestamp
- `updated_at`: Last modification timestamp

**bank_account table:**
- `id`: Primary key
- `udn_id`: Foreign key to business unit table
- `bank_id`: Foreign key to bank table
- `account_alias`: Optional friendly name for the account
- `last_4_digits`: Last 4 digits of account number (CHAR(4))
- `active`: Status flag (1=active, 0=inactive)
- `created_at`: Record creation timestamp
- `updated_at`: Last modification timestamp

## Error Handling

### Error Response Codes

| Code | Meaning | Usage |
|------|---------|-------|
| 200 | Success | Operation completed successfully |
| 400 | Bad Request | Missing or invalid required fields |
| 404 | Not Found | Account/Bank ID doesn't exist |
| 409 | Conflict | Duplicate bank name |
| 500 | Server Error | Database or system error |

## Design Decisions

### 1. Separate Bank and Account Tables

**Decision:** Create two separate tables (bank and bank_account) rather than a single denormalized table.

**Rationale:**
- Reduces data redundancy
- Allows multiple accounts per bank
- Easier to maintain bank information
- Supports future features (bank details, logos, etc.)

### 2. CHAR(4) for Last 4 Digits

**Decision:** Use CHAR(4) data type for last_4_digits field.

**Rationale:**
- Fixed length ensures consistent storage
- Faster queries compared to VARCHAR
- Enforces 4-character constraint at database level
- Supports leading zeros (e.g., "0123")

### 3. Optional Account Alias

**Decision:** Make account_alias field optional (nullable).

**Rationale:**
- Not all accounts need friendly names
- Provides flexibility for users
- Can be added later without breaking existing records

### 4. Soft Delete for Both Banks and Accounts

**Decision:** Implement active status for both banks and accounts.

**Rationale:**
- Preserves historical data
- Allows reactivation if needed
- Maintains referential integrity
- Complies with accounting audit requirements
