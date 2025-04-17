<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700 w-64 h-screen flex-shrink-0 fixed left-0 top-0">
    <div class="h-full flex flex-col" style="position: relative; height: 100vh;">
        <!-- Logo and Title -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-center space-x-2">
                <!-- Custom Logo -->
                <!-- Title -->
                <h1 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                    SMART DURIAN
                </h1>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto py-4">
            <div class="px-2 space-y-1">
                @auth
                    @if(Auth::user()->role === 'admin')
                        <div class="px-3 pt-2 pb-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                            Admin Panel
                        </div>
                        <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="block px-3 py-2 rounded-md text-base font-medium">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                                {{ __('Admin Dashboard') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('durian')" :active="request()->routeIs('durian')" class="block px-3 py-2 rounded-md text-base font-medium">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                {{ __('Durian') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('orchards')" :active="request()->routeIs('orchards')" class="block px-3 py-2 rounded-md text-base font-medium">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                                {{ __('Orchard Monitoring') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('devices')" :active="request()->routeIs('devices')" class="block px-3 py-2 rounded-md text-base font-medium">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                </svg>
                                {{ __('Devices Controller') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('production-report')" :active="request()->routeIs('production-report')" class="block px-3 py-2 rounded-md text-base font-medium">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                {{ __('Production') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="block px-3 py-2 rounded-md text-base font-medium">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                {{ __('Farmer Management') }}
                            </div>
                        </x-responsive-nav-link>
                    @elseif(Auth::user()->role === 'Farmer')
                    <!-- Regular User Links -->
                    <div class="px-3 pt-2 pb-1 text-sm font-medium text-gray-500 dark:text-gray-400">
                        Farmer Panel
                    </div>
                    @endif
                @endauth
            </div>
        </div>

        <!-- User Profile Section -->
        <div class="border-t border-gray-100 dark:border-gray-700 p-4" style="position: absolute; bottom: 0;"> 
            <div class="space-y-1">
                <!-- User Name Display -->
                <div class="px-2 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <div class="text-xs text-indigo-500 dark:text-indigo-300">
                            {{ ucfirst(Auth::user()->role) }}
                        </div>
                    </div>
                </div>
                
                <!-- Profile Link -->
                <a href="{{ route('profile.edit') }}" class="block px-2 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ __('Profile') }}
                    </div>
                </a>
                
                <!-- Logout Link -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-2 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            {{ __('Log Out') }}
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Mobile menu button - only visible on small screens -->
    <div class="md:hidden absolute top-0 right-0 p-4">
        <button @click="open = !open" class="text-gray-500 hover:text-gray-600 focus:outline-none focus:text-gray-600">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</nav>

<!-- Inside the admin section -->

