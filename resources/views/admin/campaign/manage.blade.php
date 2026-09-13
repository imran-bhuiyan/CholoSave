@extends('layouts.site_admin')

@section('title', 'Manage Campaigns')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-8 rounded shadow">
    <h2 class="text-2xl font-bold mb-6 text-green-700">Manage Campaigns</h2>
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif
    <table class="min-w-full bg-white border rounded">
        <thead>
            <tr>
                <th class="px-4 py-2 border-b">Title</th>
                <th class="px-4 py-2 border-b">Goal Amount</th>
                <th class="px-4 py-2 border-b">Current Amount</th>
                <th class="px-4 py-2 border-b">Deadline</th>
                <th class="px-4 py-2 border-b">Status</th>
                <th class="px-4 py-2 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($campaigns as $campaign)
                <tr>
                    <td class="px-4 py-2 border-b">{{ $campaign->title }}</td>
                    <td class="px-4 py-2 border-b">{{ number_format($campaign->goal_amount, 2) }}</td>
                    <td class="px-4 py-2 border-b">{{ number_format($campaign->current_amount, 2) }}</td>
                    <td class="px-4 py-2 border-b">{{ $campaign->deadline }}</td>
                    <td class="px-4 py-2 border-b capitalize">{{ $campaign->status }}</td>
                    <td class="px-4 py-2 border-b">
                        <a href="{{ route('admin.campaign.edit', $campaign->id) }}" class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">No campaigns found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection 