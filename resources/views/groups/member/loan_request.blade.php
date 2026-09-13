@extends('layouts.group_member')

@section('title', 'Request Loan')

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

    .dark-mode .form-container {
        background: #2d2d2d;
        border-color: #4d4d4d;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2), 0 2px 4px -1px rgba(0, 0, 0, 0.1);
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

    .dark-mode .form-input {
        background-color: #3d3d3d;
        border-color: #4d4d4d;
        color: #e0e0e0;
    }

    .form-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .dark-mode .form-input:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.2);
    }

    .quick-amount-btn {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        transition: all 0.2s;
        color: #000000;
    }

    .dark-mode .quick-amount-btn {
        background-color: #3d3d3d;
        border-color: #4d4d4d;
        color: #e0e0e0;
    }

    .quick-amount-btn:hover {
        background-color: #f3f4f6;
    }

    .dark-mode .quick-amount-btn:hover {
        background-color: #4d4d4d;
    }

    .quick-amount-btn.selected {
        background-color: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    .dark-mode .quick-amount-btn.selected {
        background-color: #60a5fa;
        border-color: #60a5fa;
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

    .dark-mode .submit-btn {
        background: linear-gradient(to right, #60a5fa, #3b82f6);
    }

    .submit-btn:hover {
        background: linear-gradient(to right, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .dark-mode .submit-btn:hover {
        background: linear-gradient(to right, #3b82f6, #2563eb);
    }

    /* Text colors for dark mode */
    .text-gray-700 {
        color: #000000;
    }

    .text-gray-600 {
        color: #000000;
    }

    .dark-mode .text-gray-700 {
        color: #e0e0e0;
    }

    .dark-mode .text-gray-600 {
        color: #cccccc;
    }

    .dark-mode .text-blue-600 {
        color: #60a5fa;
    }

    .dark-mode .text-blue-800 {
        color: #93c5fd;
    }

    /* Custom scrollbar for dark mode */
    .dark-mode ::-webkit-scrollbar-track {
        background: #2d2d2d;
    }

    .dark-mode ::-webkit-scrollbar-thumb {
        background: #4d4d4d;
    }

    .dark-mode ::-webkit-scrollbar-thumb:hover {
        background: #5d5d5d;
    }

    /* Force black text in light mode for form header, labels, and p */
    .form-container label,
    .form-container h2,
    .form-container p,
    .form-container .quick-amount-btn {
        color: #000 !important;
    }
    .dark-mode .form-container label,
    .dark-mode .form-container h2,
    .dark-mode .form-container p {
        color: inherit !important;
    }
</style>
@endpush

@section('content')
<div class="p-6 w-full max-w-4xl mx-auto">
    <div class="form-container p-8">
        <!-- Form Header -->
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-semibold custom-font text-blue-700">
                <i class="fa-solid fa-hand-holding-usd mr-2 text-blue-600"></i>
                Loan Request Form
            </h2>
            <p class="mt-2 text-black">Please fill in the details below to submit your loan request</p>
        </div>

        <!-- Loan Criteria & Rules -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-clipboard-list text-blue-600 text-lg"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-blue-800 mb-3">
                        Loan Eligibility Criteria
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="space-y-2">
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-600 mt-0.5 mr-2"></i>
                                <span class="text-blue-700">Must be an approved group member</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-600 mt-0.5 mr-2"></i>
                                <span class="text-blue-700">No pending leave requests</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-600 mt-0.5 mr-2"></i>
                                <span class="text-blue-700">No existing pending/approved loans</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-600 mt-0.5 mr-2"></i>
                                <span class="text-blue-700">Loan amount within emergency fund limit</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-600 mt-0.5 mr-2"></i>
                                <span class="text-blue-700">Group has sufficient current balance</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-2"></i>
                                <span class="text-blue-700">Admin approval required via poll</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-2"></i>
                                <span class="text-blue-700">One loan at a time policy</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-2"></i>
                                <span class="text-blue-700">Return date must be in the future</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-2"></i>
                                <span class="text-blue-700">Terms and conditions must be accepted</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Group Current Balance Information -->
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-piggy-bank text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-green-800">Group Current Balance</h3>
                        <p class="text-lg font-bold text-green-900">৳{{ number_format($groupCurrentBalance, 2) }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-green-700">Emergency Fund Limit</p>
                    <p class="text-lg font-bold text-green-900">৳{{ number_format($group->emergency_fund, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Loan Request Form -->
        <form method="POST" action="{{ route('member.loan.request.store', $group->group_id) }}" class="space-y-6">
            @csrf
            <div class="space-y-6">
                <!-- Amount Field -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-black dark:text-gray-300 mb-2">
                        Loan Amount (BDT)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-black dark:text-gray-400">৳</span>
                        <input type="number" id="amount" name="amount"
                            class="form-input pl-8"
                            placeholder="Enter amount" required value="{{ old('amount') }}">
                    </div>
                    @error('amount')
                        <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Quick Amount Selection -->
                <div class="mt-3 flex flex-wrap gap-2">
                    <div class="quick-amount-wrapper">
                        <input type="radio" name="quick_amount" id="amount500" value="500" class="hidden peer"
                            onclick="document.getElementById('amount').value=this.value">
                        <label for="amount500"
                            class="quick-amount-btn cursor-pointer text-black dark:text-gray-200">
                            ৳ 500
                        </label>
                    </div>

                    <div class="quick-amount-wrapper">
                        <input type="radio" name="quick_amount" id="amount1000" value="1000" class="hidden peer"
                            onclick="document.getElementById('amount').value=this.value">
                        <label for="amount1000"
                            class="quick-amount-btn cursor-pointer text-black dark:text-gray-200">
                            ৳ 1,000
                        </label>
                    </div>

                    <div class="quick-amount-wrapper">
                        <input type="radio" name="quick_amount" id="amount1500" value="1500" class="hidden peer"
                            onclick="document.getElementById('amount').value=this.value">
                        <label for="amount1500"
                            class="quick-amount-btn cursor-pointer text-black dark:text-gray-200">
                            ৳ 1,500
                        </label>
                    </div>

                    <div class="quick-amount-wrapper">
                        <input type="radio" name="quick_amount" id="amount2000" value="2000" class="hidden peer"
                            onclick="document.getElementById('amount').value=this.value">
                        <label for="amount2000"
                            class="quick-amount-btn cursor-pointer text-black dark:text-gray-200">
                            ৳ 2,000
                        </label>
                    </div>
                </div>

                <!-- Reason Field -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-black dark:text-gray-300 mb-2">
                        Reason for Loan
                    </label>
                    <textarea id="reason" name="reason" rows="4"
                        class="form-input"
                        placeholder="Please explain your reason for requesting a loan" required>{{ old('reason') }}</textarea>
                    @error('reason')
                        <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Return Date Field -->
                <div>
                    <label for="return_date" class="block text-sm font-medium text-black dark:text-gray-300 mb-2">
                        Expected Return Date
                    </label>
                    <input type="date" id="return_date" name="return_date"
                        class="form-input"
                        required value="{{ old('return_date') }}">
                    @error('return_date')
                        <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Terms and Conditions Acceptance -->
                <div class="mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="terms" name="terms"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700" required>
                        <label for="terms" class="ml-2 block text-sm text-black dark:text-gray-300">
                            I agree to the <a href="/terms_and_condition.php" target="_blank"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline">Terms and Conditions</a>
                        </label>
                    </div>
                    @error('terms')
                        <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit"
                    class="submit-btn w-full">
                    <i class="fas fa-paper-plane mr-2"></i> Submit Loan Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Handle quick amount selection
    document.querySelectorAll('input[name="quick_amount"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove selected class from all labels
            document.querySelectorAll('.quick-amount-btn').forEach(label => {
                label.classList.remove('selected');
            });
            // Add selected class to the clicked label
            if (this.checked) {
                this.nextElementSibling.classList.add('selected');
            }
        });
    });
</script>

@if(session('success') && session('just_submitted'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = window.location.pathname + window.location.search;
        }
    });
</script>
@endif

@if(session('error') && !session('just_submitted'))
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