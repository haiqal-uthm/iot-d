<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit User') }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left btn-icon"></i>Back to Users
            </a>
        </div>
    </x-slot>

    <!-- Add Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin-users.css') }}">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <i class="fas fa-check"></i>
                    </span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <i class="fas fa-exclamation-circle"></i>
                    </span>
                </div>
            @endif

            <!-- User Details Form -->
            <div class="user-edit-container">
                <div class="user-edit-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-edit"></i>User Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="profile-image-container">
                                @if($user->profile_image)
                                    <img src="{{ asset('storage/'.$user->profile_image) }}" class="profile-image" alt="{{ $user->name }}'s profile">
                                @else
                                    <div class="profile-image-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                                
                                <div class="file-input-container">
                                    <label for="profile_image" class="file-input-label">
                                        <i class="fas fa-camera"></i> Change Profile Picture
                                    </label>
                                    <input type="file" name="profile_image" id="profile_image" class="file-input">
                                    @error('profile_image')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-grid">
                                <!-- Name -->
                                <div class="form-group">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                        class="form-control @error('name') border-red-500 @enderror">
                                    @error('name')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="form-group">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                        class="form-control @error('email') border-red-500 @enderror">
                                    @error('email')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="form-group">
                                    <label for="password" class="form-label">New Password</label>
                                    <div class="relative">
                                        <input type="password" name="password" id="password"
                                            class="form-control @error('password') border-red-500 @enderror"
                                            placeholder="Leave blank to keep current password">
                                        <div class="text-xs text-gray-500 mt-1 dark:text-gray-400">
                                            <i class="fas fa-info-circle"></i> Leave blank to keep current password
                                        </div>
                                    </div>
                                    @error('password')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control">
                                </div>

                                <!-- Role -->
                                <div class="form-group">
                                    <label for="role" class="form-label">User Role</label>
                                    <select name="role" id="role" required class="form-select @error('role') border-red-500 @enderror">
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrator</option>
                                        <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="farmer" {{ $user->role === 'farmer' ? 'selected' : '' }}>Farmer</option>
                                    </select>
                                    @error('role')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-footer">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-cancel">
                                    <i class="fas fa-times btn-icon"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-update">
                                    <i class="fas fa-save btn-icon"></i>Update User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Preview uploaded image before form submission
    document.getElementById('profile_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const profileContainer = document.querySelector('.profile-image-container');
                
                // Remove existing image or placeholder
                const existingImage = profileContainer.querySelector('.profile-image');
                const existingPlaceholder = profileContainer.querySelector('.profile-image-placeholder');
                
                if (existingImage) {
                    existingImage.remove();
                }
                
                if (existingPlaceholder) {
                    existingPlaceholder.remove();
                }
                
                // Create new image element
                const newImage = document.createElement('img');
                newImage.src = event.target.result;
                newImage.classList.add('profile-image');
                newImage.alt = 'Profile preview';
                
                // Insert at the beginning of the container
                profileContainer.insertBefore(newImage, profileContainer.firstChild);
            };
            reader.readAsDataURL(file);
        }
    });
</script>