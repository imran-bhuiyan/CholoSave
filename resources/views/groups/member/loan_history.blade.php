@extends('layouts.group_member')

@section('title', 'Loan History')

@push('styles')
<style>
    .custom-font {
        font-family: 'Poppins', sans-serif;
    }

    .table-container {
        scrollbar-width: thin;
        scrollbar-color: #CBD5E0 #EDF2F7;
    }

    .table-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #EDF2F7;
    }

    .table-container::-webkit-scrollbar-thumb {
        background-color: #CBD5E0;
        border-radius: 4px;
    }

    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Force black text in light mode */
    .force-light h1,
    .force-light h2,
    .force-light th,
    .force-light td {
        color: #000 !important;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .status-approved {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fcd34d;
    }

    .status-repaid {
        background-color: #dbeafe;
        color: #1e40af;
        border: 1px solid #93c5fd;
    }

    .pay-button {
        background: linear-gradient(to right, #3b82f6, #2563eb);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .pay-button:hover {
        background: linear-gradient(to right, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="flex-1 overflow-hidden">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-center">
            <h1 class="text-2xl font-semibold custom-font text-black">
                <i class="fa-solid fa-file-invoice-dollar mr-2 text-blue-600 !important"></i>
                Loan History
            </h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="p-6 overflow-auto h-[calc(100vh-4rem)]">
        <div class="max-w-7xl mx-auto animate-fade-in">
            <!-- Loan History Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-black">Loan Details</h2>
                </div>
                <div class="table-container overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loan Amount (BDT)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approve Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($loans as $index => $loan)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($loan->amount) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $loan->approve_date ? $loan->approve_date->format('M d, Y') : 'Not Approved' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $loan->return_time ? $loan->return_time->format('M d, Y') : 'Not Set' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="status-badge 
                                            @if($loan->status === 'approved') status-approved
                                            @elseif($loan->status === 'pending') status-pending
                                            @elseif($loan->status === 'repaid') status-repaid
                                            @endif">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($loan->status === 'approved')
                                            <button class="pay-button" data-loan-id="{{ $loan->id }}" data-amount="{{ $loan->amount }}">
                                                Pay
                                            </button>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No loan history found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl relative animate-fade-in p-0 overflow-hidden">
        <!-- Close Button -->
        <button type="button" id="cancelPayment" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl font-bold focus:outline-none z-10">
            &times;
        </button>
        <div class="grid md:grid-cols-2 gap-0">
            <!-- Payment Summary -->
            <div class="bg-blue-700 text-white p-8 flex flex-col justify-center min-h-[320px]">
                <div class="flex flex-col items-center">
                    <div class="bg-white rounded-full p-3 mb-2">
                        <i class="fa-solid fa-money-bill-wave text-blue-600 text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-2">Loan Repayment</h2>
                    <p class="text-blue-100 text-sm mb-6">Confirm your repayment details below.</p>
                </div>
                <div class="space-y-2 text-base">
                    <div class="flex justify-between"><span>Repayment Amount:</span> <span class="font-bold">৳<span id="summaryAmount">0.00</span></span></div>
                </div>
            </div>
            <!-- Payment Methods & Form -->
            <div class="bg-white p-8 flex flex-col justify-center">
                <form id="paymentForm" class="space-y-6 mt-2">
                    <input type="hidden" id="loan_id" name="loan_id">
                    <input type="hidden" id="payment_method" name="payment_method">
                    <div>
                        <label class="block mb-2 font-medium text-gray-700">Select Payment Method</label>
                        <div class="flex gap-4">
                            @if(in_array('bkash', $paymentMethods))
                            <button type="button" data-method="bkash" class="method-btn border rounded-lg p-3 flex-1 flex flex-col items-center focus:outline-none bg-white hover:shadow-md transition">
                                <img src="{{ asset('images/payment/bkash.png') }}" alt="bKash" class="h-10 mb-1">
                                <span class="text-xs font-semibold text-gray-700">bKash</span>
                            </button>
                            @endif
                            @if(in_array('Rocket', $paymentMethods))
                            <button type="button" data-method="Rocket" class="method-btn border rounded-lg p-3 flex-1 flex flex-col items-center focus:outline-none bg-white hover:shadow-md transition">
                                <img src="{{ asset('images/payment/rocket.png') }}" alt="Rocket" class="h-10 mb-1">
                                <span class="text-xs font-semibold text-gray-700">Rocket</span>
                            </button>
                            @endif
                            @if(in_array('Nagad', $paymentMethods))
                            <button type="button" data-method="Nagad" class="method-btn border rounded-lg p-3 flex-1 flex flex-col items-center focus:outline-none bg-white hover:shadow-md transition">
                                <img src="{{ asset('images/payment/nagad.png') }}" alt="Nagad" class="h-10 mb-1">
                                <span class="text-xs font-semibold text-gray-700">Nagad</span>
                            </button>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-gray-700">Repayment Amount</label>
                        <input type="number" id="repayment_amount" name="repayment_amount" class="w-full border border-gray-300 rounded-lg p-2 bg-gray-100 text-gray-700" min="1" step="0.01" required readonly>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition w-full">Pay Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let selectedLoanId = null;
    let selectedAmount = null;
    let selectedMethod = null;

    // Open modal on pay button click
    document.querySelectorAll('.pay-button').forEach(function(btn, idx) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            selectedLoanId = this.getAttribute('data-loan-id');
            selectedAmount = this.getAttribute('data-amount');
            document.getElementById('loan_id').value = selectedLoanId;
            document.getElementById('repayment_amount').value = selectedAmount;
            document.getElementById('summaryAmount').textContent = parseFloat(selectedAmount).toLocaleString('en-BD', {minimumFractionDigits: 2});
            document.getElementById('payment_method').value = '';
            // Remove selection from all method buttons
            document.querySelectorAll('.method-btn').forEach(btn => btn.classList.remove('ring-2', 'ring-blue-500'));
            document.getElementById('paymentModal').classList.remove('hidden');
        });
    });

    // Payment method selection
    document.querySelectorAll('.method-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Remove selection from all
            document.querySelectorAll('.method-btn').forEach(b => b.classList.remove('ring-2', 'ring-blue-500'));
            // Add selection to clicked
            this.classList.add('ring-2', 'ring-blue-500');
            selectedMethod = this.getAttribute('data-method');
            document.getElementById('payment_method').value = selectedMethod;
        });
    });

    // Cancel button
    document.getElementById('cancelPayment').addEventListener('click', function() {
        document.getElementById('paymentModal').classList.add('hidden');
    });

    // Handle form submit
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const loanId = document.getElementById('loan_id').value;
        const paymentMethod = document.getElementById('payment_method').value;
        const repaymentAmount = document.getElementById('repayment_amount').value;
        if (!paymentMethod) {
            Swal.fire({
                icon: 'warning',
                title: 'Select Payment Method',
                text: 'Please select a payment method before proceeding.'
            });
            return;
        }
        fetch("{{ route('group.loan.pay') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                loan_id: loanId,
                payment_method: paymentMethod,
                repayment_amount: repaymentAmount
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Payment Successful!',
                    text: 'Your loan repayment was processed successfully.',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Payment Failed',
                    text: data.message || 'Unknown error occurred.'
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Payment Failed',
                text: err.message
            });
        });
    });
});
</script>
@endsection 