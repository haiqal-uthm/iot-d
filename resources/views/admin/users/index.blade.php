<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('User Management') }}
            </h2>
        </div>
    </x-slot>

    <!-- Add CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin/admin-users.css') }}">
    <!-- Add Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            <span class="block sm:inline">{{ session('success') }}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                            </span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <span class="block sm:inline">{{ session('error') }}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                            </span>
                        </div>
                    @endif

                    <div class="user-management-container">
                        <div class="page-header">
                            <h1 class="page-title">User Management</h1>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="fas fa-user-plus btn-icon"></i>Add New User
                            </a>
                        </div>

                        <div class="search-filter-container">
                            <div class="search-box">
                                <i class="fas fa-search search-icon"></i>
                                <form method="GET" action="{{ route('admin.users.index') }}">
                                    <input type="text" name="search" placeholder="Search users by name or email..." 
                                        value="{{ request('search') }}" 
                                        class="focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </form>
                            </div>
                        </div>

                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Assigned Orchards</th>
                                    <th>Date Added</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>
                                            <div class="user-profile">
                                                @if ($user->profile_image)
                                                    <img src="{{ asset('storage/' . $user->profile_image) }}"
                                                        class="user-avatar"
                                                        alt="{{ $user->name }}'s profile picture">
                                                @else
                                                    <img src="{{ asset('images/Sample_User_Icon.png') }}"
                                                        class="user-avatar"
                                                        alt="Default user icon">
                                                @endif
                                                <div class="user-info">
                                                    <span class="user-name">{{ $user->name }}</span>
                                                    <span class="user-email">{{ $user->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="role-badge {{ 'role-' . $user->role }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($user->role === 'farmer')
                                                <div class="flex flex-wrap">
                                                    @forelse(optional($user->farmer)->orchards ?? [] as $orchard)
                                                        <span class="orchard-tag">
                                                            {{ $orchard->orchardName }}
                                                        </span>
                                                    @empty
                                                        <span class="no-orchards">No orchards assigned</span>
                                                    @endforelse
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span title="{{ $user->created_at }}">
                                                {{ $user->created_at->format('M j, Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <!-- User Details Edit -->
                                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                                                    <i class="fas fa-user-edit btn-icon"></i>Edit
                                                </a>

                                                <!-- Orchard Assignment -->
                                                @if ($user->role === 'farmer')
                                                    <a href="{{ route('admin.users.manage_orchards', $user) }}" class="btn btn-success">
                                                        <i class="fas fa-tree btn-icon"></i>Orchards
                                                    </a>
                                                @endif

                                                <!-- Delete -->
                                                @if ($user->id !== auth()->id())
                                                    <button type="button" 
                                                        onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')"
                                                        class="btn btn-danger">
                                                        <i class="fas fa-trash-alt btn-icon"></i>Delete
                                                    </button>
                                                    <form id="delete-form-{{ $user->id }}" 
                                                        action="{{ route('admin.users.destroy', $user) }}" 
                                                        method="POST" 
                                                        class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="pagination-container">
                            {{ $users->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Delete User</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="deleteModalText"></p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="deleteConfirmButton" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 mb-2">
                        Delete
                    </button>
                    <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
function confirmDelete(userId, userName) {
    const modal = document.getElementById('deleteModal');
    const modalText = document.getElementById('deleteModalText');
    const confirmButton = document.getElementById('deleteConfirmButton');
    
    modalText.textContent = `Are you sure you want to delete ${userName}? This action cannot be undone.`;
    
    confirmButton.onclick = function() {
        document.getElementById(`delete-form-${userId}`).submit();
    };
    
    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
