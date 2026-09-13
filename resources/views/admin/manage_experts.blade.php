@extends('layouts.site_admin')

@section('title', 'Manage Experts')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold flex items-center"><i class="fas fa-users-cog mr-2"></i> Manage Experts</h1>
    <a href="{{ route('admin.expert.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex items-center"><i class="fas fa-plus mr-2"></i> Add New Expert</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($experts as $expert)
        <div class="bg-white rounded shadow p-6 flex flex-col relative">
            <div class="flex items-center mb-4">
                <img src="{{ Str::startsWith($expert->image, 'http') ? $expert->image : asset($expert->image) }}" class="w-16 h-16 rounded-full object-cover border-2 border-indigo-400 mr-4" alt="{{ $expert->name }}">
                <div>
                    <div class="font-bold text-lg">{{ $expert->name }}</div>
                    <div class="text-indigo-600 font-semibold">{{ $expert->expertise }}</div>
                </div>
                <div class="ml-auto flex items-center space-x-2">
                    <button class="edit-expert-btn text-blue-500 hover:text-blue-700">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('admin.expert.delete', $expert->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this expert?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 ml-2"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
            <div class="mb-2 text-gray-700"><i class="fas fa-envelope mr-1"></i> {{ $expert->email }}</div>
            <div class="mb-2 text-gray-700"><i class="fas fa-phone mr-1"></i> {{ $expert->phone }}</div>
            <div class="text-gray-600 text-sm">{{ $expert->bio }}</div>
        </div>
    @endforeach
</div>

<!-- Edit Expert Modal -->
<div id="editExpertModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-0 relative flex flex-col" style="max-height:90vh;">
        <button type="button" class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-3xl z-10" onclick="closeEditModal()" aria-label="Close">&times;</button>
        <div class="overflow-y-auto p-8" style="max-height:80vh; scrollbar-width: thin; scrollbar-color: #6366f1 #e5e7eb;">
            <h2 class="text-2xl font-bold mb-6 text-center text-indigo-700 flex items-center justify-center"><i class="fas fa-user-edit mr-2"></i> Edit Expert</h2>
            <form id="editExpertForm" method="POST" action="" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label class="block font-semibold mb-1 text-gray-700" for="edit_name">Name</label>
                    <input type="text" name="name" id="edit_name" class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1 text-gray-700" for="edit_email">Email</label>
                    <input type="email" name="email" id="edit_email" class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1 text-gray-700" for="edit_phone">Phone</label>
                    <input type="text" name="phone" id="edit_phone" class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1 text-gray-700" for="edit_expertise">Expertise</label>
                    <input type="text" name="expertise" id="edit_expertise" class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1 text-gray-700" for="edit_bio">Bio</label>
                    <textarea name="bio" id="edit_bio" class="w-full border border-indigo-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none transition" rows="4" required></textarea>
                </div>
                <div>
                    <label class="block font-semibold mb-1 text-gray-700" for="edit_image">Photo</label>
                    <input type="file" name="image" id="edit_image" class="w-full border border-indigo-300 rounded-lg px-4 py-2" accept="image/*">
                    <div class="mt-3 flex flex-col items-center" id="currentPhotoContainer">
                        <img id="currentPhoto" src="" alt="Current Photo" class="w-24 h-24 rounded-full object-cover border-2 border-indigo-300 shadow mt-2">
                        <div class="text-xs text-gray-500 mt-1">Current Photo</div>
                    </div>
                </div>
                <div class="flex justify-center pt-2">
                    <button type="submit" class="bg-indigo-600 text-white px-8 py-2 rounded-lg hover:bg-indigo-700 font-semibold shadow transition">Update Expert</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function closeEditModal() {
        document.getElementById('editExpertModal').classList.add('hidden');
    }
    document.querySelectorAll('.edit-expert-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Find the parent card and extract data
            const card = btn.closest('.flex.items-center.mb-4').parentElement;
            const name = card.querySelector('.font-bold.text-lg').textContent.trim();
            const expertise = card.querySelector('.text-indigo-600.font-semibold').textContent.trim();
            const email = card.querySelector('.mb-2.text-gray-700 i.fa-envelope').parentElement.textContent.trim().replace(/^[^\w]+/, '');
            const phone = card.querySelector('.mb-2.text-gray-700 i.fa-phone').parentElement.textContent.trim().replace(/^[^\d]+/, '');
            const bio = card.querySelector('.text-gray-600.text-sm').textContent.trim();
            const img = card.querySelector('img').src;
            const expertId = @json($experts->pluck('id'))[Array.from(document.querySelectorAll('.edit-expert-btn')).indexOf(btn)];

            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_phone').value = phone;
            document.getElementById('edit_expertise').value = expertise;
            document.getElementById('edit_bio').value = bio;
            document.getElementById('currentPhoto').src = img;
            document.getElementById('currentPhotoContainer').style.display = 'block';
            document.getElementById('editExpertForm').action = '/admin/edit-expert/' + expertId;
            document.getElementById('editExpertModal').classList.remove('hidden');
        });
    });

    document.getElementById('editExpertForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeEditModal();
                location.reload();
            } else {
                alert('Update failed!');
            }
        })
        .catch(() => alert('Update failed!'));
    });
</script>
@endpush
@endsection

@php use Illuminate\Support\Str; @endphp 