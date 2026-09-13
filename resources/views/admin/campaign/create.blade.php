@extends('layouts.site_admin')

@section('title', 'Create Campaign')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-2xl mt-10">
    <h2 class="text-3xl font-extrabold mb-8 text-center text-indigo-700 flex items-center justify-center">
        <i class="fas fa-plus-circle mr-2"></i> Create New Campaign
    </h2>
    
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('admin.campaign.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div>
            <label class="block font-semibold mb-1 text-gray-700" for="title">Campaign Title</label>
            <input type="text" name="title" id="title" 
                class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" 
                required value="{{ old('title') }}" 
                placeholder="Enter campaign title">
        </div>
        
        <div>
            <label class="block font-semibold mb-1 text-gray-700" for="description">Campaign Description</label>
            <textarea name="description" id="description" rows="4" 
                class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" 
                required placeholder="Describe your campaign goals and purpose">{{ old('description') }}</textarea>
        </div>
        
        <div>
            <label class="block font-semibold mb-1 text-gray-700" for="goal_amount">Goal Amount (৳)</label>
            <input type="number" name="goal_amount" id="goal_amount" 
                class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" 
                required min="1" value="{{ old('goal_amount') }}" 
                placeholder="Enter goal amount">
        </div>
        
        <div>
            <label class="block font-semibold mb-1 text-gray-700" for="deadline">Campaign Deadline</label>
            <input type="date" name="deadline" id="deadline" 
                class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" 
                required value="{{ old('deadline') }}">
        </div>
        
        <div>
            <label class="block font-semibold mb-1 text-gray-700" for="bKash">bKash Number</label>
            <input type="text" name="bKash" id="bKash" 
                class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" 
                value="{{ old('bKash') }}" 
                placeholder="01XXXXXXXXX">
        </div>
        
        <div>
            <label class="block font-semibold mb-1 text-gray-700" for="Rocket">Rocket Number</label>
            <input type="text" name="Rocket" id="Rocket" 
                class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" 
                value="{{ old('Rocket') }}" 
                placeholder="01XXXXXXXXX">
        </div>
        
        <div>
            <label class="block font-semibold mb-1 text-gray-700" for="Nagad">Nagad Number</label>
            <input type="text" name="Nagad" id="Nagad" 
                class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" 
                value="{{ old('Nagad') }}" 
                placeholder="01XXXXXXXXX">
        </div>
        
        <div class="flex justify-center pt-2">
            <button type="submit" 
                class="bg-green-600 text-white px-8 py-2 rounded-lg hover:bg-green-700 font-semibold shadow transition flex items-center">
                <i class="fas fa-plus mr-2"></i> Create Campaign
            </button>
        </div>
    </form>
</div>
@endsection 