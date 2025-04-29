<nav x-data="{ open: false, collapsed: false }"
s    class="bg-white dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700 w-64 h-screen flex-shrink-0 fixed left-0 top-0 z-30 transition-all duration-300"
    :class="{ 'sidebar-hidden': open, 'w-64': !collapsed, 'w-20': collapsed }">
    <div class="h-full flex flex-col" style="position: relative; height: 100vh; padding-right: 2rem;">
        <!-- Logo and Title -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center justify-center space-x-2">
                <!-- Logo (Add your logo here)
                <img src=" alt="IOT-D Logo" class="h-8 w-auto" x-show="!collapsed">-->

                <!-- Title -->
                <h1 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex justify-center items-center w-full" x-show="!collapsed">
                    IOT-D
                </h1>

            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 overflow-y-auto py-4">
            <div class="px-2 space-y-1">
                @auth
                    @if (Auth::user()->role === 'admin')
                        <div class="px-3 pt-2 pb-1 text-sm font-medium text-gray-500 dark:text-gray-400"
                            x-show="!collapsed">
                            Admin Panel
                        </div>
                        <!-- Admin Links -->
                        <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')"
                            class="block px-3 py-2 rounded-md text-base font-medium">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                {{ __('Dashboard') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.durian')" :active="request()->routeIs('admin.durian')"
                            class="block px-3 py-2 rounded-md text-base font-medium">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                {{ __('Durian') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.orchards')" :active="request()->routeIs('admin.orchards')"
                            class="block px-3 py-2 rounded-md text-base font-medium">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                                    </path>
                                </svg>
                                {{ __('Orchard') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.devices')" :active="request()->routeIs('admin.devices')"
                            class="block px-3 py-2 rounded-md text-base font-medium">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                                    </path>
                                </svg>
                                {{ __('Devices') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')"
                            class="block px-3 py-2 rounded-md text-base font-medium">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                                {{ __('User') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.inventory.index')" :active="request()->routeIs('admin.inventory.index')"
                            class="block px-3 py-2 rounded-md text-base font-medium">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                {{ __('Inventory') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.production-report')" :active="request()->routeIs('admin.production-report')"
                            class="block px-3 py-2 rounded-md text-base font-medium">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                {{ __('Production') }}
                            </div>
                        </x-responsive-nav-link>
                    @elseif(Auth::user()->role === 'farmer')
                        <div class="px-3 pt-2 pb-1 text-sm font-medium text-gray-500 dark:text-gray-400"
                            x-show="!collapsed">
                            Farmer Panel
                        </div>
                        <!-- Regular User Links -->
                        <x-responsive-nav-link :href="route('farmer.dashboard')" :active="request()->routeIs('farmer.dashboard')"
                            class="block px-3 py-2 rounded-md text-base font-medium">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" <path
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                                    </path>
                                </svg>
                                {{ __('Dashboard') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('farmer.orchards')" :active="request()->routeIs('farmer.orchards')"
                            class="block px-3 py-2 rounded-md text-base font-medium">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                                    </path>
                                </svg>
                                {{ __('Orchard Monitoring') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('farmer.harvestEntry')" :active="request()->routeIs('farmer.harvestEntry')"
                            class="block px-3 py-2 rounded-md text-base font-medium">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                {{ __('Harvest Entry') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('farmer.harvest.report')" :active="request()->routeIs('farmer.harvest.report')">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ __('Harvest Report') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('farmer.inventory.index')" :active="request()->routeIs('farmer.inventory.index')">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                {{ __('Inventory') }}
                            </div>
                        </x-responsive-nav-link>
                    @elseif(Auth::user()->role === 'manager')
                        <x-responsive-nav-link :href="route('manager.durian-fall.index')" :active="request()->routeIs('manager.durian-fall.index')">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                {{ __('Durian Fall') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('manager.notification.index')" :active="request()->routeIs('manager.notification.index')">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                {{ __('Notification') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('manager.performance.index')" :active="request()->routeIs('manager.performance.index')">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                {{ __('Farmer Performance') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('manager.inventory.index')" :active="request()->routeIs('manager.inventory.index')">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                {{ __('Inventory') }}
                            </div>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('manager.report.index')" :active="request()->routeIs('manager.report.index')">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ __('Report') }}
                            </div>
                        </x-responsive-nav-link>
                    @endif
                @endauth
            </div>
        </div>

        <!-- User Profile Section -->
        <div class="border-t border-gray-100 dark:border-gray-700 p-4">
            <div class="space-y-1">
                <!-- User Name Display -->
                <div class="px-2 py-2 text-sm font-medium text-gray-700 dark:text-gray-300" x-show="!collapsed">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <div class="text-xs text-indigo-500 dark:text-indigo-300">
                            {{ Auth::user()->name }} - {{ ucfirst(Auth::user()->role) }}
                        </div>
                    </div>
                </div>

                <!-- Profile Link -->
                <a href="{{ route('profile.edit') }}"
                    class="block px-2 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors duration-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span x-show="!collapsed">{{ __('Profile') }}</span>
                    </div>
                </a>

                <!-- Logout Link -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-2 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors duration-200">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            <span x-show="!collapsed">{{ __('Log Out') }}</span>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>

</nav>

<!-- Inside the admin section -->
