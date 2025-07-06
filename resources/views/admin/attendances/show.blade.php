<x-layouts.app>
    <x-slot name="title">Attendance Details</x-slot>

    <!-- Flash Messages -->
    <x-flash-messages />

    <div class="py-6">
        <div class="container mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Attendance Details</h1>
                            <p class="text-gray-600">{{ $attendance->employee->user->name }} - {{ $attendance->date->format('F j, Y') }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.attendances.edit', $attendance) }}"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-edit class="w-5 h-5 mr-2" />
                                Edit Attendance
                            </a>
                            <a href="{{ route('admin.attendances.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-arrow-left class="w-5 h-5 mr-2" />
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Attendance Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Employee</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $attendance->employee->user->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Employee ID</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $attendance->employee->employee_id }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Location</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $attendance->location->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $attendance->date->format('F j, Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                                             @if($attendance->status === 'present') bg-green-100 text-green-800
                                             @elseif($attendance->status === 'late') bg-yellow-100 text-yellow-800
                                             @elseif($attendance->status === 'absent') bg-red-100 text-red-800
                                             @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </div>
                                @if($attendance->notes)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $attendance->notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Time Records -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Time Records</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <x-fas-sign-in-alt class="w-6 h-6 text-green-600" />
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-green-900">Check In</h4>
                                            @if($attendance->check_in)
                                                <p class="text-lg font-bold text-green-900">{{ $attendance->check_in->format('H:i:s') }}</p>
                                                <p class="text-sm text-green-700">{{ $attendance->check_in->format('F j, Y') }}</p>
                                            @else
                                                <p class="text-sm text-green-700">Not checked in</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-red-50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <x-fas-sign-out-alt class="w-6 h-6 text-red-600" />
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-red-900">Check Out</h4>
                                            @if($attendance->check_out)
                                                <p class="text-lg font-bold text-red-900">{{ $attendance->check_out->format('H:i:s') }}</p>
                                                <p class="text-sm text-red-700">{{ $attendance->check_out->format('F j, Y') }}</p>
                                            @else
                                                <p class="text-sm text-red-700">Not checked out</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($attendance->check_in && $attendance->check_out)
                                <div class="mt-6 bg-blue-50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <x-fas-clock class="w-6 h-6 text-blue-600" />
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-blue-900">Working Hours</h4>
                                            <p class="text-lg font-bold text-blue-900">
                                                {{ $attendance->check_in->diffForHumans($attendance->check_out, true) }}
                                            </p>
                                            <p class="text-sm text-blue-700">Total time worked</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Activity Logs -->
                <div class="space-y-6">
                    <!-- Employee Quick Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Employee</h3>
                            <div class="text-center">
                                <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-3">
                                    <span class="text-xl font-medium text-blue-900">
                                        {{ $attendance->employee->user->initials() }}
                                    </span>
                                </div>
                                <h4 class="font-medium text-gray-900">{{ $attendance->employee->user->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $attendance->employee->employee_id }}</p>
                                @if($attendance->employee->department)
                                    <p class="text-sm text-gray-600">{{ $attendance->employee->department }}</p>
                                @endif
                                @if($attendance->employee->position)
                                    <p class="text-sm text-gray-600">{{ $attendance->employee->position }}</p>
                                @endif
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.employees.show', $attendance->employee) }}"
                                    class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                    <x-fas-user class="w-4 h-4 mr-2" />
                                    View Employee
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Logs -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Logs</h3>
                            @if($attendance->attendanceLogs->isEmpty())
                                <p class="text-gray-500 text-sm">No activity logs available.</p>
                            @else
                                <div class="space-y-3">
                                    @foreach($attendance->attendanceLogs as $log)
                                        <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                            <div class="flex-shrink-0">
                                                @if($log->action === 'check_in')
                                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                        <x-fas-sign-in-alt class="w-4 h-4 text-green-600" />
                                                    </div>
                                                @else
                                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                        <x-fas-sign-out-alt class="w-4 h-4 text-red-600" />
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</p>
                                                <p class="text-sm text-gray-600">{{ $log->action_time->format('H:i:s - M j, Y') }}</p>
                                                <p class="text-xs text-gray-500">Method: {{ ucfirst(str_replace('_', ' ', $log->method)) }}</p>
                                                @if($log->notes)
                                                    <p class="text-xs text-gray-500">{{ $log->notes }}</p>
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