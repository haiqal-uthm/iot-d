<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('User Management') }}
            </h2>

        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <div class="flex justify-between items-center mb-4">
                            <form method="GET" action="{{ route('admin.users.index') }}" class="w-full max-w-md">
                                <div class="flex gap-2">
                                    <input type="text" name="search" placeholder="Search users..."
                                        value="{{ request('search') }}"
                                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-500 text-black rounded-md hover:bg-blue-600">
                                        Search
                                    </button>
                                </div>
                            </form>
                            <a href="{{ route('admin.users.create') }}"
                                class="px-4 py-2 bg-blue-500 text-black rounded-md hover:bg-blue-600 whitespace-nowrap">
                                Add New User
                            </a>
                        </div>
                        <table class="min-w-full bg-white dark:bg-gray-700 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100 dark:bg-gray-600 text-gray-900 dark:text-gray-100">
                                <tr>
                                    <th class="py-3 px-4 text-left">User name</th>
                                    <th class="py-3 px-4 text-left">Role</th>
                                    <th class="py-3 px-4 text-left">Assigned Orchards</th>
                                    <th class="py-3 px-4 text-left">Date Added</th>
                                    <th class="py-3 px-4 text-left">Actions</th>
                                </tr>
                            </thead>
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center">
                                            @if ($user->profile_image)
                                                <img src="{{ asset('storage/' . $user->profile_image) }}"
                                                    class="h-10 w-10 rounded-full object-cover mr-3"
                                                    alt="{{ $user->name }}'s profile picture" style="height: 40px;">
                                            @else
                                                <img src="{{ asset('images/Sample_User_Icon.png') }}"
                                                    class="h-10 w-10 rounded-full object-cover mr-3"
                                                    alt="Default user icon" style="height: 30px;">
                                            @endif
                                            <div class="flex flex-col" style="padding-left: 10px;">
                                                {{ $user->name }}
                                                <span
                                                    class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</span>
                                            </div>
                                        </div>

                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                            @if ($user->role === 'admin') text-green-600 bg-green-100 border border-green-300
                                            @elseif($user->role === 'farmer') text-purple-600 bg-purple-100 border border-purple-300
                                            @else text-orange-600 bg-orange-100 border border-orange-300 @endif">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>

                                    <td class="py-3 px-4">
                                        @if ($user->role === 'farmer')
                                            @forelse(optional($user->farmer)->orchards ?? [] as $orchard)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-blue-600 bg-blue-100 border border-blue-300">
                                                    {{ $orchard->orchardName }}
                                                </span>
                                            @empty
                                                <span class="text-red-500 text-xs">No orchards assigned</span>
                                            @endforelse
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $user->created_at->format('M j, Y H:i') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex space-x-2">
                                            <!-- User Details Edit -->
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                                class="px-2 py-1 bg-blue-500 text-black rounded hover:bg-blue-600 text-sm">
                                                Edit Details
                                            </a>

                                            <!-- Orchard Assignment -->
                                            <!-- Change from link to form -->
                                            @if ($user->role === 'farmer')
                                                <a href="{{ route('admin.users.manage_orchards', $user) }}"
                                                    class="px-2 py-1 bg-green-500 text-black rounded hover:bg-green-600 text-sm">
                                                    Manage Orchards
                                                </a>
                                            @endif

                                            <!-- Delete -->
                                            @if ($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                    onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-2 py-1 bg-red-500 text-black rounded hover:bg-red-600 text-sm">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $users->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
