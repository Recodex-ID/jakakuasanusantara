<x-layouts.app>
    <x-slot name="title">Employee Dashboard</x-slot>

    <!-- Flash Messages -->
    <x-flash-messages />

    <!-- Face Enrollment Alert -->
    @if (!$faceEnrolled)
        <div class="py-4">
            <div class="container mx-auto sm:px-6 lg:px-8">
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-fas-exclamation-triangle class="h-5 w-5 text-yellow-400" />
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-yellow-800">
                                Face Recognition Setup Required
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>You need to register your face before you can record attendance. This is required for
                                    our face recognition attendance system.</p>
                            </div>
                            <div class="mt-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('employee.face-enrollment.index') }}"
                                        class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                        Register Face Now
                                    </a>
                                    <button onclick="dismissAlert()"
                                        class="bg-white hover:bg-gray-50 text-yellow-800 px-3 py-2 rounded-md text-sm font-medium border border-yellow-200 transition-colors">
                                        Remind Later
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0 ml-4">
                            <button onclick="dismissAlert()"
                                class="bg-yellow-50 rounded-md inline-flex text-yellow-400 hover:text-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-yellow-50 focus:ring-yellow-600">
                                <span class="sr-only">Close</span>
                                <x-fas-times class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="py-6">
        <div class="container mx-auto sm:px-6 lg:px-8">
            <!-- Dashboard Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Welcome, {{ $employee->user->name }}!</h1>
                            <p class="text-gray-600">{{ $employee->department ?? 'Employee' }} •
                                {{ $employee->position ?? $employee->employee_id }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</div>
                            <div id="realtime-clock" class="text-lg font-semibold text-gray-900">{{ now()->format('H:i:s') }}</div>
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
                            <x-fas-clock class="w-16 h-16 mx-auto mb-4 text-gray-400" />
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
                                <x-fas-clock class="w-5 h-5 mr-2" />
                                Record Attendance
                            </a>
                            <a href="{{ route('employee.attendance.history') }}"
                                class="w-full flex items-center justify-center px-4 py-3 bg-green-50 hover:bg-green-100 text-green-700 font-medium rounded-lg transition-colors">
                                <x-fas-clipboard-list class="w-5 h-5 mr-2" />
                                View History
                            </a>
                            <a href="{{ route('settings.profile.edit') }}"
                                class="w-full flex items-center justify-center px-4 py-3 bg-purple-50 hover:bg-purple-100 text-purple-700 font-medium rounded-lg transition-colors">
                                <x-fas-user class="w-5 h-5 mr-2" />
                                Update Profile
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Assigned Locations -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Location</h3>
                        @if (!$employee->location)
                            <div class="text-center py-4">
                                <x-fas-map-marker-alt class="w-12 h-12 mx-auto mb-2 text-gray-400" />
                                <p class="text-gray-500 text-sm">No location assigned</p>
                            </div>
                        @else
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $employee->location->name }}</h4>
                                        <p class="text-sm text-gray-600">
                                            {{ Str::limit($employee->location->address, 30) }}</p>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                 {{ $employee->location->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($employee->location->status) }}
                                    </span>
                                </div>
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
                            <x-fas-clipboard-list class="w-12 h-12 mx-auto mb-2 text-gray-400" />
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
                                                    <x-fas-check class="w-4 h-4 text-green-600" />
                                                </div>
                                            @elseif($attendance->status === 'late')
                                                <div
                                                    class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                    <x-fas-clock class="w-4 h-4 text-yellow-600" />
                                                </div>
                                            @else
                                                <div
                                                    class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                    <x-fas-times class="w-4 h-4 text-red-600" />
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
        // Auto-refresh current time with seconds
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });

            const clockDisplay = document.getElementById('realtime-clock');
            if (clockDisplay) {
                clockDisplay.textContent = timeString;
            }
        }

        // Dismiss alert function
        function dismissAlert() {
            const alert = document.querySelector('.bg-yellow-50');
            if (alert) {
                alert.style.display = 'none';
                // Store dismissal in session storage to not show again during this session
                sessionStorage.setItem('faceEnrollmentAlertDismissed', 'true');
            }
        }

        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize realtime clock
            updateTime();
            setInterval(updateTime, 1000);

            // Check if alert was dismissed in this session
            if (sessionStorage.getItem('faceEnrollmentAlertDismissed') === 'true') {
                const alert = document.querySelector('.bg-yellow-50');
                if (alert) {
                    alert.style.display = 'none';
                }
            }
        });
    </script>
</x-layouts.app>
