<x-layouts.app>
    <x-slot name="title">{{ $employee->user->name }} - Attendance Records</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $employee->user->name }}</h1>
                            <p class="text-gray-600">Attendance Records - Employee ID: {{ $employee->employee_id }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.employees.show', $employee) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-user class="w-5 h-5 mr-2" />
                                Back to Employee
                            </a>
                            <a href="{{ route('admin.attendances.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-clock class="w-5 h-5 mr-2" />
                                All Attendances
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $attendances->where('status', 'present')->count() }}</div>
                            <div class="text-sm text-gray-600">Present</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $attendances->where('status', 'absent')->count() }}</div>
                            <div class="text-sm text-gray-600">Absent</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ $attendances->where('status', 'late')->count() }}</div>
                            <div class="text-sm text-gray-600">Late</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $attendances->total() }}</div>
                            <div class="text-sm text-gray-600">Total Records</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Records -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Attendance Records</h3>
                    
                    @if ($attendances->isEmpty())
                        <div class="text-center py-8">
                            <x-fas-calendar-times class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                            <p class="text-gray-500">No attendance records found for this employee.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($attendances as $attendance)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $attendance->date ? $attendance->date->format('M j, Y') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $attendance->location->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $attendance->check_out ? $attendance->check_out->format('H:i') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800' : 
                                                       ($attendance->status === 'absent' ? 'bg-red-100 text-red-800' : 
                                                       ($attendance->status === 'late' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                                    {{ ucfirst($attendance->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('admin.attendances.show', $attendance) }}"
                                                        class="text-blue-600 hover:text-blue-900">
                                                        <x-fas-eye class="w-4 h-4" />
                                                    </a>
                                                    <a href="{{ route('admin.attendances.edit', $attendance) }}"
                                                        class="text-yellow-600 hover:text-yellow-900">
                                                        <x-fas-edit class="w-4 h-4" />
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $attendances->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>