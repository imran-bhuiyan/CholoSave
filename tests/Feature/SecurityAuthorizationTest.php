<?php

use App\Models\GroupMembership;
use App\Models\LoanRequest;
use App\Models\Message;
use App\Models\MyGroup;
use App\Models\Poll;
use App\Models\TransactionInfo;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function secMakeUser(array $attributes = []): User
{
    return User::factory()->create(array_merge(['role' => 'user'], $attributes));
}

function secMakeGroup(User $admin, float $emergencyFund = 100.0): MyGroup
{
    return MyGroup::create([
        'group_name' => 'Test Group',
        'description' => 'A group created for security tests',
        'members' => 2,
        'group_admin_id' => $admin->id,
        'dps_type' => 'monthly',
        'time_period' => 12,
        'amount' => 500,
        'goal_amount' => 6000,
        'emergency_fund' => $emergencyFund,
    ]);
}

function secAddMembership(MyGroup $group, User $user, bool $isAdmin = false, string $status = 'approved'): GroupMembership
{
    return GroupMembership::create([
        'group_id' => $group->group_id,
        'user_id' => $user->id,
        'status' => $status,
        'is_admin' => $isAdmin,
    ]);
}

test('guests are redirected from financial, poll, and site-admin endpoints', function () {
    $this->post('/group/loan/pay')->assertRedirect();
    $this->post('/groups/1/admin/withdrawals/1/approve')->assertRedirect();
    $this->post('/groups/1/admin/withdrawals/1/decline')->assertRedirect();
    $this->post('/member/withdrawal/request/1')->assertRedirect();
    $this->post('/admin/withdrawal/request/1')->assertRedirect();
    $this->post('/admin/loan/request/1')->assertRedirect();
    $this->post('/member/poll/vote/1')->assertRedirect();
    $this->post('/admin/poll/store/1')->assertRedirect();
    $this->get('/site-admin/dashboard')->assertRedirect();
});

test('only the group admin can approve or decline withdrawals', function () {
    $admin = secMakeUser();
    $member = secMakeUser();
    $group = secMakeGroup($admin);
    secAddMembership($group, $admin, true);
    secAddMembership($group, $member);

    $withdrawal = Withdrawal::create([
        'user_id' => $member->id,
        'group_id' => $group->group_id,
        'amount' => 50,
        'payment_number' => '0123456789',
        'payment_method' => 'Bkash',
        'status' => 'pending',
    ]);

    // A non-admin member of the group is rejected
    $this->actingAs($member)
        ->post("/groups/{$group->group_id}/admin/withdrawals/{$withdrawal->withdrawal_id}/approve")
        ->assertStatus(403);
    expect($withdrawal->fresh()->status)->toBe('pending');

    // The group admin can approve
    $this->actingAs($admin)
        ->post("/groups/{$group->group_id}/admin/withdrawals/{$withdrawal->withdrawal_id}/approve")
        ->assertOk()
        ->assertJson(['success' => true]);
    expect($withdrawal->fresh()->status)->toBe('approved');

    // A second approval attempt is rejected
    $this->actingAs($admin)
        ->post("/groups/{$group->group_id}/admin/withdrawals/{$withdrawal->withdrawal_id}/approve")
        ->assertStatus(400);
});

test('loan repayment is limited to the borrower or group admin and capped at the loan amount', function () {
    $admin = secMakeUser();
    $borrower = secMakeUser();
    $outsider = secMakeUser();
    $group = secMakeGroup($admin, 100.0);
    secAddMembership($group, $admin, true);
    secAddMembership($group, $borrower);

    $loan = LoanRequest::create([
        'user_id' => $borrower->id,
        'group_id' => $group->group_id,
        'reason' => 'Emergency medical expense',
        'amount' => 50,
        'status' => 'approved',
        'return_time' => now()->addMonth()->toDateString(),
        'repayment_amount' => 0,
    ]);

    // An unrelated user cannot repay someone else's loan
    $this->actingAs($outsider)
        ->post('/group/loan/pay', [
            'loan_id' => $loan->id,
            'payment_method' => 'bkash',
            'repayment_amount' => 50,
        ])->assertStatus(403);

    // The borrower's overpayment is capped at the outstanding loan amount
    $this->actingAs($borrower)
        ->post('/group/loan/pay', [
            'loan_id' => $loan->id,
            'payment_method' => 'bkash',
            'repayment_amount' => 500,
        ])->assertOk()
        ->assertJson(['success' => true]);

    $loan->refresh();
    expect((float) $loan->repayment_amount)->toBe(50.0)
        ->and($loan->status)->toBe('repaid');

    // The emergency fund only grows by the capped amount (100 + 50)
    $group->refresh();
    expect((float) $group->emergency_fund)->toBe(150.0);

    // A second repayment of the same loan is rejected
    $this->actingAs($borrower)
        ->post('/group/loan/pay', [
            'loan_id' => $loan->id,
            'payment_method' => 'bkash',
            'repayment_amount' => 50,
        ])->assertJson(['success' => false]);
});

