@extends('layouts.group_admin')

@section('title', 'Admin - Initiate Installment Payment')

@push('styles')
<style>
    .payment-background {
        background-image: url('{{ asset('images/payment/american.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
</style>
@endpush

@section('content')
    <!-- Support Header -->
    <div class="bg-gray-700/80 text-white p-2 text-right text-sm" style="background-color: rgba(55, 65, 81, 0.8); color: white;">
        Having Problems? Call Support: +880 9612 22 1000
    </div>

    <div class="container mx-auto p-4 md:p-8 max-w-6xl payment-background">
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Order Summary Card -->
            <div class="bg-white rounded shadow-sm mt-8" style="background-color: white;">
                <div class="bg-blue-700 text-white p-4 rounded-t flex justify-between items-center" style="background-color: #1d4ed8; color: white;">
                    <h2 class="text-xl">Admin Payment Summary</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-2 text-gray-600" style="color: #4b5563;">
                        <div>Admin Name:</div>
                        <div style="color: #111;">{{ $user->name ?? 'N/A' }}</div>
                        <div>Group:</div>
                        <div style="color: #111;">{{ $group->group_name ?? 'N/A' }}</div>
                        <div>Transaction ID:</div>
                        <div style="color: #111;">{{ $transactionId ?? 'N/A' }}</div>
                        <div>Total (BDT):</div>
                        <div class="text-2xl font-bold text-gray-800" style="color: #1f2937;">{{ number_format($group->amount ?? 0, 2) }}</div>
                    </div>
                    <div class="pt-4 text-sm text-red-500" style="color: #ef4444;">
                        <a href="{{ route('groups.admin.dashboard', $group->group_id) }}" class="hover:underline" style="color: #ef4444;">Cancel Payment & return to Dashboard</a>
                    </div>
                </div>
            </div>

            <!-- Payment Methods Card -->
            <div x-data="{ selectedMethod: '' }" class="bg-white rounded shadow-sm mt-8" style="background-color: white;">
                <div class="bg-blue-700 text-white p-4 rounded-t flex justify-between items-center" style="background-color: #1d4ed8; color: white;">
                    <h2 class="text-xl">Select Payment Method</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <h3 class="text-gray-500 font-medium" style="color: #6b7280;">Mobile Banking</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @if(!empty($group->bKash))
                            <button type="button" @click="selectedMethod = 'bKash'" :class="{ 'ring-2 ring-blue-500': selectedMethod === 'bKash' }"
                                class="p-4 border rounded hover:shadow-md transition-all duration-200 focus:outline-none" style="background-color: white; border-color: #e5e7eb;">
                                <img src="{{ asset('images/payment/bkash.png') }}" alt="bKash" class="w-full h-12 object-contain">
                            </button>
                            @endif
                            @if(!empty($group->Rocket))
                            <button type="button" @click="selectedMethod = 'Rocket'" :class="{ 'ring-2 ring-blue-500': selectedMethod === 'Rocket' }"
                                class="p-4 border rounded hover:shadow-md transition-all duration-200 focus:outline-none" style="background-color: white; border-color: #e5e7eb;">
                                <img src="{{ asset('images/payment/rocket.png') }}" alt="Rocket" class="w-full h-12 object-contain">
                            </button>
                            @endif
                            @if(!empty($group->Nagad))
                            <button type="button" @click="selectedMethod = 'Nagad'" :class="{ 'ring-2 ring-blue-500': selectedMethod === 'Nagad' }"
                                class="p-4 border rounded hover:shadow-md transition-all duration-200 focus:outline-none" style="background-color: white; border-color: #e5e7eb;">
                                <img src="{{ asset('images/payment/nagad.png') }}" alt="Nagad" class="w-full h-12 object-contain">
                            </button>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8">
                        <!-- Loading Overlay -->
                        <div id="loadingOverlay"
                            class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
                            <div class="bg-white p-6 rounded-lg shadow-xl flex flex-col items-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
                                <p class="mt-4 text-gray-700 font-medium">Processing payment...</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.installment.payment.initiate', $group->group_id) }}" id="paymentForm" onsubmit="handleSubmit(event)">
                            @csrf
                            <input type="hidden" name="transaction_id" value="{{ $transactionId ?? '' }}">
                            <input type="hidden" name="payment_method" x-model="selectedMethod">
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                            <input type="hidden" name="group_id" value="{{ $group->group_id }}">
                            <input type="hidden" name="amount" value="{{ $group->amount ?? 0 }}">
                            <button type="submit"
                                :class="{ 'bg-[#1d4ed8] hover:bg-[#1e40af] text-white': selectedMethod, 'bg-gray-300 cursor-not-allowed text-gray-500': !selectedMethod }"
                                :disabled="!selectedMethod"
                                class="w-full py-3 rounded font-medium transition-colors duration-200">
                                Pay Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Powered by CholoSave Logo -->
    <div class="fixed bottom-4 right-4 ">
        <p class="text-gray-700" style="color: #4b5563;">Powered by CholoSave</p>
    </div>

    @push('scripts')
    <script>
        function handleSubmit(event) {
            event.preventDefault();

            // Show loading overlay
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.classList.remove('hidden');

            // Disable the submit button
            const submitButton = event.target.querySelector('button');
            submitButton.disabled = true;

            // Submit the form after a delay to show the loading animation
            setTimeout(() => {
               document.getElementById('paymentForm').submit();
            }, 100);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush

@endsection 