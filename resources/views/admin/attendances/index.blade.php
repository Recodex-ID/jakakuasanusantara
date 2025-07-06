<x-layouts.app>
    <x-slot name="title">Attendance Monitoring</x-slot>

    <div class="py-6">
        <div class="container mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Attendance Monitoring</h1>
                            <p class="text-gray-600">Monitor employee attendance and check-in/check-out activities</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button onclick="refreshData()"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-sync-alt class="w-5 h-5 mr-2" />
                                Refresh
                            </button>
                            <a href="{{ route('admin.reports.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-chart-bar class="w-5 h-5 mr-2" />
                                View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <x-fas-check class="w-5 h-5 text-green-600" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Present Today</div>
                                <div class="text-2xl font-bold text-green-600" id="present-count">
                                    {{ $stats['present'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <x-fas-times class="w-5 h-5 text-red-600" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Absent Today</div>
                                <div class="text-2xl font-bold text-red-600" id="absent-count">
                                    {{ $stats['absent'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <x-fas-clock class="w-5 h-5 text-yellow-600" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Late Arrivals</div>
                                <div class="text-2xl font-bold text-yellow-600" id="late-count">
                                    {{ $stats['late'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <x-fas-users class="w-5 h-5 text-blue-600" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Total Employees</div>
                                <div class="text-2xl font-bold text-blue-600" id="total-employees">
                                    {{ $stats['total_employees'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.attendances.index') }}"
                        class="flex flex-wrap gap-4 items-end">
                        <div class="min-w-48">
                            <x-forms.input type="date" name="date" id="date" label="Date"
                                value="{{ request('date', today()->format('Y-m-d')) }}" />
                        </div>
                        <div class="min-w-48">
                            <x-forms.select name="location" id="location" label="Location">
                                <option value="">All Locations</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}"
                                        {{ request('location') == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </x-forms.select>
                        </div>
                        <div class="min-w-32">
                            <x-forms.select name="status" id="status" label="Status">
                                <option value="">All Status</option>
                                <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>
                                    Present</option>
                                <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Absent
                                </option>
                                <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Late
                                </option>
                            </x-forms.select>
                        </div>
                        <div class="flex gap-2">
                            <x-button type="primary" buttonType="submit">
                                Filter
                            </x-button>
                            <a href="{{ route('admin.attendances.index') }}"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-md transition-colors">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Employee
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Location
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Check In
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Check Out
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($attendances as $attendance)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-blue-900">
                                                        {{ $attendance->employee->user->initials() }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $attendance->employee->user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $attendance->employee->employee_id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $attendance->location->name }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ Str::limit($attendance->location->address, 30) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($attendance->check_in)
                                            <div class="text-sm text-gray-900">
                                                {{ $attendance->check_in->format('H:i:s') }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ $attendance->check_in->format('M j, Y') }}</div>
                                        @else
                                            <span class="text-sm text-gray-400">Not checked in</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($attendance->check_out)
                                            <div class="text-sm text-gray-900">
                                                {{ $attendance->check_out->format('H:i:s') }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ $attendance->check_out->format('M j, Y') }}</div>
                                        @else
                                            <span class="text-sm text-gray-400">Not checked out</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                     @if ($attendance->status === 'present') bg-green-100 text-green-800
                                                     @elseif($attendance->status === 'late') bg-yellow-100 text-yellow-800
                                                     @elseif($attendance->status === 'absent') bg-red-100 text-red-800
                                                     @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.attendances.show', $attendance) }}"
                                                class="text-blue-600 hover:text-blue-900" title="View Details">
                                                <x-fas-eye class="w-5 h-5" />
                                            </a>
                                            <a href="{{ route('admin.employees.show', $attendance->employee) }}"
                                                class="text-purple-600 hover:text-purple-900" title="View Employee">
                                                <x-fas-user class="w-5 h-5" />
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="text-gray-500">
                                            <x-fas-clipboard-list class="w-12 h-12 mx-auto mb-4 text-gray-400" />
                                            <p class="text-lg font-medium">No attendance records found</p>
                                            <p class="text-sm">Try adjusting your filters or check back later.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($attendances->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200">
                        {{ $attendances->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function refreshData() {
            location.reload();
        }

        // Auto-refresh every 30 seconds
        setInterval(function() {
            // Only auto-refresh if we're viewing today's data
            const dateInput = document.getElementById('date');
            const today = new Date().toISOString().split('T')[0];

            if (dateInput.value === today || !dateInput.value) {
                // Refresh stats without full page reload
                fetch(window.location.href, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.stats) {
                            document.getElementById('present-count').textContent = data.stats.present || 0;
                            document.getElementById('absent-count').textContent = data.stats.absent || 0;
                            document.getElementById('late-count').textContent = data.stats.late || 0;
                            document.getElementById('total-employees').textContent = data.stats
                                .total_employees || 0;
                        }
                    })
                    .catch(error => {
                        // Silently ignore errors for auto-refresh
                    });
            }
        }, 30000); // 30 seconds
    </script>
</x-layouts.app>
