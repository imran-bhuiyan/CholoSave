<?php

namespace App\Http\Controllers;

use App\Models\MyGroup;
use App\Models\LoanRequest;
use App\Models\TransactionInfo;
use App\Models\Withdrawal;
use App\Models\GroupMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\Savings;
use App\Models\Leaderboard;
use App\Models\PaymentOtp as PaymentOtpModel;
use App\Mail\PaymentOtp;
use Illuminate\Support\Facades\DB;

class GroupMemberController extends Controller
{
    public function loanHistory($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $loans = LoanRequest::where('user_id', auth()->id())
            ->where('group_id', $groupId)
            ->whereIn('status', ['approved', 'pending', 'repaid'])
            ->orderBy('created_at', 'desc')
            ->get();

        $paymentMethods = [];
        if (!empty($group->bKash)) $paymentMethods[] = 'bkash';
        if (!empty($group->Rocket)) $paymentMethods[] = 'Rocket';
        if (!empty($group->Nagad)) $paymentMethods[] = 'Nagad';

        return view('groups.member.loan_history', compact('group', 'loans', 'paymentMethods'));
    }

    public function paymentHistory($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $transactions = TransactionInfo::where('user_id', auth()->id())
            ->where('group_id', $groupId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('groups.member.payment_history', compact('group', 'transactions'));
    }

    public function withdrawalHistory($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $withdrawals = Withdrawal::where('group_id', $groupId)
            ->where('user_id', auth()->id())  // Filter by current user
            ->orderBy('created_at', 'desc')
            ->get();

        return view('groups.member.withdrawal_history', compact('group', 'withdrawals'));
    }

    public function createInstallmentPayment($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $user = auth()->user();

        // Generate a transaction ID similar to the sample
        // Using a simple combination for now, you might want a more robust method
        $transactionId = 'CHS' . Str::upper(Str::random(2)) . Str::lower(Str::random(2)) . rand(1000, 9999);

        // We might need to check for remaining payments here or in the next step
        // For now, just pass the data to the view

        return view('groups.member.initiate_installment_payment', compact('group', 'user', 'transactionId'));
    }

    public function initiateInstallmentPayment(Request $request, $groupId)
    {
        $user = auth()->user();
        $group = MyGroup::findOrFail($groupId);
        $amount = $group->amount;
        $selectedMethod = $request->input('payment_method');
        $transactionId = 'TRX' . time() . rand(1000, 9999);

        // Generate OTP
        $otp = rand(100000, 999999);
        $otpExpiry = now()->addMinutes(2);

        // Store OTP in database
        PaymentOtpModel::create([
            'user_id' => $user->id,
            'group_id' => $groupId,
            'otp' => $otp,
            'otp_expiry' => $otpExpiry,
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'payment_method' => $selectedMethod
        ]);

        // Send OTP via email
        try {
            Mail::to($user->email)->send(new PaymentOtp($otp, $amount, $group->group_name));
        } catch (\Exception $e) {
            // Log the error but don't stop the process
            \Log::error('Failed to send payment OTP email: ' . $e->getMessage());
        }

        return redirect()->route('member.installment.payment.verify-otp', [
            'groupId' => $groupId,
            'transactionId' => $transactionId
        ]);
    }

    public function showInstallmentVerifyOtpForm($groupId, $transactionId)
    {
        $group = MyGroup::findOrFail($groupId);
        $user = auth()->user();
        
        // Check if there's a valid OTP in the database
        $paymentOtp = PaymentOtpModel::where('user_id', $user->id)
            ->where('group_id', $groupId)
            ->where('transaction_id', $transactionId)
            ->where('otp_expiry', '>', now())
            ->first();

        if (!$paymentOtp) {
            return redirect()->route('member.installment.payment.create', $groupId)
                ->with('error', 'OTP expired or not generated. Please try again.');
        }

        return view('groups.member.verify_installment_otp', compact('groupId', 'transactionId', 'group'));
    }

    public function verifyInstallmentOtp(Request $request, $groupId, $transactionId)
    {
        $user = auth()->user();
        $enteredOtp = $request->input('otp');

        // Get the OTP record from database
        $paymentOtp = PaymentOtpModel::where('user_id', $user->id)
            ->where('group_id', $groupId)
            ->where('transaction_id', $transactionId)
            ->where('otp_expiry', '>', now())
            ->first();

        if (!$paymentOtp || $paymentOtp->otp !== $enteredOtp) {
            return redirect()->back()->with('error', 'Invalid or expired OTP. Please try again.');
        }

        // Start database transaction
        DB::beginTransaction();
        try {
            // Create transaction record
            TransactionInfo::create([
                'user_id' => $user->id,
                'group_id' => $groupId,
                'amount' => $paymentOtp->amount,
                'transaction_id' => $transactionId,
                'payment_method' => $paymentOtp->payment_method,
                'payment_time' => now(),
            ]);

            // Create savings record
            Savings::create([
                'user_id' => $user->id,
                'group_id' => $groupId,
                'amount' => $paymentOtp->amount,
            ]);

            // Update group membership
            $membership = GroupMembership::where('user_id', $user->id)
                ->where('group_id', $groupId)
                ->where('status', 'approved')
                ->firstOrFail();
            $membership->decrement('time_period_remaining');

            // Delete the used OTP
            $paymentOtp->delete();

            DB::commit();

            return redirect()->route('member.installment.payment.success', [
                'groupId' => $groupId,
                'transactionId' => $transactionId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment processing failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    public function showInstallmentPaymentSuccess($groupId, $transactionId)
    {
        // Fetch the group
        $group = MyGroup::findOrFail($groupId);

        // Fetch the transaction details using the transactionId
        $transaction = TransactionInfo::where('transaction_id', $transactionId)
            ->where('user_id', auth()->id())
            ->where('group_id', $groupId)
            ->first();

        // Pass groupId, group, and transaction to the view
        return view('groups.member.payment_success', compact('groupId', 'group', 'transaction'));
    }

    public function groupNotifications($groupId)
    {
        // Fetch the group
        $group = \App\Models\MyGroup::findOrFail($groupId);

        // Fetch group notifications
        $notifications = \App\Models\Notification::where('target_group_id', $groupId)
                               ->where('status', 'unread')
                               ->orderBy('created_at', 'desc')
                               ->get();

        // Pass notifications and group ID to the view
        return view('groups.member.group_notifications', compact('notifications', 'group', 'groupId'));
    }
} 