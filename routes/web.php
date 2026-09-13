<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExpertController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\LoanRequestController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\AiTipsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\GroupMemberController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\SiteAdminController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ChatController;

// Main Pages
Route::get('/', function () {
    if (auth()->check()) {
        return view('welcome', ['user' => auth()->user()]);
    }
    return view('welcome');
})->name('home');

Route::get('/vision', function () {
    return view('vision');
})->name('vision');

Route::get('/experts', [ExpertController::class, 'index'])->name('experts');

// Contact Routes
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Registration Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated User Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
    
    // AI Tips Route
    Route::get('/ai-tips', [AiTipsController::class, 'show'])->name('ai.tips');
    
    // Group Routes
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/my-groups', [GroupController::class, 'myGroups'])->name('groups.my');
    Route::get('/join_groups', [GroupController::class, 'joinGroups'])->name('groups.join');
    Route::get('/groups/leaderboard', [GroupController::class, 'leaderboardPage'])->name('groups.leaderboard');
    
    // Group-specific routes
    Route::prefix('groups/{groupId}')->group(function () {
        Route::get('/', [GroupController::class, 'show'])->name('groups.show');
        Route::post('/join', [GroupController::class, 'joinGroup'])->name('groups.join.request');
        Route::get('/enter', [GroupController::class, 'enterGroup'])->name('groups.enter');
        
        // Admin routes
        Route::middleware([\App\Http\Middleware\GroupAdminMiddleware::class])->group(function () {
            Route::get('/admin-dashboard', [GroupController::class, 'adminDashboard'])->name('groups.admin.dashboard');
            Route::get('/admin/notifications', [GroupController::class, 'adminNotifications'])->name('groups.admin.notifications');
            Route::post('/admin/notifications/{notificationId}/mark-read', [GroupController::class, 'markNotificationAsRead'])->name('groups.admin.notifications.mark-read');
            Route::delete('/admin/notifications/clear-all', [GroupController::class, 'clearAllNotifications'])->name('groups.admin.notifications.clear-all');
            Route::get('/admin/settings', [GroupController::class, 'adminSettings'])->name('groups.admin.settings');
            Route::put('/admin/settings', [GroupController::class, 'updateSettings'])->name('groups.admin.settings.update');
            Route::post('/admin/close-savings', [GroupController::class, 'closeSavings'])->name('groups.admin.close-savings');
            Route::get('/loan-request', [LoanRequestController::class, 'create'])->name('admin.loan.request.create');
            Route::post('/loan-request', [LoanRequestController::class, 'store'])->name('admin.loan.request.store');
            Route::get('/admin/members', [GroupController::class, 'adminMembers'])->name('groups.admin.members');
            
            // Admin Payment Routes
            Route::get('/admin/installment-payment', [GroupController::class, 'createAdminInstallmentPayment'])->name('admin.installment.payment.create');
            Route::post('/admin/installment-payment/initiate', [GroupController::class, 'initiateAdminInstallmentPayment'])->name('admin.installment.payment.initiate');
            Route::get('/admin/installment-payment/verify-otp/{transactionId}', [GroupController::class, 'showAdminInstallmentVerifyOtpForm'])->name('admin.installment.payment.verify-otp');
            Route::post('/admin/installment-payment/verify-otp/{transactionId}', [GroupController::class, 'verifyAdminInstallmentOtp'])->name('admin.installment.payment.verify-otp.post');
            Route::get('/admin/installment-payment/success/{transactionId}', [GroupController::class, 'showAdminInstallmentPaymentSuccess'])->name('admin.installment.payment.success');
            Route::get('/admin/payment-history', [GroupController::class, 'adminPaymentHistory'])->name('admin.payment.history');
            Route::get('/admin/member-payment', [GroupController::class, 'adminMemberPayment'])->name('admin.member.payment');
            Route::get('/admin/member-payment/export', [GroupController::class, 'exportMemberPayment'])->name('admin.member.payment.export');
            Route::post('/admin/transfer-admin', [GroupController::class, 'transferAdmin'])->name('groups.admin.transfer-admin');
            Route::get('/admin/leave-request', [GroupController::class, 'showAdminLeaveRequestForm'])->name('groups.admin.leave-request.form');
            Route::post('/admin/leave-request', [GroupController::class, 'adminLeaveRequest'])->name('groups.admin.leave-request');
            // Member Leave Requests Management
            Route::get('/admin/member-leave-requests', [GroupController::class, 'memberLeaveRequests'])->name('groups.admin.member-leave-requests');
            Route::post('/admin/member-leave-requests/{membership}/approve', [GroupController::class, 'approveMemberLeaveRequest'])->name('groups.admin.member-leave-requests.approve');
            Route::post('/admin/member-leave-requests/{membership}/reject', [GroupController::class, 'rejectMemberLeaveRequest'])->name('groups.admin.member-leave-requests.reject');
        });
        
        // Member routes
        Route::get('/member/dashboard', [GroupController::class, 'memberDashboard'])->name('groups.member.dashboard');
        Route::get('/member/investment-details', [GroupController::class, 'investmentDetails'])->name('groups.member.investment-details');
        Route::get('/member/investment-details/export', [GroupController::class, 'exportInvestmentDetails'])->name('groups.member.investment-details.export');
        Route::get('/member/loan-request', [LoanRequestController::class, 'create'])->name('member.loan.request.create');
        Route::post('/member/loan-request', [LoanRequestController::class, 'store'])->name('member.loan.request.store');
        Route::get('/member/withdrawal-request', [WithdrawalController::class, 'create'])->name('member.withdrawal.request.create');
        Route::post('/member/withdrawal-request', [WithdrawalController::class, 'store'])->name('member.withdrawal.request.store');
        Route::get('/member/report', [ReportController::class, 'generateReport'])->name('member.report.generate');
        Route::get('/member/payment-history', [GroupMemberController::class, 'paymentHistory'])->name('member.payment.history');
        Route::get('/member/installment-payment', [GroupMemberController::class, 'createInstallmentPayment'])->name('member.installment.payment.create');
        Route::post('/member/installment-payment/initiate', [GroupMemberController::class, 'initiateInstallmentPayment'])->name('member.installment.payment.initiate');

        // Installment Payment OTP Verification Routes
        Route::get('/member/installment-payment/verify-otp/{transactionId}', [GroupMemberController::class, 'showInstallmentVerifyOtpForm'])->name('member.installment.payment.verify-otp');
        Route::post('/member/installment-payment/verify-otp/{transactionId}', [GroupMemberController::class, 'verifyInstallmentOtp'])->name('member.installment.payment.verify-otp.post');

        // Installment Payment Success Route
        Route::get('/member/installment-payment/success/{transactionId}', [GroupMemberController::class, 'showInstallmentPaymentSuccess'])->name('member.installment.payment.success');

        // Group Notifications Route
        Route::get('/member/notifications', [GroupMemberController::class, 'groupNotifications'])->name('member.group.notifications');

        // Member Leave Request Routes
        Route::get('/member/leave-request', [GroupController::class, 'showMemberLeaveRequestForm'])->name('groups.member.leave-request.form');
        Route::post('/member/leave-request', [GroupController::class, 'memberLeaveRequest'])->name('groups.member.leave-request');
    });
    
    // Withdrawal History Route
    Route::get('/member/{groupId}/withdrawal-history', [GroupMemberController::class, 'withdrawalHistory'])->name('member.withdrawal.history');
    
    // Loan History Route
    Route::get('/member/{groupId}/loan-history', [GroupMemberController::class, 'loanHistory'])->name('member.loan.history');
    
    // Investments Routes
    Route::get('/investments', [InvestmentController::class, 'index'])->name('investments.index');
    Route::get('/investments/create', [InvestmentController::class, 'create'])->name('investments.create');
    Route::post('/investments', [InvestmentController::class, 'store'])->name('investments.store');
    Route::get('/investments/{investment}', [InvestmentController::class, 'show'])->name('investments.show');

    // Group Members Route
    Route::get('/groups/{group}/members', [GroupController::class, 'members'])->name('groups.members');

    // Investment Routes
    Route::get('/groups/{group}/admin/investment/create', [InvestmentController::class, 'create'])->name('admin.investment.create');
    Route::post('/groups/{group}/admin/investment/store', [InvestmentController::class, 'store'])->name('admin.investment.store');
    Route::get('/groups/{group}/admin/investment/return/create', [InvestmentController::class, 'createReturn'])->name('admin.investment.return.create');
    Route::post('/groups/{group}/admin/investment/return/store', [InvestmentController::class, 'storeReturn'])->name('admin.investment.return.store');
    Route::get('/groups/{group}/admin/investment/history', [InvestmentController::class, 'history'])->name('admin.investment.history');

    // Member Loans Management Routes
    Route::get('/groups/{group}/admin/loans', [GroupController::class, 'memberLoans'])->name('admin.member.loans');
    Route::post('/groups/{group}/admin/loans/{loan}/approve', [GroupController::class, 'approveLoan'])->name('admin.loans.approve');
    Route::post('/groups/{group}/admin/loans/{loan}/decline', [GroupController::class, 'declineLoan'])->name('admin.loans.decline');

    // Join Request Routes
    Route::get('/groups/{group}/join-requests', [GroupController::class, 'joinRequests'])->name('groups.admin.join-requests');
    Route::put('/groups/{group}/join-requests/{request}/approve', [GroupController::class, 'approveJoinRequest'])->name('groups.admin.join-requests.approve');
    Route::put('/groups/{group}/join-requests/{request}/reject', [GroupController::class, 'rejectJoinRequest'])->name('groups.admin.join-requests.reject');

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);

    // Reminders Route
    Route::get('/reminders', [ReminderController::class, 'index'])->name('reminders');

    // Site Admin Dashboard Route
    Route::get('/site-admin/dashboard', [SiteAdminController::class, 'index'])->name('site_admin.dashboard');

    // Forum Routes
    Route::prefix('forum')->group(function () {
        Route::get('/', [ForumController::class, 'index'])->name('forum.index');
        Route::get('/ask', [ForumController::class, 'create'])->name('forum.ask');
        Route::post('/ask', [ForumController::class, 'store'])->name('forum.store');
        Route::get('/my-questions', [ForumController::class, 'myQuestions'])->name('forum.my_questions');
        Route::get('/{id}', [ForumController::class, 'show'])->name('forum.show');
        Route::post('/{id}/reply', [ForumController::class, 'reply'])->name('forum.reply');
    });

    // Campaign Routes
    Route::get('/admin/campaign/create', [CampaignController::class, 'create'])->name('admin.campaign.create');
    Route::post('/admin/campaign/store', [CampaignController::class, 'store'])->name('admin.campaign.store');
    Route::get('/admin/campaign/manage', [CampaignController::class, 'index'])->name('admin.campaign.manage');
    Route::get('/admin/campaign/edit/{id}', [CampaignController::class, 'edit'])->name('admin.campaign.edit');
    Route::patch('/admin/campaign/update/{id}', [CampaignController::class, 'update'])->name('admin.campaign.update');

    // Chat routes
    Route::get('/groups/{groupId}/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/groups/{groupId}/chat', [ChatController::class, 'store'])->name('chat.store');
});

