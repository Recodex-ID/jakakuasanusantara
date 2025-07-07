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
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.attendances.index') }}"
                        class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-48">
                            <x-forms.input type="month" name="month" label="Filter by Month"
                                value="{{ request('month') }}" />
                        </div>
                        <div class="flex-1 min-w-48">
                            <x-forms.select name="employee_id" label="Filter by Employee" :options="$employees->pluck('user.name', 'id')->prepend('All Employees', '')"
                                :selected="request('employee_id')" />
                        </div>
                        <div class="flex gap-2">
                            <x-button type="primary" buttonType="submit">
                                <x-fas-filter class="w-4 h-4 mr-2" />
                                Filter
                            </x-button>
                            <a href="{{ route('admin.attendances.index') }}"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Filter Statistics -->
            @if ((request('month') || request('employee_id')) && !empty($stats))
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <x-fas-calendar class="w-5 h-5 text-blue-600" />
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Total Records</div>
                                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <x-fas-check class="w-5 h-5 text-green-600" />
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Present</div>
                                    <div class="text-2xl font-bold text-green-600">{{ $stats['present'] ?? 0 }}</div>
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
                                    <div class="text-sm font-medium text-gray-500">Late</div>
                                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['late'] ?? 0 }}</div>
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
                                    <div class="text-sm font-medium text-gray-500">Absent</div>
                                    <div class="text-2xl font-bold text-red-600">{{ $stats['absent'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

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
                                        @php
                                            $statusClasses = [
                                                'present' => 'bg-green-100 text-green-800',
                                                'late' => 'bg-yellow-100 text-yellow-800',
                                                'absent' => 'bg-red-100 text-red-800',
                                                'default' => 'bg-gray-100 text-gray-800',
                                            ];
                                            $class = $statusClasses[$attendance->status] ?? $statusClasses['default'];
                                        @endphp

                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class }}">
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
    </script>
</x-layouts.app>
