@extends('layouts.group_member')

@section('title', 'Leave Request')

@section('content')
<div class="max-w-xl mx-auto mt-12 p-8 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-center text-blue-600">
        Leave Group Request
    </h2>
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <form method="POST" action="{{ route('groups.member.leave-request', $group->group_id) }}">
        @csrf
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Request to Leave Group
        </button>
    </form>
    <p class="mt-6 text-gray-600 text-sm text-center">
        <strong>Note:</strong> You can only leave the group if you have no outstanding loans, no remaining savings, and no pending leave request.
    </p>
</div>
@endsection 