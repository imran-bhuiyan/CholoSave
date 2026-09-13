@extends('layouts.group_admin')

@section('title', 'Member Leave Requests')

@section('content')
<div class="max-w-6xl mx-auto p-6 animate-fade-in">
    <div class="mb-8 text-center">
        <h2 class="text-3xl font-semibold custom-font text-black mb-2">
            <i class="fa-solid fa-user-minus mr-2 text-red-600"></i>
            Member Leave Requests
        </h2>
        <p class="text-lg text-black">Review and manage pending leave requests from group members</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">#</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Join Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Installments Left</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Total Contribution</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Total Withdrawn</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($leaveRequests as $i => $request)
                        <tr>
                            <td class="px-4 py-2">{{ $i + 1 }}</td>
                            <td class="px-4 py-2">{{ $request['name'] }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($request['join_date'])->format('M d, Y') }}</td>
                            <td class="px-4 py-2">{{ $request['installments_left'] }}</td>
                            <td class="px-4 py-2">{{ number_format($request['total_contribution']) }} BDT</td>
                            <td class="px-4 py-2">{{ number_format($request['total_withdrawn']) }} BDT</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $request['leave_request'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($request['leave_request']) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <form method="POST" action="{{ route('groups.admin.member-leave-requests.approve', [$group->group_id, $request['membership_id']]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded mr-2">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('groups.admin.member-leave-requests.reject', [$group->group_id, $request['membership_id']]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-4 text-center text-gray-500">No pending leave requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 