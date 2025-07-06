<x-layouts.app>
    <x-slot name="title">Employee Details</x-slot>

    <div class="py-6">
        <div class="container mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $employee->user->name }}</h1>
                            <p class="text-gray-600">Employee ID: {{ $employee->employee_id }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.employees.edit', $employee) }}"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-edit class="w-5 h-5 mr-2" />
                                Edit Employee
                            </a>
                            <a href="{{ route('admin.employees.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-arrow-left class="w-5 h-5 mr-2" />
                                Back to Employees
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Employee Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->user->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Employee ID</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->employee_id }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->user->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Username</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->user->username ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Role</label>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                                        @if ($employee->user->role === 'admin') bg-blue-100 text-blue-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst($employee->user->role) }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->phone ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Gender</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $employee->gender ? ucfirst($employee->gender) : '-' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ $employee->date_of_birth ? $employee->date_of_birth->format('F j, Y') : '-' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                                                 {{ $employee->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($employee->status) }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Face Status</label>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                                                 {{ $employee->isFaceEnrolled() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $employee->isFaceEnrolled() ? 'Enrolled' : 'Not Enrolled' }}
                                    </span>
                                </div>
                                @if ($employee->address)
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Address</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $employee->address }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Work Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Work Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Department</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->department ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Position</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->position ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Joined Date</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->created_at->format('F j, Y') }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Account Status</label>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                                                 {{ $employee->user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $employee->user->email_verified_at ? 'Email Verified' : 'Email Unverified' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assigned Location -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Location</h3>
                            @if ($employee->location)
                                <div
                                    class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $employee->location->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $employee->location->address }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-gray-500">No location assigned.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Work Schedule -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Work Schedule</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Work Hours</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $employee->work_start_time ? \Carbon\Carbon::createFromFormat('H:i:s', $employee->work_start_time)->format('H:i') : '09:00' }}
                                        -
                                        {{ $employee->work_end_time ? \Carbon\Carbon::createFromFormat('H:i:s', $employee->work_end_time)->format('H:i') : '17:00' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Late Tolerance</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $employee->late_tolerance_minutes ?? 15 }}
                                        minutes</span>
                                </div>
                                <div class="flex items-start justify-between">
                                    <span class="text-sm text-gray-600">Work Days</span>
                                    <div class="text-right">
                                        @if ($employee->work_days)
                                            @foreach ($employee->getWorkDaysNames() as $day)
                                                <span
                                                    class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded mr-1 mb-1">{{ $day }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-sm text-gray-500">Not configured</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics & Actions -->
                <div class="space-y-6">
                    <!-- Statistics -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Attendance Statistics</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">This Month</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $attendanceStats['this_month'] ?? 0 }}
                                        days</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Total Present</span>
                                    <span
                                        class="text-sm font-medium text-green-600">{{ $attendanceStats['total_present'] ?? 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Total Absent</span>
                                    <span
                                        class="text-sm font-medium text-red-600">{{ $attendanceStats['total_absent'] ?? 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Late Arrivals</span>
                                    <span
                                        class="text-sm font-medium text-yellow-600">{{ $attendanceStats['total_late'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <a href="{{ route('admin.face-enrollment.show', $employee) }}"
                                    class="w-full flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                                    <x-fas-camera class="w-5 h-5 mr-2" />
                                    Enroll Face
                                </a>
                                <a href="{{ route('admin.attendances.employee', $employee) }}"
                                    class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    <x-fas-clock class="w-5 h-5 mr-2" />
                                    View Attendance
                                </a>
                                <a href="{{ route('admin.employees.edit', $employee) }}"
                                    class="w-full flex items-center justify-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors">
                                    <x-fas-edit class="w-5 h-5 mr-2" />
                                    Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                            @if ($recentAttendances->isEmpty())
                                <p class="text-gray-500 text-sm">No recent activity.</p>
                            @else
                                <div class="space-y-3">
                                    @foreach ($recentAttendances as $attendance)
                                        <div class="flex items-center justify-between text-sm">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $attendance->location->name }}
                                                </p>
                                                <p class="text-gray-600">
                                                    {{ $attendance->date?->format('M j, Y') ?? '-' }}</p>
                                            </div>
                                            <div class="text-right">
                                                @if ($attendance->check_in)
                                                    <p class="text-green-600">In:
                                                        {{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }}
                                                    </p>
                                                @endif
                                                @if ($attendance->check_out)
                                                    <p class="text-red-600">Out:
                                                        {{ $attendance->check_out ? $attendance->check_out->format('H:i') : '-' }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
