@extends('layouts.site_admin')

@section('title', 'Add Expert')

@section('content')
<div class="max-w-xl mx-auto bg-white p-8 rounded-2xl shadow-2xl mt-10">
    <h2 class="text-3xl font-extrabold mb-8 text-center text-indigo-700 flex items-center justify-center"><i class="fas fa-user-plus mr-2"></i> Add Expert</h2>
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg text-center font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('admin.expert.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div>
            <label class="block font-semibold mb-1 text-gray-700" for="name">Name</label>
            <input type="text" name="name" id="name" class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" value="{{ old('name') }}" required>
        </div>
        <div>
            <label class="block font-semibold mb-1 text-gray-700" for="email">Email</label>
            <input type="email" name="email" id="email" class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" value="{{ old('email') }}" required>
        </div>
        <div>
            <label class="block font-semibold mb-1 text-gray-700" for="phone">Phone</label>
            <input type="text" name="phone" id="phone" class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" value="{{ old('phone') }}" required>
        </div>
        <div>
            <label class="block font-semibold mb-1 text-gray-700" for="expertise">Expertise</label>
            <input type="text" name="expertise" id="expertise" class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" value="{{ old('expertise') }}" required>
        </div>
        <div>
            <label class="block font-semibold mb-1 text-gray-700" for="bio">Bio</label>
            <textarea name="bio" id="bio" class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" rows="4" required>{{ old('bio') }}</textarea>
        </div>
        <div>
            <label class="block font-semibold mb-1 text-gray-700" for="image">Photo</label>
            <input type="file" name="image" id="image" class="w-full border border-indigo-300 rounded-lg px-4 py-2" accept="image/*" required>
        </div>
        <div class="flex justify-center pt-2">
            <button type="submit" class="bg-green-600 text-white px-8 py-2 rounded-lg hover:bg-green-700 font-semibold shadow transition flex items-center"><i class="fas fa-plus mr-2"></i> Add Expert</button>
        </div>
    </form>
</div>
@endsection 