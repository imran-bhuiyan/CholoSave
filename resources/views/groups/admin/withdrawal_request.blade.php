@extends('layouts.group_admin')
@section('title', 'Withdrawal Request')

@push('styles')
<style>
    .custom-font {
        font-family: 'Poppins', sans-serif;
    }
    .form-container {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
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
    .error-message {
        color: #ff0000;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .error-message::before {
        content: "⚠️";
        font-size: 1rem;
    }
</style>
@endpush

@section('content')
<div class="p-6 w-full max-w-4xl mx-auto">
    <div class="form-container p-8">
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-semibold custom-font text-blue-700">
                <i class="fa-solid fa-money-bill-wave mr-2 text-blue-600"></i>
                Withdrawal Request Form
            </h2>
            <p class="mt-2 text-black">Please fill in the details below to submit your withdrawal request</p>
        </div>

        <!-- Warning Message -->
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-lg"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Early Withdrawal Notice
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p class="mb-2">
                            <strong>⚠️ Important:</strong> We strongly discourage withdrawing your savings before completing the full savings period unless it's a genuine emergency.
                        </p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Early withdrawals may affect the group's financial stability</li>
                            <li>You may miss out on potential investment returns</li>
                            <li>Consider the impact on your long-term financial goals</li>
                            <li>Only proceed if this is a true emergency situation</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Information -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-wallet text-blue-600 text-lg"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-blue-800 mb-2">
                        Your Available Balance
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-blue-700">Total Savings:</span>
                                <span class="font-medium text-blue-900">৳{{ number_format($totalSavings, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">Approved Withdrawals:</span>
                                <span class="font-medium text-orange-600">-৳{{ number_format($totalApprovedWithdrawals, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">Approved Loans:</span>
                                <span class="font-medium text-red-600">-৳{{ number_format($totalApprovedLoans, 2) }}</span>
                            </div>
                        </div>
                        <div class="border-l border-blue-300 pl-4">
                            <div class="text-center">
                                <div class="text-lg font-bold text-blue-900 mb-1">
                                    ৳{{ number_format($netAvailableBalance, 2) }}
                                </div>
                                <div class="text-xs text-blue-700">Available for Withdrawal</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.withdrawal.request.store', $group->group_id) }}" class="space-y-6">
            @csrf
            <div>
                <label for="amount" class="block text-sm font-medium text-black mb-2">Withdrawal Amount (BDT)</label>
                <input type="number" id="amount" name="amount" class="form-input @error('amount') border-red-500 @enderror" placeholder="Enter amount" required value="{{ old('amount') }}">
                @error('amount')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="payment_number" class="block text-sm font-medium text-black mb-2">Payment Number</label>
                <input type="text" id="payment_number" name="payment_number" class="form-input @error('payment_number') border-red-500 @enderror" placeholder="Enter payment number" required value="{{ old('payment_number') }}">
                @error('payment_number')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="payment_method" class="block text-sm font-medium text-black mb-2">Payment Method</label>
                <select id="payment_method" name="payment_method" class="form-input @error('payment_method') border-red-500 @enderror" required>
                    <option value="">Select a method</option>
                    <option value="Bkash" {{ old('payment_method') == 'Bkash' ? 'selected' : '' }}>Bkash</option>
                    <option value="Nagad" {{ old('payment_method') == 'Nagad' ? 'selected' : '' }}>Nagad</option>
                    <option value="Rocket" {{ old('payment_method') == 'Rocket' ? 'selected' : '' }}>Rocket</option>
                </select>
                @error('payment_method')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div class="pt-4">
                <button type="submit" class="submit-btn w-full">
                    <i class="fas fa-paper-plane mr-2"></i> Submit Withdrawal Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
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