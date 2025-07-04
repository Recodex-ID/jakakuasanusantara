<x-layouts.app>
    <x-slot name="title">Edit Employee</x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Edit Employee</h1>
                            <p class="text-gray-600">Update employee information and settings</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.employees.show', $employee) }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                                View Details
                            </a>
                            <a href="{{ route('admin.employees.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Employees
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.employees.update', $employee) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- User Account Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">User Account Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name"
                                        value="{{ old('name', $employee->user->name) }}" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" name="email" id="email"
                                        value="{{ old('email', $employee->user->email) }}" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                        New Password <span class="text-gray-500">(leave blank to keep current)</span>
                                    </label>
                                    <input type="password" name="password" id="password"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation"
                                        class="block text-sm font-medium text-gray-700 mb-1">
                                        Confirm New Password
                                    </label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Employee Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Employee Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        Employee ID <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="employee_id" id="employee_id"
                                        value="{{ old('employee_id', $employee->employee_id) }}" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('employee_id') border-red-500 @enderror">
                                    @error('employee_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">
                                        NIK (National ID) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nik" id="nik"
                                        value="{{ old('nik', $employee->nik) }}" required maxlength="16"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('nik') border-red-500 @enderror">
                                    @error('nik')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Full Name (as per ID) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="full_name" id="full_name"
                                        value="{{ old('full_name', $employee->full_name) }}" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('full_name') border-red-500 @enderror">
                                    @error('full_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        Phone Number
                                    </label>
                                    <input type="text" name="phone" id="phone"
                                        value="{{ old('phone', $employee->phone) }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="department" class="block text-sm font-medium text-gray-700 mb-1">
                                        Department
                                    </label>
                                    <input type="text" name="department" id="department"
                                        value="{{ old('department', $employee->department) }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('department') border-red-500 @enderror">
                                    @error('department')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1">
                                        Position
                                    </label>
                                    <input type="text" name="position" id="position"
                                        value="{{ old('position', $employee->position) }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('position') border-red-500 @enderror">
                                    @error('position')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">
                                        Date of Birth
                                    </label>
                                    <input type="date" name="date_of_birth" id="date_of_birth"
                                        value="{{ old('date_of_birth', $employee->date_of_birth?->format('Y-m-d')) }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('date_of_birth') border-red-500 @enderror">
                                    @error('date_of_birth')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                                        Gender
                                    </label>
                                    <select name="gender" id="gender"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('gender') border-red-500 @enderror">
                                        <option value="">Select Gender</option>
                                        <option value="male"
                                            {{ old('gender', $employee->gender) === 'male' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="female"
                                            {{ old('gender', $employee->gender) === 'female' ? 'selected' : '' }}>
                                            Female</option>
                                    </select>
                                    @error('gender')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status" id="status" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                                        <option value="active"
                                            {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="inactive"
                                            {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                        Address
                                    </label>
                                    <textarea name="address" id="address" rows="3"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('address') border-red-500 @enderror">{{ old('address', $employee->address) }}</textarea>
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Location Assignment -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Location Assignment</h3>
                            <div class="space-y-3">
                                @forelse($locations as $location)
                                    <label
                                        class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer">
                                        <input type="checkbox" name="locations[]" value="{{ $location->id }}"
                                            {{ in_array($location->id, old('locations', $employee->locations->pluck('id')->toArray())) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $location->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $location->address }}</div>
                                        </div>
                                    </label>
                                @empty
                                    <div class="text-center py-6 text-gray-500">
                                        <p>No locations available. Please create locations first.</p>
                                        <a href="{{ route('admin.locations.create') }}"
                                            class="mt-2 inline-flex items-center text-blue-600 hover:text-blue-800">
                                            Create Location
                                        </a>
                                    </div>
                                @endforelse
                            </div>
                            @error('locations')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.employees.show', $employee) }}"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                Update Employee
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-format NIK input
        document.getElementById('nik').addEventListener('input', function(e) {
            const value = e.target.value.replace(/\D/g, '');
            e.target.value = value.substring(0, 16);
        });

        // Auto-copy name to full_name if full_name is empty
        document.getElementById('name').addEventListener('input', function(e) {
            const fullNameField = document.getElementById('full_name');
            if (!fullNameField.value.trim()) {
                fullNameField.value = e.target.value;
            }
        });

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
