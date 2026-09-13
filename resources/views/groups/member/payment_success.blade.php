@extends('layouts.group_member')

@section('title', 'Payment Successful')

@push('styles')
<style>
    @keyframes slideDown {
      from { transform: translateY(-20px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    @keyframes checkmark {
      from { transform: scale(0); }
      to { transform: scale(1); }
    }
    .animate-slide-down {
      animation: slideDown 0.6s ease-out forwards;
    }
    .animate-checkmark {
      animation: checkmark 0.5s ease-out forwards;
      animation-delay: 0.3s;
      transform: scale(0);
    }
     .success-background {
         background-image: url('{{ asset('images/payment/american.jpg') }}');
         background-size: cover;
         background-position: center;
    }
</style>
@endpush

@section('content')
<div class="success-background min-h-screen flex items-center justify-center p-4">
  <div class="text-center p-8 rounded-2xl shadow-2xl bg-white w-full max-w-md animate-slide-down">
    <!-- Success Icon -->
    <div class="mb-6">
      <div class="mx-auto h-20 w-20 rounded-full bg-green-100 flex items-center justify-center">
        <i class="fas fa-check text-4xl text-green-500 animate-checkmark"></i>
      </div>
    </div>

    <h2 class="text-3xl font-bold text-green-600 mb-4">Payment Successful!</h2>
    <p class="text-xl text-gray-600 mb-6">Your payment to group "{{ $group->group_name }}" has been processed securely.</p>

    <!-- Transaction Details -->
    @if($transaction)
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
      <div class="flex justify-between items-center mb-2">
        <span class="text-gray-600">Transaction ID:</span>
        <span class="text-gray-800 font-medium">{{ $transaction->transaction_id }}</span>
      </div>
      <div class="flex justify-between items-center mb-2">
        <span class="text-gray-600">Date:</span>
        <span class="text-gray-800 font-medium">{{ $transaction->payment_time ? \Carbon\Carbon::parse($transaction->payment_time)->format('M d, Y H:i') : 'N/A' }}</span>
      </div>
      <div class="border-t border-gray-200 my-2"></div>
      <div class="flex justify-between items-center">
        <span class="text-gray-600">Payment Method:</span>
        <span class="text-gray-800 font-medium">
          <i class="fas fa-credit-card mr-2"></i>
          {{ $transaction->payment_method ?? 'N/A' }}
        </span>
      </div>
      <div class="flex justify-between items-center mt-2">
        <span class="text-gray-600">Amount:</span>
        <span class="text-gray-800 font-medium">৳{{ number_format($transaction->amount ?? 0, 2) }}</span>
      </div>
    </div>
     @else
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
           Transaction details not available.
        </div>
    @endif

   {{-- Receipt Button (Optional) --}}
{{-- <a href="{{ route('member.installment.payment.receipt', ['groupId' => $groupId, 'transactionId' => $transactionId]) }}"
   class="w-full mb-4 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg text-lg transition duration-300 flex items-center justify-center">
  <i class="fas fa-download mr-2"></i>
  Download Receipt
</a> --}}

    <!-- Home Button -->
    <a href="{{ route('groups.member.dashboard', $groupId) }}"
       class="w-full block bg-green-600 text-white px-6 py-3 rounded-lg text-lg hover:bg-green-500 transition duration-300 flex items-center justify-center">
      <i class="fas fa-home mr-2"></i>
      Return to Dashboard
    </a>

    <!-- Support Link -->
    <p class="mt-6 text-sm text-gray-500">
      Need help? <a href="#" class="text-green-600 hover:text-green-500">Contact support</a>
    </p>
  </div>
</div>
@endsection 