// Admin Loan Routes
Route::prefix('admin/loan')->name('admin.loan.')->group(function () {
    Route::get('/request/{group}', [LoanRequestController::class, 'create'])->name('request.create');
    Route::post('/request/{group}', [LoanRequestController::class, 'store'])->name('request.store');
    Route::get('/history/{group}', [LoanRequestController::class, 'adminLoanHistory'])->name('history');
});

// Admin Withdrawal Routes
Route::prefix('admin/withdrawal')->name('admin.withdrawal.')->group(function () {
    Route::get('/request/{group}', [WithdrawalController::class, 'adminCreate'])->name('request.create');
    Route::post('/request/{group}', [WithdrawalController::class, 'adminStore'])->name('request.store');
    Route::get('/history/{group}', [WithdrawalController::class, 'adminPersonalWithdrawalHistory'])->name('history');
    Route::get('/requests/{group}', [WithdrawalController::class, 'adminRequests'])->name('requests');
    Route::post('/requests/{withdrawal}/approve', [WithdrawalController::class, 'approveWithdrawal'])->name('requests.approve');
    Route::post('/requests/{withdrawal}/reject', [WithdrawalController::class, 'rejectWithdrawal'])->name('requests.reject');
});

// Member Withdrawal Routes
Route::prefix('member/withdrawal')->name('member.withdrawal.')->group(function () {
    Route::get('/request/{group}', [WithdrawalController::class, 'create'])->name('request.create');
    Route::post('/request/{group}', [WithdrawalController::class, 'store'])->name('request.store');
    Route::get('/history/{group}', [WithdrawalController::class, 'withdrawalHistory'])->name('history');
});

