<x-layouts.app>
    <x-slot name="title">Employee Management</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Employee Management</h1>
                            <p class="text-gray-600">Manage employee accounts and face recognition enrollment</p>
                        </div>
                        <a href="{{ route('admin.employees.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                            <x-fas-plus class="w-5 h-5 mr-2" />
                            Add Employee
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.employees.index') }}"
                        class="flex flex-wrap gap-4 items-end">
                        <div class="flex-1 min-w-64">
                            <x-forms.input label="Search" name="search" type="text"
                                placeholder="Search by name, employee ID, or department..."
                                value="{{ request('search') }}" />
                        </div>
                        <div class="min-w-32">
                            <x-forms.select label="Status" name="status" placeholder="All Status" :options="['active' => 'Active', 'inactive' => 'Inactive']"
                                :selected="request('status')" />
                        </div>
                        <div class="flex gap-2">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
                                Search
                            </button>
                            <a href="{{ route('admin.employees.index') }}"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-md transition-colors">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Employees Table -->
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
                                    Employee ID
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Department
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Locations
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
                            @forelse($employees as $employee)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-sm font-bold text-blue-500">
                                                        {{ $employee->user->initials() }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $employee->full_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $employee->user->username }}
                                                </div>
                                                <div class="text-xs text-gray-400">
                                                    {{ $employee->user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $employee->employee_id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $employee->department ?? '-' }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->position ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($employee->locations as $location)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ $location->name }}
                                                </span>
                                            @endforeach
                                            @if ($employee->locations->isEmpty())
                                                <span class="text-sm text-gray-500">No locations assigned</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                     {{ $employee->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($employee->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.employees.show', $employee) }}"
                                                class="text-blue-600 hover:text-blue-900" title="View Details">
                                                <x-fas-eye class="w-5 h-5" />
                                            </a>
                                            <a href="{{ route('admin.employees.edit', $employee) }}"
                                                class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <x-fas-edit class="w-5 h-5" />
                                            </a>
                                            <a href="{{ route('admin.employees.enroll-face', $employee) }}"
                                                class="text-purple-600 hover:text-purple-900" title="Enroll Face">
                                                <x-fas-camera class="w-5 h-5" />
                                            </a>
                                            <form method="POST"
                                                action="{{ route('admin.employees.destroy', $employee) }}"
                                                class="inline-flex" onsubmit="return confirmDelete()">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 flex items-center"
                                                    title="Delete">
                                                    <x-fas-trash class="w-5 h-5" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="text-gray-500">
                                            <x-fas-users class="w-12 h-12 mx-auto mb-4 text-gray-400" />
                                            <p class="text-lg font-medium">No employees found</p>
                                            <p class="text-sm">Get started by creating a new employee account.</p>
                                            <a href="{{ route('admin.employees.create') }}"
                                                class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                                Add First Employee
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($employees->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200">
                        {{ $employees->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this employee? This action cannot be undone.');
        }
    </script>
</x-layouts.app>
