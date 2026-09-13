@extends('layouts.group_admin')

@section('title', 'New Investment')

@push('styles')
<style>
    .custom-font {
        font-family: 'Poppins', sans-serif;
    }

    /* Form specific styles */
    .form-container {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .form-input {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        width: 100%;
        transition: all 0.2s;
        color: #000000;
    }

    .form-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .submit-btn {
        background: linear-gradient(to right, #3b82f6, #2563eb);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .submit-btn:hover {
        background: linear-gradient(to right, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Error message styles */
    .error-message {
        color: #dc2626;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .error-message::before {
        content: "⚠️";
        font-size: 1rem;
    }

    .form-input.error {
        border-color: #dc2626;
    }

    /* Investment type select styles */
    .investment-type-select {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        width: 100%;
        transition: all 0.2s;
        color: #000000;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1.5em 1.5em;
    }
</style>
@endpush

@section('content')
<div class="p-6 w-full max-w-4xl mx-auto">
    <div class="form-container p-8">
        <!-- Form Header -->
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-semibold custom-font text-black mb-2">
                <i class="fas fa-piggy-bank mr-2 text-blue-600"></i>
                New Investment Entry
            </h2>
            <p class="text-lg text-black">Please fill in the details below to record a new investment</p>
        </div>

        <!-- Investment Form -->
        <form method="POST" action="{{ route('admin.investment.store', $group->group_id) }}" class="space-y-6" id="investmentForm">
            @csrf
            <div class="space-y-6">
                <!-- Amount Field -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-black mb-2">
                        Investment Amount (BDT)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-black">৳</span>
                        <input type="number" id="amount" name="amount" step="0.01"
                            class="form-input pl-8 @error('amount') error @enderror"
                            placeholder="Enter investment amount" required value="{{ old('amount') }}">
                    </div>
                    @error('amount')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Investment Type Field -->
                <div>
                    <label for="investment_type" class="block text-sm font-medium text-black mb-2">
                        Investment Type
                    </label>
                    <select id="investment_type" name="investment_type"
                        class="investment-type-select @error('investment_type') error @enderror" required>
                        <option value="">Select investment type</option>
                        <option value="savings" {{ old('investment_type') == 'savings' ? 'selected' : '' }}>Savings Account</option>
                        <option value="fixed_deposit" {{ old('investment_type') == 'fixed_deposit' ? 'selected' : '' }}>Fixed Deposit</option>
                        <option value="mutual_fund" {{ old('investment_type') == 'mutual_fund' ? 'selected' : '' }}>Mutual Fund</option>
                        <option value="stocks" {{ old('investment_type') == 'stocks' ? 'selected' : '' }}>Stocks</option>
                        <option value="bonds" {{ old('investment_type') == 'bonds' ? 'selected' : '' }}>Bonds</option>
                        <option value="other" {{ old('investment_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('investment_type')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Expected Profit Field -->
                <div>
                    <label for="ex_profit" class="block text-sm font-medium text-black mb-2">
                        Expected Return Amount (BDT)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-black">৳</span>
                        <input type="number" id="ex_profit" name="ex_profit" step="0.01"
                            class="form-input pl-8 @error('ex_profit') error @enderror"
                            placeholder="Enter expected return amount" required value="{{ old('ex_profit') }}">
                    </div>
                    @error('ex_profit')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Expected Return Date Field -->
                <div>
                    <label for="ex_return_date" class="block text-sm font-medium text-black mb-2">
                        Expected Return Date
                    </label>
                    <input type="date" id="ex_return_date" name="ex_return_date"
                        class="form-input @error('ex_return_date') error @enderror"
                        required value="{{ old('ex_return_date') }}">
                    @error('ex_return_date')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" class="submit-btn w-full">
                    <i class="fas fa-save mr-2"></i> Save Investment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Form validation
    const form = document.getElementById('investmentForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear previous error messages
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
        
        let hasError = false;
        const amount = parseFloat(document.getElementById('amount').value);
        const investmentType = document.getElementById('investment_type').value;
        const exProfit = parseFloat(document.getElementById('ex_profit').value);
        const exReturnDate = document.getElementById('ex_return_date').value;

        // Validate amount
        if (!amount || amount <= 0) {
            showError('amount', 'Please enter a valid investment amount');
            hasError = true;
        }

        // Validate investment type
        if (!investmentType) {
            showError('investment_type', 'Please select an investment type');
            hasError = true;
        }

        // Validate expected profit
        if (!exProfit || exProfit <= 0) {
            showError('ex_profit', 'Please enter a valid expected return amount');
            hasError = true;
        }

        // Validate return date
        if (!exReturnDate) {
            showError('ex_return_date', 'Please select a return date');
            hasError = true;
        } else {
            const selectedDate = new Date(exReturnDate);
            const today = new Date();
            if (selectedDate <= today) {
                showError('ex_return_date', 'Return date must be after today');
                hasError = true;
            }
        }

        if (!hasError) {
            this.submit();
        }
    });

    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        field.classList.add('error');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }
</script>

@if(session('success'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "{{ route('admin.investment.create', $group->group_id) }}";
        }
    });
</script>
@endif

@if(session('error'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
        confirmButtonText: 'OK'
    });
</script>
@endif
@endpush 