test('poll management requires the group admin', function () {
    $admin = secMakeUser();
    $member = secMakeUser();
    $group = secMakeGroup($admin);
    secAddMembership($group, $admin, true);
    secAddMembership($group, $member);

    // A plain member cannot create polls
    $this->actingAs($member)
        ->post("/admin/poll/store/{$group->group_id}", ['question' => 'Buy stocks?'])
        ->assertStatus(403);
    expect(Poll::count())->toBe(0);

    // The group admin can
    $this->actingAs($admin)
        ->post("/admin/poll/store/{$group->group_id}", ['question' => 'Buy stocks?'])
        ->assertOk()
        ->assertJson(['success' => true]);
    expect(Poll::count())->toBe(1);

    $poll = Poll::first();

    // A plain member cannot update or delete polls
    $this->actingAs($member)
        ->post("/admin/poll/update/{$poll->poll_id}", ['poll_question' => 'Hacked', 'status' => 'closed'])
        ->assertStatus(403);
    $this->actingAs($member)
        ->delete("/admin/poll/delete/{$poll->poll_id}")
        ->assertStatus(403);
    expect($poll->fresh()->poll_question)->toBe('Buy stocks?');
});

test('poll voting requires group membership and is limited to one vote per user', function () {
    $admin = secMakeUser();
    $member = secMakeUser();
    $outsider = secMakeUser();
    $group = secMakeGroup($admin);
    secAddMembership($group, $admin, true);
    secAddMembership($group, $member);

    $poll = Poll::create([
        'group_id' => $group->group_id,
        'poll_question' => 'Approve the loan?',
        'status' => 'active',
    ]);

    // Non-members cannot vote
    $this->actingAs($outsider)
        ->post("/member/poll/vote/{$poll->poll_id}", ['vote_option' => 'yes'])
        ->assertStatus(403);
    expect($poll->votes()->count())->toBe(0);

    // A member votes
    $this->actingAs($member)
        ->post("/member/poll/vote/{$poll->poll_id}", ['vote_option' => 'yes'])
        ->assertOk()
        ->assertJson(['success' => true]);
    expect($poll->votes()->count())->toBe(1);

    // Changing the vote updates the existing row instead of adding one
    $this->actingAs($member)
        ->post("/member/poll/vote/{$poll->poll_id}", ['vote_option' => 'no'])
        ->assertOk();
    expect($poll->votes()->count())->toBe(1)
        ->and($poll->votes()->first()->vote_option)->toBe('no');
});

test('join request approval is scoped to the group in the route', function () {
    $adminA = secMakeUser();
    $adminB = secMakeUser();
    $applicant = secMakeUser();
    $groupA = secMakeGroup($adminA);
    $groupB = secMakeGroup($adminB);
    secAddMembership($groupA, $adminA, true);
    secAddMembership($groupB, $adminB, true);

    $pendingRequest = secAddMembership($groupB, $applicant, false, 'pending');

    // Admin of group A cannot approve group B's join request
    $this->actingAs($adminA)
        ->put("/groups/{$groupA->group_id}/join-requests/{$pendingRequest->membership_id}/approve")
        ->assertStatus(404);
    expect($pendingRequest->fresh()->status)->toBe('pending');

    // Admin of group B approves their own group's request
    $this->actingAs($adminB)
        ->put("/groups/{$groupB->group_id}/join-requests/{$pendingRequest->membership_id}/approve")
        ->assertOk()
        ->assertJson(['success' => true]);
    expect($pendingRequest->fresh()->status)->toBe('approved');
});

test('the site admin dashboard is restricted to users with the admin role', function () {
    $user = secMakeUser();
    $admin = secMakeUser(['role' => 'admin']);

    $this->actingAs($user)->get('/site-admin/dashboard')->assertStatus(403);
    $this->actingAs($admin)->get('/site-admin/dashboard')->assertOk();
});

test('chat messages can only be posted by group members', function () {
    $admin = secMakeUser();
    $member = secMakeUser();
    $outsider = secMakeUser();
    $group = secMakeGroup($admin);
    secAddMembership($group, $admin, true);
    secAddMembership($group, $member);

    // Non-members cannot post
    $this->actingAs($outsider)
        ->post("/groups/{$group->group_id}/chat", [
            'group_id' => $group->group_id,
            'message' => 'hello',
        ])->assertStatus(403);
    expect(Message::count())->toBe(0);

    // Members can post
    $this->actingAs($member)
        ->post("/groups/{$group->group_id}/chat", [
            'group_id' => $group->group_id,
            'message' => 'hello',
        ])->assertOk()
        ->assertJson(['success' => true]);
    expect(Message::count())->toBe(1);
});

test('transaction_info rejects duplicate transaction ids', function () {
    $user = secMakeUser();
    $group = secMakeGroup($user);

    TransactionInfo::create([
        'user_id' => $user->id,
        'group_id' => $group->group_id,
        'amount' => 100,
        'transaction_id' => 'TRXDUPLICATE',
        'payment_method' => 'bKash',
        'payment_time' => now(),
    ]);

    $this->expectException(UniqueConstraintViolationException::class);

    TransactionInfo::create([
        'user_id' => $user->id,
        'group_id' => $group->group_id,
        'amount' => 100,
        'transaction_id' => 'TRXDUPLICATE',
        'payment_method' => 'bKash',
        'payment_time' => now(),
    ]);
});
