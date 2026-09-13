@extends('layouts.site_admin')

@section('title', 'Edit Campaign')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded shadow">
    <h2 class="text-2xl font-bold mb-6 text-green-700">Edit Campaign</h2>
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.campaign.update', $campaign->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="title">Title</label>
            <input type="text" name="title" id="title" class="w-full border rounded px-3 py-2" required value="{{ old('title', $campaign->title) }}">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="description">Description</label>
            <textarea name="description" id="description" rows="4" class="w-full border rounded px-3 py-2" required>{{ old('description', $campaign->description) }}</textarea>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="goal_amount">Goal Amount</label>
            <input type="number" name="goal_amount" id="goal_amount" class="w-full border rounded px-3 py-2" required min="1" value="{{ old('goal_amount', $campaign->goal_amount) }}">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="deadline">Deadline</label>
            <input type="date" name="deadline" id="deadline" class="w-full border rounded px-3 py-2" required value="{{ old('deadline', $campaign->deadline) }}">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="status">Status</label>
            <select name="status" id="status" class="w-full border rounded px-3 py-2" required>
                <option value="active" @if(old('status', $campaign->status) == 'active') selected @endif>Active</option>
                <option value="completed" @if(old('status', $campaign->status) == 'completed') selected @endif>Completed</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="bKash">bKash Number</label>
            <input type="text" name="bKash" id="bKash" class="w-full border rounded px-3 py-2" value="{{ old('bKash', $campaign->bKash) }}">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="Rocket">Rocket Number</label>
            <input type="text" name="Rocket" id="Rocket" class="w-full border rounded px-3 py-2" value="{{ old('Rocket', $campaign->Rocket) }}">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold" for="Nagad">Nagad Number</label>
            <input type="text" name="Nagad" id="Nagad" class="w-full border rounded px-3 py-2" value="{{ old('Nagad', $campaign->Nagad) }}">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Update Campaign</button>
    </form>
</div>
@endsection 