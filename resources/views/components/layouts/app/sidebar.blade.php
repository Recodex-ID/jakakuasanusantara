<aside :class="{ 'w-full md:w-64': sidebarOpen, 'w-0 md:w-16 hidden md:block': !sidebarOpen }"
    class="bg-sidebar text-sidebar-foreground border-r border-gray-200 sidebar-transition overflow-hidden">
    <!-- Sidebar Content -->
    <div class="h-full flex flex-col">
        <!-- Sidebar Menu -->
        <nav class="flex-1 overflow-y-auto custom-scrollbar py-4">
            <ul class="space-y-1 px-2">
                <!-- Dashboard -->
                <x-layouts.sidebar-link 
                    href="{{ route('dashboard') }}" 
                    icon='fas-house'
                    :active="request()->routeIs('dashboard*') || request()->routeIs('admin.dashboard*') || request()->routeIs('employee.dashboard*')">
                    Dashboard
                </x-layouts.sidebar-link>

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
                @endif

                <!-- Settings -->
                <x-layouts.sidebar-link href="{{ route('settings.profile.edit') }}" icon='fas-cog' :active="request()->routeIs('settings*')">
                    Settings</x-layouts.sidebar-link>
            </ul>
        </nav>
    </div>
</aside>