// Admin Poll Routes
Route::prefix('admin/poll')->name('admin.poll.')->group(function () {
    Route::get('/create/{group}', [PollController::class, 'create'])->name('create');
    Route::post('/store/{group}', [PollController::class, 'store'])->name('store');
    Route::get('/list/{group}', [PollController::class, 'list'])->name('list');
    Route::post('/update/{poll}', [PollController::class, 'update'])->name('update');
    Route::delete('/delete/{poll}', [PollController::class, 'delete'])->name('delete');
    Route::post('/vote/{poll}', [PollController::class, 'vote'])->name('vote');
});

// Member Poll Routes
Route::prefix('member/poll')->name('member.poll.')->group(function () {
    Route::post('/vote/{poll}', [PollController::class, 'vote'])->name('vote');
});

// Admin Withdrawal Routes
Route::get('/groups/{groupId}/admin/withdrawals', [WithdrawalController::class, 'adminWithdrawalHistory'])->name('admin.withdrawals.index');
Route::post('/groups/{groupId}/admin/withdrawals/{withdrawal_id}/approve', [WithdrawalController::class, 'approveWithdrawal'])->name('admin.withdrawals.approve');
Route::post('/groups/{groupId}/admin/withdrawals/{withdrawal_id}/decline', [WithdrawalController::class, 'declineWithdrawal'])->name('admin.withdrawals.decline');

