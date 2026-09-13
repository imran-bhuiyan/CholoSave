@extends('layouts.group_admin')

@section('title', 'Leave Request (For Me)')

@section('content')
<div class="max-w-xl mx-auto mt-12 p-8 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-center text-red-600">
        Leave Group Request
    </h2>
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif
    <form method="POST" action="{{ route('groups.admin.leave-request', $group->group_id) }}">
        @csrf
        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Request to Leave Group
        </button>
    </form>
    <p class="mt-6 text-gray-600 text-sm text-center">
        <strong>Note:</strong> As an admin, you cannot leave the group. If you have outstanding loans or are still the admin, you will see an error message.
    </p>
</div>
@endsection 