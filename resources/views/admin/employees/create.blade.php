<x-layouts.app>
    <x-slot name="title">Create Employee</x-slot>

    <!-- Flash Messages -->
    <x-flash-messages />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                            </div>
                        </div>

                        <!-- Employee Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Employee Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-forms.input label="Employee ID *" name="employee_id" type="text"
                                        placeholder="e.g., EMP001" value="{{ old('employee_id') }}" required />
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
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Location Assignment</h3>
                            <div>
                                <x-forms.select 
                                    label="Assigned Location" 
                                    name="location_id" 
                                    :options="$locations->pluck('name', 'id')->prepend('-- Select Location --', '')" 
                                    :selected="old('location_id')" />
                                <p class="mt-1 text-sm text-gray-500">Employee will be able to check attendance at this location</p>
                            </div>
                            @if($locations->isEmpty())
                                <div class="text-center py-6 text-gray-500">
                                    <p>No locations available. Please create locations first.</p>
                                    <a href="{{ route('admin.locations.create') }}"
                                        class="mt-2 inline-flex items-center text-blue-600 hover:text-blue-800">
                                        Create Location
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.employees.index') }}"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                Create Employee
                            </button>
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
