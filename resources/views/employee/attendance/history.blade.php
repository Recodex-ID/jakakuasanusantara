<x-layouts.app>
    <x-slot name="title">Attendance History</x-slot>

    <!-- Flash Messages -->
    <x-flash-messages />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Attendance History</h1>
                            <p class="text-gray-600">View your attendance records and statistics</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('employee.attendance.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-clock class="w-5 h-5 mr-2" />
                                Record Attendance
                            </a>
                            <a href="{{ route('employee.attendance.history', array_merge(request()->all(), ['export' => 'csv'])) }}"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-download class="w-5 h-5 mr-2" />
                                Export CSV
                            </a>
                            <a href="{{ route('employee.dashboard') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-tachometer-alt class="w-5 h-5 mr-2" />
                                Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <x-fas-calendar-alt class="w-5 h-5 text-blue-600" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Total Days</div>
                                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_days'] }}</div>
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
                                <div class="text-sm font-medium text-gray-500">Present Days</div>
                                <div class="text-2xl font-bold text-green-600">{{ $stats['present_days'] }}</div>
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
                                <div class="text-sm font-medium text-gray-500">Absent Days</div>
                                <div class="text-2xl font-bold text-red-600">{{ $stats['absent_days'] }}</div>
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
                                <div class="text-sm font-medium text-gray-500">Late Days</div>
                                <div class="text-2xl font-bold text-yellow-600">{{ $stats['late_days'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('employee.attendance.history') }}"
                        class="flex flex-wrap gap-4 items-end">
                        <div class="min-w-48">
                            <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                            <input type="month" name="month" id="month"
                                value="{{ request('month', now()->format('Y-m')) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
                                Filter
                            </button>
                            <a href="{{ route('employee.attendance.history') }}"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-md transition-colors">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Attendance Records -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
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
                                    Working Hours
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
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $attendance->date->format('M j, Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $attendance->date->format('l') }}
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
                                                {{ $attendance->check_in->format('M j') }}</div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($attendance->check_out)
                                            <div class="text-sm text-gray-900">
                                                {{ $attendance->check_out->format('H:i:s') }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ $attendance->check_out->format('M j') }}</div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($attendance->check_in)
                                            @php
                                                $workingHours = $attendance->check_out
                                                    ? $attendance->check_in->diffInHours($attendance->check_out, true)
                                                    : ($attendance->date->isToday()
                                                        ? $attendance->check_in->diffInHours(now(), true)
                                                        : 0);
                                                $hours = floor($workingHours);
                                                $minutes = floor(($workingHours - $hours) * 60);
                                            @endphp
                                            <div class="text-sm text-gray-900">{{ $hours }}h
                                                {{ $minutes }}m</div>
                                            @if (!$attendance->check_out && $attendance->date->isToday())
                                                <div class="text-sm text-blue-500">Currently working</div>
                                            @elseif(!$attendance->check_out)
                                                <div class="text-sm text-red-500">No check out</div>
                                            @endif
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
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
                                        <a href="{{ route('employee.attendance.show', $attendance) }}"
                                            class="text-blue-600 hover:text-blue-900" title="View Details">
                                            <x-fas-eye class="w-5 h-5" />
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="text-gray-500">
                                            <x-fas-clipboard-list class="w-12 h-12 mx-auto mb-4 text-gray-400" />
                                            <p class="text-lg font-medium">No attendance records found</p>
                                            <p class="text-sm">Records for the selected month will appear here.</p>
                                            <a href="{{ route('employee.attendance.index') }}"
                                                class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                                Record Attendance
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Calendar View -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Calendar View</h3>
                    <div class="grid grid-cols-7 gap-2 text-center">
                        <!-- Calendar Header -->
                        <div class="font-medium text-gray-500 py-2">Sun</div>
                        <div class="font-medium text-gray-500 py-2">Mon</div>
                        <div class="font-medium text-gray-500 py-2">Tue</div>
                        <div class="font-medium text-gray-500 py-2">Wed</div>
                        <div class="font-medium text-gray-500 py-2">Thu</div>
                        <div class="font-medium text-gray-500 py-2">Fri</div>
                        <div class="font-medium text-gray-500 py-2">Sat</div>

                        <!-- Calendar Days -->
                        @php
                            $month = request('month') ? \Carbon\Carbon::parse(request('month')) : now();
                            $startOfMonth = $month->copy()->startOfMonth();
                            $endOfMonth = $month->copy()->endOfMonth();
                            $startOfCalendar = $startOfMonth->copy()->startOfWeek();
                            $endOfCalendar = $endOfMonth->copy()->endOfWeek();

                            $attendanceMap = $attendances->keyBy(function ($attendance) {
                                return $attendance->date->format('Y-m-d');
                            });
                        @endphp

                        @for ($date = $startOfCalendar; $date <= $endOfCalendar; $date->addDay())
                            @php
                                $isCurrentMonth = $date->month === $month->month;
                                $isToday = $date->isToday();
                                $attendance = $attendanceMap->get($date->format('Y-m-d'));
                            @endphp

                            <div
                                class="relative p-2 h-16 border border-gray-200 rounded
                                        {{ $isCurrentMonth ? 'bg-white' : 'bg-gray-50' }}
                                        {{ $isToday ? 'ring-2 ring-blue-500' : '' }}">
                                <div class="text-sm {{ $isCurrentMonth ? 'text-gray-900' : 'text-gray-400' }}">
                                    {{ $date->day }}
                                </div>

                                @if ($attendance && $isCurrentMonth)
                                    <div class="absolute bottom-1 left-1 right-1">
                                        <div
                                            class="w-full h-2 rounded
                                                    @if ($attendance->status === 'present') bg-green-400
                                                    @elseif($attendance->status === 'late') bg-yellow-400
                                                    @elseif($attendance->status === 'absent') bg-red-400
                                                    @else bg-gray-400 @endif">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endfor
                    </div>

                    <!-- Legend -->
                    <div class="mt-4 flex items-center justify-center space-x-6 text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-400 rounded mr-2"></div>
                            <span class="text-gray-600">Present</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-400 rounded mr-2"></div>
                            <span class="text-gray-600">Late</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-400 rounded mr-2"></div>
                            <span class="text-gray-600">Absent</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
