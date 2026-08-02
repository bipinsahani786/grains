# Grain Stock Management System — Master Plan (Laravel)

> **Project:** Grain (अनाज) Stock Management System
> **Stack:** Laravel (assumed latest LTS), MySQL/PostgreSQL
> **Multi-tenancy:** Yes — Superadmin onboards multiple Companies (Business Admins)
> **Status:** Superadmin + Company onboarding already built. Business-side modules (below) are pending.

---

## 0. High-Level Architecture

The system has 4 core pillars that are tightly interlinked:

1. **Master Data** — Party, Grain, Broker, Godown
2. **Transactions** — Purchase, Sale (each transaction cascades into Inventory + Ledger)
3. **Inventory Engine** — Lot-wise stock tracking, FIFO-based deduction on sale
4. **Ledger / Wallet Engine** — Party outstanding, advance balance, payment history

**Golden rule:** Every Purchase and Sale must be wrapped in a DB transaction because they touch 3 tables simultaneously (the transaction table itself, `inventory_lots`, and `party_ledgers`). Never let these go out of sync.

---

## 1. User Hierarchy (Already Built)

```
Superadmin
  └── onboards → Company (Stock Manager / Business)
         inputs: email, mobile, password, company_name
         └── Manager / Admin (company-level roles)
```

This master plan covers everything **below** the company admin level — i.e., the actual grain trading business logic.

---

## 2. Module-by-Module Plan

### Step 1 — Party Management

Parties can be of type: **Farmer, Trader, Broker, Mill** — kept as a **separate lookup table** instead of enum, so new types can be added later without a migration.

**Table: `party_types`**
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| company_id | FK, nullable | nullable if types are global defaults, set if company adds custom type |
| name | string | Farmer, Trader, Broker, Mill, etc. |
| slug | string, unique | machine-friendly key |
| status | boolean | active/inactive |

Seed the 4 default types (`farmer`, `trader`, `broker`, `mill`) on company onboarding, but allow Company Admin to add custom types later from settings.

**Table: `parties`**
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| company_id | FK | multi-tenant scoping |
| party_type_id | FK → party_types.id | |
| name | string | |
| mobile | string | |
| address | text | |
| aadhar_number | string, nullable | |
| opening_balance | decimal | |
| current_outstanding | decimal | running, derived from ledger |
| advance_balance | decimal | wallet-like, credited on advance payments |
| status | boolean | active/inactive |
| created_by | FK users | |
| timestamps | | |

- Party profile page (Step 5) shows advance payments, bill minus outstanding, ledger history.
- Every party has a computed "outstanding amount" = sum of ledger debits − credits.

---

### Step 2 — Grain Master

**Table: `grains`**
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| company_id | FK | |
| name | string | Wheat, Maize, etc. |
| unit | enum(quintal, kg, etc.) | |
| hsn_code | string, nullable | for invoicing |

Keep flexible — future may need grain **variety/grade** (Wheat-A, Wheat-B). Consider a `grain_id` + `variety` nullable field now to avoid migration pain later.

---

### Step 3 — Broker Management

**Table: `brokers`**
| Column | Type |
|---|---|
| id | PK |
| company_id | FK |
| name, mobile, address | |

**Table: `broker_grain_commissions`** (pivot — broker can have different commission per grain)
| Column | Type |
|---|---|
| broker_id | FK |
| grain_id | FK |
| commission_type | enum(per_quintal, percentage) |
| commission_value | decimal |

Commission is calculated automatically at time of Purchase/Sale if a broker is attached.

---

### Step 4 — Purchase Module (Core Transaction #1)

**Table: `purchases`**
| Column | Type | Notes |
|---|---|---|
| id | PK | |
| company_id | FK | |
| lot_no | string, unique, auto-generated | e.g. `LOT-2026-00001` |
| date | date | |
| party_id | FK | seller (farmer/trader) |
| broker_id | FK, nullable | |
| grain_id | FK | |
| quantity | decimal | |
| unit | string | |
| moisture_percent | decimal, nullable | |
| rate | decimal | |
| total_amount | decimal | computed |
| created_by | FK users | |

**Table: `purchase_charges`** (add-on charges breakdown)
| Column | Type |
|---|---|
| purchase_id | FK |
| charge_type | enum(labour, transport, other) |
| amount | decimal |

