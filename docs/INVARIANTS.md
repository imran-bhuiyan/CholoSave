# CholoSave Constitution & Architectural Invariants

Collaborative FinTech savings circles, bidding cycles, and peer-to-peer payout pools. Laravel + MySQL.

> **Distinction (`not_the_same_as`):**  
> TakaFlow is a single-user local ledger with offline regex parsing. ConsoleTheory is a media publication platform. CholoSave is multi-user with Eloquent `decimal:2`, Sanctum tokens, and database row locks.

---

## 🏛️ The Rooms

### Hot Room 1: `Money`
- **Paths**: `app/Http/Controllers/PaymentController.php`, `app/Http/Controllers/WithdrawalController.php`, `app/Models/SavingsPool.php`, `app/Models/Transaction.php`, `database/migrations/**`
- **Responsibility**: Financial contributions, bidding cycles, group balance accounting, and payout execution.
- **Key Law**: All pool balance and withdrawal mutations must execute within a `DB::transaction()` and apply pessimistic row locking (`lockForUpdate()`) to prevent race conditions and double-spending.

### Hot Room 2: `Realtime`
- **Paths**: `routes/channels.php`, `config/broadcasting.php`, `app/Events/**`
- **Responsibility**: Realtime bidding events, notifications, and group chat.
- **Key Law**: All WebSocket broadcasts must use `PrivateChannel` with explicit authorization checks verifying that `auth()->id()` is an active participant in the circle.

### Hot Room 3: `Tips`
- **Paths**: `app/Http/Controllers/AiTipsController.php`, `ai_backend/**`
- **Responsibility**: Financial wellness heuristics and automated saving recommendations.
- **Key Law**: Python microservice calls must fail-soft; failure of AI tips must never block core transaction processing.

### 💤 Dormant Rooms
- **`Auth`**: Sanctum API tokens and session login stable; modify only on dedicated auth tasks.
- **`Views`**: Blade templates stable; modify only on user interface tasks.

---

## 🛡️ Architectural Invariants

1. **Tracked Environment File Hygiene (Boundary)**:
   - The repository currently contains a committed `env` file. It must be untracked (`git rm --cached env`), added to `.gitignore`, and any exposed secrets rotated (Task `cs-purge-env-leak`).
2. **Financial Concurrency & Locking (Boundary)**:
   - Any code that checks a balance before deducting or disbursing funds must lock the row:
     `$pool = SavingsPool::where('id', $id)->lockForUpdate()->first();`
3. **Pusher Private Channel Authorization (Boundary)**:
   - Public channels (`new Channel(...)`) are prohibited for financial or user-to-user events.
4. **TLS Certificate Verification (Boundary)**:
   - `CURLOPT_SSL_VERIFYPEER` must be set to `true` (or `1`) in `config/broadcasting.php`.

---

## 🧪 Verification Commands

- **Unit & Feature Suite**: `php artisan test`
- **Diff Security Audit**: `ai inspect`
