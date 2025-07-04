<x-layouts.app>
    <x-slot name="title">Employee Dashboard</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Dashboard Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Welcome, {{ $employee->full_name }}!</h1>
                            <p class="text-gray-600">{{ $employee->department ?? 'Employee' }} •
                                {{ $employee->position ?? $employee->employee_id }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ now()->format('H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Status -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Today's Attendance</h3>
                    @if ($todayAttendance)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Check In Status -->
                            <div class="text-center">
                                <div class="text-sm font-medium text-gray-500 mb-2">Check In</div>
                                @if ($todayAttendance->check_in)
                                    <div class="text-2xl font-bold text-green-600">
                                        {{ $todayAttendance->check_in->format('H:i') }}</div>
                                    <div class="text-sm text-gray-500">{{ $todayAttendance->location->name }}</div>
                                @else
                                    <div class="text-2xl font-bold text-gray-400">--:--</div>
                                    <div class="text-sm text-gray-400">Not checked in</div>
                                @endif
                            </div>

                            <!-- Check Out Status -->
                            <div class="text-center">
                                <div class="text-sm font-medium text-gray-500 mb-2">Check Out</div>
                                @if ($todayAttendance->check_out)
                                    <div class="text-2xl font-bold text-red-600">
                                        {{ $todayAttendance->check_out->format('H:i') }}</div>
                                    <div class="text-sm text-gray-500">{{ $todayAttendance->location->name }}</div>
                                @else
                                    <div class="text-2xl font-bold text-gray-400">--:--</div>
                                    <div class="text-sm text-gray-400">Not checked out</div>
                                @endif
                            </div>

                            <!-- Working Hours -->
                            <div class="text-center">
                                <div class="text-sm font-medium text-gray-500 mb-2">Working Hours</div>
                                @if ($todayAttendance && $todayAttendance->check_in)
                                    @php
                                        $workingHours = $todayAttendance->check_out
                                            ? $todayAttendance->check_in->diffInHours($todayAttendance->check_out, true)
                                            : $todayAttendance->check_in->diffInHours(now(), true);
                                        $hours = floor($workingHours);
                                        $minutes = floor(($workingHours - $hours) * 60);
                                    @endphp
                                    <div class="text-2xl font-bold text-blue-600">{{ $hours }}h
                                        {{ $minutes }}m</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $todayAttendance->check_out ? 'Total' : 'Current' }}
                                    </div>
                                @else
                                    <div class="text-2xl font-bold text-gray-400">0h 0m</div>
                                    <div class="text-sm text-gray-400">No check in</div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-6 text-center">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                         @if ($todayAttendance->status === 'present') bg-green-100 text-green-800
                                         @elseif($todayAttendance->status === 'late') bg-yellow-100 text-yellow-800
                                         @else bg-gray-100 text-gray-800 @endif">
                                Status: {{ ucfirst($todayAttendance->status) }}
                            </span>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No attendance recorded today</h3>
                            <p class="text-gray-500 mb-4">Start your day by checking in at your assigned location.</p>
                            <a href="{{ route('employee.attendance.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                Check In Now
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('employee.attendance.index') }}"
                                class="w-full flex items-center justify-center px-4 py-3 bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Record Attendance
                            </a>
                            <a href="{{ route('employee.attendance.history') }}"
                                class="w-full flex items-center justify-center px-4 py-3 bg-green-50 hover:bg-green-100 text-green-700 font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                View History
                            </a>
                            <a href="{{ route('profile.edit') }}"
                                class="w-full flex items-center justify-center px-4 py-3 bg-purple-50 hover:bg-purple-100 text-purple-700 font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Update Profile
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Assigned Locations -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Locations</h3>
                        @if ($employee->locations->isEmpty())
                            <div class="text-center py-4">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <p class="text-gray-500 text-sm">No locations assigned</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($employee->locations as $location)
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $location->name }}</h4>
                                                <p class="text-sm text-gray-600">
                                                    {{ Str::limit($location->address, 30) }}</p>
                                            </div>
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                         {{ $location->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($location->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Monthly Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">This Month</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Present Days</span>
                                <span
                                    class="text-sm font-medium text-green-600">{{ $monthlyStats['present'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Absent Days</span>
                                <span
                                    class="text-sm font-medium text-red-600">{{ $monthlyStats['absent'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Late Arrivals</span>
                                <span
                                    class="text-sm font-medium text-yellow-600">{{ $monthlyStats['late'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-200 pt-3">
                                <span class="text-sm font-medium text-gray-900">Attendance Rate</span>
                                <span class="text-sm font-bold text-blue-600">
                                    {{ $monthlyStats['attendance_rate'] ?? '0%' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                    @if ($recentAttendances->isEmpty())
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            <p class="text-gray-500">No recent activity</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($recentAttendances as $attendance)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            @if ($attendance->status === 'present')
                                                <div
                                                    class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-green-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            @elseif($attendance->status === 'late')
                                                <div
                                                    class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-yellow-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            @else
                                                <div
                                                    class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-red-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $attendance->location->name }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ $attendance->date->format('M j, Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if ($attendance->check_in)
                                            <div class="text-sm text-green-600">In:
                                                {{ $attendance->check_in->format('H:i') }}</div>
                                        @endif
                                        @if ($attendance->check_out)
                                            <div class="text-sm text-red-600">Out:
                                                {{ $attendance->check_out->format('H:i') }}</div>
                                        @endif
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mt-1
                                                     @if ($attendance->status === 'present') bg-green-100 text-green-800
                                                     @elseif($attendance->status === 'late') bg-yellow-100 text-yellow-800
                                                     @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('employee.attendance.history') }}"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View All History →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh current time
        setInterval(function() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit'
            });

            // Find and update time display if it exists
            const timeDisplay = document.querySelector('.text-lg.font-semibold.text-gray-900');
            if (timeDisplay) {
                timeDisplay.textContent = timeString;
            }
        }, 1000);
    </script>
</x-layouts.app>