**Table: `payments`** (polymorphic — used by Purchase & Sale both)
| Column | Type |
|---|---|
| id | PK |
| payable_id / payable_type | polymorphic |
| party_id | FK |
| amount | decimal |
| mode | enum(cash, bank, upi, cheque) |
| reference_no | string, nullable |
| date | date |

**Business Rule — "Agar udhar hua":** If purchase is not fully paid at time of entry, the unpaid amount becomes party's outstanding (they owe *us* payment as seller isn't the right direction — clarify: if we owe the farmer money, it's our payable; track via `party_ledgers` with correct debit/credit direction). If party already has advance balance, adjust from `advance_balance` first before creating new outstanding.

**On Purchase Save (use Laravel Observer/Event):**
1. Create `inventory_lots` entry (qty = purchase quantity, remaining_quantity = quantity)
2. Create `party_ledgers` entry reflecting payable/outstanding movement
3. If broker attached → calculate and log broker commission

---

### Step 5 — Party Profile Page

A dedicated page/controller combining:
- `party_ledgers` — full running-balance history (all purchases, sales, payments, advances in one timeline)
- Filters — date range, transaction type
- "Collect Advance" action — creates a payment entry not tied to any purchase/sale, credited directly to `advance_balance`
- Outstanding summary card at top

**Table: `party_ledgers`**
| Column | Type | Notes |
|---|---|---|
| id | PK | |
| party_id | FK | |
| transaction_type | enum(purchase, sale, payment, advance, adjustment) | |
| reference_id | bigint | polymorphic-style link to source txn |
| debit | decimal | |
| credit | decimal | |
| balance_after | decimal | running balance, computed at insert time |
| date | date | |
| remarks | string, nullable | |

---

### Step 6 & 7 — Inventory System

**Table: `inventory_lots`**
| Column | Type | Notes |
|---|---|---|
| id | PK | |
| purchase_id | FK | |
| grain_id | FK | |
| lot_no | string | |
| godown_id | FK, nullable | if warehouse-wise tracking needed |
| initial_quantity | decimal | |
| remaining_quantity | decimal | decremented on each sale/adjustment |
| purchase_rate | decimal | needed for FIFO profit calc |
| purchase_date | date | **critical for FIFO ordering** |
| status | enum(active, exhausted) | |

**Table: `godowns`** (optional but recommended)
| Column | Type |
|---|---|
| id | PK |
| company_id | FK |
| name, capacity | |

**Rule:** Every purchase automatically increases inventory. No manual inventory entry should be allowed outside of Purchase / Stock Adjustment — this keeps stock always traceable to source.

---

### Step 8 — Selling (FIFO Logic — Most Critical Part)

**Table: `sales`**
| Column | Type | Notes |
|---|---|---|
| id | PK | |
| company_id | FK | |
| date | date | |
| party_id | FK | customer |
| broker_id | FK, nullable | |
| grain_id | FK | |
| quantity | decimal | |
| unit | string | |
| rate | decimal | |
| cash_discount_percent | decimal, nullable | |
| payment_mode | multiple, via `payments` table | |

**Table: `sale_lot_allocations`** (tracks which lots fulfilled a sale — needed for FIFO + profit reporting)
| Column | Type |
|---|---|
| sale_id | FK |
| inventory_lot_id | FK |
| quantity_taken | decimal |
| rate_at_purchase | decimal | snapshot, for margin calc |

**FIFO Allocation Service (`FifoAllocationService`):**
```php
function allocateFifo($grainId, $requiredQty, $saleId)
{
    $lots = InventoryLot::where('grain_id', $grainId)
        ->where('remaining_quantity', '>', 0)
        ->orderBy('purchase_date', 'asc') // oldest lot first
        ->get();

    foreach ($lots as $lot) {
        if ($requiredQty <= 0) break;

        $takeQty = min($lot->remaining_quantity, $requiredQty);

        SaleLotAllocation::create([
            'sale_id' => $saleId,
            'inventory_lot_id' => $lot->id,
            'quantity_taken' => $takeQty,
            'rate_at_purchase' => $lot->purchase_rate,
        ]);

        $lot->decrement('remaining_quantity', $takeQty);
        if ($lot->remaining_quantity <= 0) {
            $lot->update(['status' => 'exhausted']);
        }

        $requiredQty -= $takeQty;
    }

    if ($requiredQty > 0) {
        throw new \Exception('Insufficient stock for this grain.');
    }
}
```

