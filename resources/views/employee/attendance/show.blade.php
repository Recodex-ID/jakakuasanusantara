<x-layouts.app>
    <x-slot name="title">Attendance Details</x-slot>

    <!-- Flash Messages -->
    <x-flash-messages />

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Attendance Details</h1>
                            <p class="text-gray-600">{{ $attendance->date->format('F j, Y - l') }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('employee.attendance.history') }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                Back to History
                            </a>
                            <a href="{{ route('employee.dashboard') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2V7z"></path>
                                </svg>
                                Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Attendance Summary -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Attendance Summary</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Location</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $attendance->location->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $attendance->location->address }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                                                 @if ($attendance->status === 'present') bg-green-100 text-green-800
                                                 @elseif($attendance->status === 'late') bg-yellow-100 text-yellow-800
                                                 @elseif($attendance->status === 'absent') bg-red-100 text-red-800
                                                 @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Time Details -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Time Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Check In -->
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-500 mb-2">Check In</div>
                                    @if ($attendance->check_in)
                                        <div class="text-2xl font-bold text-green-600">
                                            {{ $attendance->check_in->format('H:i:s') }}</div>
                                        <div class="text-sm text-gray-500">{{ $attendance->check_in->format('M j, Y') }}
                                        </div>
                                        @if ($attendance->check_in_lat && $attendance->check_in_lng)
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ number_format($attendance->check_in_lat, 6) }},
                                                {{ number_format($attendance->check_in_lng, 6) }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-2xl font-bold text-gray-400">--:--:--</div>
                                        <div class="text-sm text-gray-400">Not checked in</div>
                                    @endif
                                </div>

                                <!-- Check Out -->
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-500 mb-2">Check Out</div>
                                    @if ($attendance->check_out)
                                        <div class="text-2xl font-bold text-red-600">
                                            {{ $attendance->check_out->format('H:i:s') }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $attendance->check_out->format('M j, Y') }}</div>
                                        @if ($attendance->check_out_lat && $attendance->check_out_lng)
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ number_format($attendance->check_out_lat, 6) }},
                                                {{ number_format($attendance->check_out_lng, 6) }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-2xl font-bold text-gray-400">--:--:--</div>
                                        <div class="text-sm text-gray-400">Not checked out</div>
                                    @endif
                                </div>

                                <!-- Working Hours -->
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-500 mb-2">Working Hours</div>
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
                                        <div class="text-2xl font-bold text-blue-600">{{ $hours }}h
                                            {{ $minutes }}m</div>
                                        @if (!$attendance->check_out && $attendance->date->isToday())
                                            <div class="text-sm text-blue-500">Currently working</div>
                                        @elseif(!$attendance->check_out)
                                            <div class="text-sm text-red-500">Incomplete</div>
                                        @else
                                            <div class="text-sm text-gray-500">Total time</div>
                                        @endif
                                    @else
                                        <div class="text-2xl font-bold text-gray-400">0h 0m</div>
                                        <div class="text-sm text-gray-400">No check in</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Log -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Log</h3>
                            @if ($attendance->attendanceLogs->isEmpty())
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                        </path>
                                    </svg>
                                    <p class="text-gray-500">No activity logs available</p>
                                </div>
                            @else
                                <div class="space-y-4">
                                    @foreach ($attendance->attendanceLogs as $log)
                                        <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                                            <div class="flex-shrink-0">
                                                @if ($log->action === 'check_in')
                                                    <div
                                                        class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-green-600" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div
                                                        class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-red-600" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <h4 class="text-sm font-medium text-gray-900">
                                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                                    </h4>
                                                    <span class="text-xs text-gray-500">
                                                        {{ $log->action_time->format('H:i:s') }}
                                                    </span>
                                                </div>
                                                <div class="mt-1 text-sm text-gray-600">
                                                    Method: {{ ucfirst(str_replace('_', ' ', $log->method)) }}
                                                    @if ($log->face_verified)
                                                        <span
                                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Face Verified
                                                        </span>
                                                    @endif
                                                </div>
                                                @if ($log->face_similarity)
                                                    <div class="mt-1 text-xs text-gray-500">
                                                        Face similarity:
                                                        {{ number_format($log->face_similarity * 100, 1) }}%
                                                    </div>
                                                @endif
                                                @if ($log->latitude && $log->longitude)
                                                    <div class="mt-1 text-xs text-gray-500">
                                                        Location: {{ number_format($log->latitude, 6) }},
                                                        {{ number_format($log->longitude, 6) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Stats -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Date</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $attendance->date->format('M j, Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Day</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $attendance->date->format('l') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Activities</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $attendance->attendanceLogs->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Method</span>
                                    <span class="text-sm font-medium text-gray-900">Face Recognition</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Location Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $attendance->location->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Address</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $attendance->location->address }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Coordinates</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $attendance->location->latitude }}, {{ $attendance->location->longitude }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Allowed Radius</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $attendance->location->radius_meters }}
                                        meters</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                            <div class="space-y-3">
                                @if ($attendance->date->isToday() && $attendance->check_in && !$attendance->check_out)
                                    <a href="{{ route('employee.attendance.index') }}"
                                        class="w-full flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                            </path>
                                        </svg>
                                        Check Out Now
                                    </a>
                                @elseif($attendance->date->isToday() && !$attendance->check_in)
                                    <a href="{{ route('employee.attendance.index') }}"
                                        class="w-full flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                                            </path>
                                        </svg>
                                        Check In Now
                                    </a>
                                @endif

                                <a href="{{ route('employee.attendance.history') }}"
                                    class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                        </path>
                                    </svg>
                                    View All History
                                </a>

                                <a href="{{ route('employee.dashboard') }}"
                                    class="w-full flex items-center justify-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2V7z">
                                        </path>
                                    </svg>
                                    Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
