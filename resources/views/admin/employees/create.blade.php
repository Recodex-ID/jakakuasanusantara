<x-layouts.app>
    <x-slot name="title">Create Employee</x-slot>

    <!-- Flash Messages -->
    <x-flash-messages />

    <div class="py-6">
        <div class="container mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Create New Employee</h1>
                            <p class="text-gray-600">Add a new employee to the attendance system</p>
                        </div>
                        <a href="{{ route('admin.employees.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                            <x-fas-arrow-left class="w-5 h-5 mr-2" />
                            Back to Employees
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.employees.store') }}" class="space-y-6">
                        @csrf

                        <!-- User Account Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">User Account Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-forms.input label="Full Name *" name="name" type="text"
                                        placeholder="Enter full name" value="{{ old('name') }}" required />
                                </div>

                                <div>
                                    <x-forms.input label="Email Address *" name="email" type="email"
                                        placeholder="your@email.com" value="{{ old('email') }}" required />
                                </div>

                                <div>
                                    <x-forms.input label="Username *" name="username" type="text"
                                        placeholder="Enter username" value="{{ old('username') }}" required />
                                </div>

                                <div>
                                    <x-forms.input label="Password *" name="password" type="password"
                                        placeholder="••••••••" required />
                                </div>

                                <div>
                                    <x-forms.input label="Confirm Password *" name="password_confirmation"
                                        type="password" placeholder="••••••••" required />
                                </div>

                                <div>
                                    <x-forms.select label="Role *" name="role" :options="['employee' => 'Employee', 'admin' => 'Admin']" :selected="old('role', 'employee')"
                                        required />
                                </div>
                            </div>
                        </div>

                        <!-- Employee Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Employee Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Employee ID</label>
                                    <p class="text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded-md border">
                                        Employee ID will be generated automatically
                                    </p>
                                </div>

                                <div>
                                    <x-forms.input label="Phone Number" name="phone" type="text"
                                        placeholder="e.g., +62812345678" value="{{ old('phone') }}" />
                                </div>

                                <div>
                                    <x-forms.input label="Department" name="department" type="text"
                                        placeholder="e.g., Information Technology" value="{{ old('department') }}" />
                                </div>

                                <div>
                                    <x-forms.input label="Position" name="position" type="text"
                                        placeholder="e.g., Software Developer" value="{{ old('position') }}" />
                                </div>

                                <div>
                                    <x-forms.input label="Date of Birth" name="date_of_birth" type="date"
                                        value="{{ old('date_of_birth') }}" />
                                </div>

                                <div>
                                    <x-forms.select label="Gender" name="gender" placeholder="Select Gender"
                                        :options="['male' => 'Male', 'female' => 'Female']" :selected="old('gender')" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-forms.textarea label="Address" name="address" placeholder="Full address..."
                                        rows="3" value="{{ old('address') }}" />
                                </div>
                            </div>
                        </div>

                        <!-- Location Assignment -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Location Assignment</h3>
                            <div>
                                <x-forms.select label="Assigned Location" name="location_id" :options="$locations->pluck('name', 'id')->prepend('-- Select Location --', '')"
                                    :selected="old('location_id')" />
                            </div>
                            @if ($locations->isEmpty())
                                <div class="text-center py-6 text-gray-500">
                                    <p>No locations available. Please create locations first.</p>
                                    <a href="{{ route('admin.locations.create') }}"
                                        class="mt-2 inline-flex items-center text-blue-600 hover:text-blue-800">
                                        Create Location
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Work Schedule -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Work Schedule</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Working Hours -->
                                <div>
                                    <x-forms.input label="Work Start Time" name="work_start_time" type="time"
                                        value="{{ old('work_start_time', '09:00') }}" />
                                </div>

                                <div>
                                    <x-forms.input label="Work End Time" name="work_end_time" type="time"
                                        value="{{ old('work_end_time', '17:00') }}" />
                                </div>

                                <!-- Late Tolerance -->
                                <div>
                                    <x-forms.input label="Late Tolerance (Minutes)" name="late_tolerance_minutes"
                                        type="number" placeholder="15" min="0" max="60"
                                        value="{{ old('late_tolerance_minutes', '15') }}" />
                                </div>

                                <!-- Work Days -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Work Days</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        @php
                                            $days = [
                                                '1' => 'Monday',
                                                '2' => 'Tuesday',
                                                '3' => 'Wednesday',
                                                '4' => 'Thursday',
                                                '5' => 'Friday',
                                                '6' => 'Saturday',
                                                '0' => 'Sunday',
                                            ];
                                            $defaultWorkDays = old('work_days', ['1', '2', '3', '4', '5']);
                                        @endphp
                                        @foreach ($days as $value => $label)
                                            <label class="flex items-center">
                                                <input type="checkbox" name="work_days[]"
                                                    value="{{ $value }}"
                                                    {{ in_array($value, $defaultWorkDays) ? 'checked' : '' }}
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4">
                                                <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.employees.index') }}"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                                Cancel
                            </a>
                            <x-button type="primary" buttonType="submit">
                                Create Employee
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('0')) {
                value = '+62' + value.substring(1);
            } else if (value.startsWith('62')) {
                value = '+' + value;
            } else if (!value.startsWith('+62') && value.length > 0) {
                value = '+62' + value;
            }
            e.target.value = value;
        });
    </script>
</x-layouts.app>