This table later powers **Profit/Loss reports**: `(sale.rate - sale_lot_allocations.rate_at_purchase) * quantity_taken`.

---

### Step 9 — Stock Adjustment System

**Table: `stock_adjustments`**
| Column | Type |
|---|---|
| id | PK |
| inventory_lot_id | FK |
| type | enum(damage, quality_decrement, other) |
| quantity_adjusted | decimal |
| reason | text |
| adjusted_by | FK users |
| date | date |

Directly decrements `inventory_lots.remaining_quantity`, independent of any sale. Kept as a separate table (not merged into sales) purely for **audit trail correctness** — otherwise stock reports get polluted with fake sales.

---

### Step 10 — Reports & Analytics

Minimum viable report set:
- Purchase Register
- Sales Register
- Party Ledger / Statement
- Stock Report (lot-wise AND grain-wise summary)
- Profit & Loss (via `sale_lot_allocations`)
- Broker Commission Report
- Outstanding/Advance Summary (all parties)

---

### Step 11 — Excel Export & Invoice Generation

- **Excel Export:** `maatwebsite/laravel-excel` package for all reports above.
- **Invoice PDF:** `barryvdh/laravel-dompdf` or Laravel Snappy.
  - Must support signature/stamp image overlay — store stamp image per company in settings, overlay on invoice template at print time.

---

### Step 12 — Roles Based System

Use `spatie/laravel-permission`.

```
Roles: Superadmin, Company Admin, Manager, Staff
Sample Permissions:
  - create-purchase, edit-purchase, delete-purchase
  - create-sale, edit-sale, delete-sale
  - manage-parties, manage-grains, manage-brokers
  - view-reports, export-reports
  - adjust-stock
  - collect-payment
```
This layer sits on top of the already-built Superadmin/Company onboarding — no rework needed there.

---

### Step 13 — Audit Logs

Use `spatie/laravel-activitylog`. Auto-logs create/update/delete on all key models (Purchase, Sale, Party, Payment, StockAdjustment) without manual logging code. Log viewer should be filterable by user, model, date.

---

## 3. Recommended Build Order (Dependency-Based)

1. **Masters:** Party, Grain, Broker, Godown
2. **Purchase module** + auto Inventory Lot creation (test this thoroughly before moving on)
3. **Party Ledger + Profile Page** (validate against Purchase entries)
4. **Sale module + FIFO Allocation Service**
5. **Stock Adjustment**
6. **Payments module** (polymorphic, multi-mode) — attach to Purchase/Sale once base transactions are stable
7. **Reports + Excel Export + Invoice PDF**
8. **Roles & Permissions** (Spatie)
9. **Audit Logs** (Spatie Activitylog)

Roles and Audit Logs are cross-cutting — can be added at any point without breaking existing logic, hence kept last.

---

## 4. Key Business Rules Checklist (for AI reference while coding)

- [ ] Every Purchase/Sale is a DB transaction — Purchase/Sale row + Inventory update + Ledger entry must succeed or fail together.
- [ ] FIFO is enforced at the `inventory_lots` level using `purchase_date` ordering — never delete a lot, only decrement `remaining_quantity`.
- [ ] Party's `advance_balance` must be checked/adjusted before creating new outstanding on any transaction.
- [ ] Broker commission auto-calculates per grain, per broker, at time of Purchase/Sale if broker_id is set.
- [ ] Stock Adjustments (damage/quality loss) never create fake sales — always a separate `stock_adjustments` entry.
- [ ] All tables are scoped by `company_id` for multi-tenancy (global scope recommended in Laravel models).
- [ ] Invoices must support signature/stamp overlay and multiple payment modes shown clearly.
- [ ] Roles/permissions gate every write action (create/edit/delete) at controller or policy level.
- [ ] All key models (Purchase, Sale, Party, Payment, StockAdjustment) must be under Activity Log.

---

## 5. Suggested Laravel Package List

| Purpose | Package |
|---|---|
| Roles & Permissions | spatie/laravel-permission |
| Audit Logging | spatie/laravel-activitylog |
| Excel Export/Import | maatwebsite/laravel-excel |
| PDF Invoice Generation | barryvdh/laravel-dompdf |
| Multi-tenancy scoping (optional) | stancl/tenancy OR custom global scopes |

---

*End of master plan. Use this file as the single source of truth for module structure, table schema, and business rules while building the business-side of the system in Laravel.*