@extends('layouts.group_admin')

@section('title', 'Record Investment Return')

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

    /* Investment select styles */
    .investment-select {
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

    /* Investment details card */
    .investment-details {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
    }

    .investment-details p {
        margin: 0.5rem 0;
        color: #000000;
    }
</style>
@endpush

@section('content')
<div class="p-6 w-full max-w-4xl mx-auto">
    <div class="form-container p-8">
        <!-- Form Header -->
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-semibold custom-font text-black mb-2">
                <i class="fas fa-hand-holding-usd mr-2 text-blue-600"></i>
                Record Investment Return
            </h2>
            <p class="text-lg text-black">Record returns for your group's investments</p>
        </div>

        <!-- Investment Return Form -->
        <form method="POST" action="{{ route('admin.investment.return.store', $group->group_id) }}" class="space-y-6" id="returnForm">
            @csrf
            <div class="space-y-6">
                <!-- Investment Selection -->
                <div>
                    <label for="investment_id" class="block text-sm font-medium text-black mb-2">
                        Select Investment
                    </label>
                    <select id="investment_id" name="investment_id"
                        class="investment-select @error('investment_id') error @enderror" required>
                        <option value="">Select an investment</option>
                        @foreach($pendingInvestments as $investment)
                            <option value="{{ $investment->investment_id }}" 
                                data-amount="{{ $investment->amount }}"
                                data-type="{{ $investment->investment_type }}"
                                data-expected-return="{{ $investment->ex_profit }}"
                                data-return-date="{{ $investment->ex_return_date }}">
                                {{ $investment->investment_type }} - ৳{{ number_format($investment->amount, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('investment_id')
                        <div class="error-message">{{ $message }}</div>
                    @enderror

                    <!-- Investment Details Card -->
                    <div id="investmentDetails" class="investment-details hidden">
                        <p><strong>Investment Type:</strong> <span id="investmentType"></span></p>
                        <p><strong>Amount:</strong> ৳<span id="investmentAmount"></span></p>
                        <p><strong>Expected Return:</strong> ৳<span id="expectedReturn"></span></p>
                        <p><strong>Expected Return Date:</strong> <span id="returnDate"></span></p>
                    </div>
                </div>

                <!-- Return Amount Field -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-black mb-2">
                        Return Amount (BDT)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-black">৳</span>
                        <input type="number" id="amount" name="amount" step="0.01"
                            class="form-input pl-8 @error('amount') error @enderror"
                            placeholder="Enter return amount" required value="{{ old('amount') }}">
                    </div>
                    @error('amount')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description Field -->
                <div>
                    <label for="description" class="block text-sm font-medium text-black mb-2">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="4"
                        class="form-input @error('description') error @enderror"
                        placeholder="Enter details about this return" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" class="submit-btn w-full">
                    <i class="fas fa-save mr-2"></i> Record Return
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Form validation
    const form = document.getElementById('returnForm');
    const investmentSelect = document.getElementById('investment_id');
    const investmentDetails = document.getElementById('investmentDetails');

    // Show investment details when an investment is selected
    investmentSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            investmentDetails.classList.remove('hidden');
            document.getElementById('investmentType').textContent = selectedOption.dataset.type;
            document.getElementById('investmentAmount').textContent = parseFloat(selectedOption.dataset.amount).toFixed(2);
            document.getElementById('expectedReturn').textContent = parseFloat(selectedOption.dataset.expectedReturn).toFixed(2);
            document.getElementById('returnDate').textContent = new Date(selectedOption.dataset.returnDate).toLocaleDateString();
        } else {
            investmentDetails.classList.add('hidden');
        }
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear previous error messages
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
        
        let hasError = false;
        const investmentId = document.getElementById('investment_id').value;
        const amount = parseFloat(document.getElementById('amount').value);
        const description = document.getElementById('description').value;

        // Validate investment selection
        if (!investmentId) {
            showError('investment_id', 'Please select an investment');
            hasError = true;
        }

        // Validate amount
        if (!amount || amount <= 0) {
            showError('amount', 'Please enter a valid return amount');
            hasError = true;
        }

        // Validate description
        if (!description || description.trim().length < 10) {
            showError('description', 'Please provide a detailed description (minimum 10 characters)');
            hasError = true;
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
            window.location.href = "{{ route('admin.investment.return.create', $group->group_id) }}";
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