Route::post('/group/loan/pay', [GroupController::class, 'payLoan'])->name('group.loan.pay');

// Admin Expert Team Routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/add-expert', [App\Http\Controllers\ExpertController::class, 'create'])->name('admin.expert.create');
    Route::post('/add-expert', [App\Http\Controllers\ExpertController::class, 'store'])->name('admin.expert.store');
    Route::get('/manage-experts', [App\Http\Controllers\ExpertController::class, 'manage'])->name('admin.expert.manage');
    Route::delete('/delete-expert/{id}', [App\Http\Controllers\ExpertController::class, 'destroy'])->name('admin.expert.delete');
    Route::post('/edit-expert/{id}', [App\Http\Controllers\ExpertController::class, 'update'])->name('admin.expert.update');
    Route::get('/report', [App\Http\Controllers\SiteAdminController::class, 'report'])->name('admin.report');
    Route::post('/contact/{id}/mark-done', [App\Http\Controllers\ContactController::class, 'markAsDone'])->name('admin.contact.mark-done');
});

// Fetch campaign contributions for modal
Route::get('/campaign/{id}/contributions', [App\Http\Controllers\CampaignController::class, 'contributions']);

// Store a campaign contribution
Route::post('/campaign/{id}/contribute', [App\Http\Controllers\CampaignController::class, 'storeContribution'])->middleware('auth');

// Download contribution receipt
Route::get('/contribution/{id}/receipt', [App\Http\Controllers\CampaignController::class, 'downloadReceipt'])->middleware('auth');