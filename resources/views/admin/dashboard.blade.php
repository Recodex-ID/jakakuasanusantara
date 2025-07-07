<x-layouts.app>
    <x-slot name="title">Admin Dashboard</x-slot>

    <!-- Flash Messages -->
    <x-flash-messages />

    <div class="py-6">
        <div class="container mx-auto sm:px-6 lg:px-8">
            <!-- Dashboard Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                            <p class="text-gray-600">Face Recognition Attendance System - PT Jaka Kuasa Nusantara</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ now()->format('H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Employees -->
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
                                <div class="text-2xl font-bold text-gray-900">{{ $stats['total_employees'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Locations -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <x-fas-map-marker-alt class="w-5 h-5 text-green-600" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Active Locations</div>
                                <div class="text-2xl font-bold text-gray-900">{{ $stats['total_locations'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Attendance -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <x-fas-clock class="w-5 h-5 text-yellow-600" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Today's Attendance</div>
                                <div class="text-2xl font-bold text-gray-900">{{ $stats['today_attendance'] }}</div>
                                <div class="text-xs text-gray-500">{{ $stats['attendance_rate'] }}% attendance rate</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Face Enrollment -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <x-fas-camera class="w-5 h-5 text-purple-600" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Face Enrollment</div>
                                <div class="text-2xl font-bold text-gray-900">{{ $stats['enrolled_employees'] }}/{{ $stats['total_employees'] }}</div>
                                <div class="text-xs text-gray-500">{{ $stats['face_enrollment_rate'] }}% enrolled</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Status Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Today's Status Overview</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $stats['today_present'] }}</div>
                            <div class="text-sm text-green-600">Present</div>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-600">{{ $stats['today_late'] }}</div>
                            <div class="text-sm text-yellow-600">Late</div>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">{{ $stats['today_absent'] }}</div>
                            <div class="text-sm text-red-600">Absent</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions and Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Quick Actions Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="{{ route('admin.employees.create') }}"
                                class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                <x-fas-user-plus class="w-6 h-6 text-blue-600 mr-3" />
                                <span class="text-sm font-medium text-blue-900">Add Employee</span>
                            </a>

                            <a href="{{ route('admin.locations.create') }}"
                                class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                                <x-fas-map-marker-alt class="w-6 h-6 text-green-600 mr-3" />
                                <span class="text-sm font-medium text-green-900">Add Location</span>
                            </a>

                            <a href="{{ route('admin.attendances.monitor') }}"
                                class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                                <x-fas-chart-bar class="w-6 h-6 text-yellow-600 mr-3" />
                                <span class="text-sm font-medium text-yellow-900">Monitor Today</span>
                            </a>

                            <a href="{{ route('admin.attendances.index') }}"
                                class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                                <x-fas-list class="w-6 h-6 text-purple-600 mr-3" />
                                <span class="text-sm font-medium text-purple-900">View Attendance</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Attendance Activity</h3>
                        <div class="space-y-3">
                            @forelse($recentActivity as $activity)
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-{{ $activity['icon_color'] }}-100 rounded-full flex items-center justify-center">
                                            @if($activity['icon_color'] === 'green')
                                                <x-fas-sign-in-alt class="w-4 h-4 text-green-600" />
                                            @else
                                                <x-fas-sign-out-alt class="w-4 h-4 text-red-600" />
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">{{ $activity['employee_name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $activity['action'] }} at {{ $activity['location'] }}</p>
                                        @if($activity['method'] === 'face_recognition')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                <x-fas-camera class="w-3 h-3 mr-1" />
                                                Face Recognition
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $activity['time'] }}</div>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 py-4">
                                    <x-fas-clock class="w-8 h-8 mx-auto mb-2 text-gray-400" />
                                    <p>No recent activity today</p>
                                </div>
                            @endforelse
                        </div>
                        @if($recentActivity->count() > 0)
                            <div class="mt-4 text-center">
                                <a href="{{ route('admin.attendances.index') }}" 
                                    class="text-sm text-blue-600 hover:text-blue-800">
                                    View all attendance records →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Weekly Attendance Trend -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Weekly Attendance Trend</h3>
                        <div class="space-y-3">
                            @foreach($chartData['weekly_trend'] as $day)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="text-sm font-medium text-gray-900 w-12">{{ $day['day'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $day['date'] }}</div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="text-sm text-gray-600">{{ $day['attended'] }}/{{ $day['total'] }}</div>
                                        <div class="w-32 bg-gray-200 rounded-full h-2">
                                            @php
                                                $percentage = $day['total'] > 0 ? ($day['attended'] / $day['total']) * 100 : 0;
                                            @endphp
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <div class="text-xs text-gray-500">{{ round($percentage) }}%</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Location Usage -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Location Usage This Week</h3>
                        <div class="space-y-3">
                            @forelse($chartData['location_usage'] as $location)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                                        <div class="text-sm font-medium text-gray-900">{{ $location->name }}</div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="text-sm text-gray-600">{{ $location->count }} visits</div>
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            @php
                                                $maxCount = $chartData['location_usage']->max('count');
                                                $percentage = $maxCount > 0 ? ($location->count / $maxCount) * 100 : 0;
                                            @endphp
                                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 py-4">
                                    <x-fas-map-marker-alt class="w-8 h-8 mx-auto mb-2 text-gray-400" />
                                    <p>No location data available</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>