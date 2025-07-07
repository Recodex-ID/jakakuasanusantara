<aside :class="{ 'w-full md:w-64': sidebarOpen, 'w-0 md:w-16 hidden md:block': !sidebarOpen }"
    class="bg-sidebar text-sidebar-foreground border-r border-gray-200 dark:border-gray-700 sidebar-transition overflow-hidden">
    <!-- Sidebar Content -->
    <div class="h-full flex flex-col">
        <!-- Sidebar Menu -->
        <nav class="flex-1 overflow-y-auto custom-scrollbar py-4">
            <ul class="space-y-1 px-2">
                <!-- Dashboard -->
                <x-layouts.sidebar-link href="{{ route('dashboard') }}" icon='fas-house'
                    :active="request()->routeIs('dashboard*')">Dashboard</x-layouts.sidebar-link>

                @if (auth()->user()->isAdmin())
                    <!-- Employee Management -->
                    <x-layouts.sidebar-link href="{{ route('admin.employees.index') }}" icon='fas-users'
                        :active="request()->routeIs('admin.employees*')">Employees</x-layouts.sidebar-link>

                    <!-- Location Management -->
                    <x-layouts.sidebar-link href="{{ route('admin.locations.index') }}" icon='fas-map-marker-alt'
                        :active="request()->routeIs('admin.locations*')">Locations</x-layouts.sidebar-link>


                    <!-- Attendance Management -->
                    <x-layouts.sidebar-link href="{{ route('admin.attendances.index') }}" icon='fas-clock'
                        :active="request()->routeIs('admin.attendances*')">Attendance</x-layouts.sidebar-link>

                @endif

                @if (auth()->user()->isEmployee())
                    <!-- Employee Attendance -->
                    <x-layouts.sidebar-link href="{{ route('employee.attendance.index') }}" icon='fas-clock'
                        :active="request()->routeIs('employee.attendance*')">My Attendance</x-layouts.sidebar-link>

                    <!-- Employee Profile -->
                    <x-layouts.sidebar-link href="{{ route('employee.profile') }}" icon='fas-user' :active="request()->routeIs('employee.profile*')">My
                        Profile</x-layouts.sidebar-link>
                @endif

                <!-- Settings -->
                <x-layouts.sidebar-two-level-link-parent title="Settings" icon="fas-cog" :active="request()->routeIs('settings*')">
                    <x-layouts.sidebar-two-level-link href="{{ route('settings.profile.edit') }}" icon='fas-user-edit'
                        :active="request()->routeIs('settings.profile*')">Profile</x-layouts.sidebar-two-level-link>
                    <x-layouts.sidebar-two-level-link href="{{ route('settings.password.edit') }}" icon='fas-key'
                        :active="request()->routeIs('settings.password*')">Password</x-layouts.sidebar-two-level-link>
                    <x-layouts.sidebar-two-level-link href="{{ route('settings.appearance.edit') }}" icon='fas-palette'
                        :active="request()->routeIs('settings.appearance*')">Appearance</x-layouts.sidebar-two-level-link>
                </x-layouts.sidebar-two-level-link-parent>
            </ul>
        </nav>
    </div>
</aside>
