<x-layouts.app>
    <x-slot name="title">Edit Face Gallery</x-slot>

    <!-- Flash Messages -->
    <x-flash-messages />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Edit Face Gallery</h1>
                            <p class="text-gray-600">Update face gallery information</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.face-galleries.show', $faceGallery) }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-eye class="w-5 h-5 mr-2" />
                                View Details
                            </a>
                            <a href="{{ route('admin.face-galleries.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-arrow-left class="w-5 h-5 mr-2" />
                                Back to Galleries
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.face-galleries.update', $faceGallery) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Gallery Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Gallery Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <x-forms.input label="Gallery ID *" name="gallery_id" type="text"
                                        placeholder="e.g., main_office_gallery" value="{{ old('gallery_id', $faceGallery->gallery_id) }}"
                                        required readonly />
                                    <p class="mt-1 text-sm text-gray-500">
                                        <x-fas-info-circle class="w-4 h-4 inline mr-1" />
                                        Gallery ID cannot be changed after creation.
                                    </p>
                                </div>

                                <div class="md:col-span-2">
                                    <x-forms.input label="Gallery Name *" name="name" type="text"
                                        placeholder="e.g., Main Office Gallery" value="{{ old('name', $faceGallery->name) }}"
                                        required />
                                </div>

                                <div class="md:col-span-2">
                                    <x-forms.textarea label="Description" name="description"
                                        placeholder="Optional description of the gallery..." rows="3">{{ old('description', $faceGallery->description) }}</x-forms.textarea>
                                </div>

                                <div>
                                    <x-forms.select label="Location" name="location_id" 
                                        :options="$locations->pluck('name', 'id')->prepend('Select Location (Optional)', '')" 
                                        :selected="old('location_id', $faceGallery->location_id)" />
                                </div>

                                <div>
                                    <x-forms.select label="Status *" name="status" 
                                        :options="['active' => 'Active', 'inactive' => 'Inactive']" 
                                        :selected="old('status', $faceGallery->status)" required />
                                </div>
                            </div>
                        </div>

                        <!-- Gallery Statistics -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Gallery Statistics</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <x-fas-users class="w-8 h-8 text-blue-600 mr-3" />
                                        <div>
                                            <p class="text-sm text-gray-500">Enrolled Faces</p>
                                            <p class="text-2xl font-bold text-gray-900">{{ $faceGallery->enrolled_faces_count ?? 0 }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <x-fas-user-check class="w-8 h-8 text-green-600 mr-3" />
                                        <div>
                                            <p class="text-sm text-gray-500">Employees</p>
                                            <p class="text-2xl font-bold text-gray-900">{{ $faceGallery->employees_count ?? 0 }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <x-fas-chart-line class="w-8 h-8 text-purple-600 mr-3" />
                                        <div>
                                            <p class="text-sm text-gray-500">Verifications</p>
                                            <p class="text-2xl font-bold text-gray-900">{{ $faceGallery->verifications_count ?? 0 }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- API Integration Info -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-medium text-blue-800 mb-2">
                                <x-fas-info-circle class="w-4 h-4 mr-1 inline" />
                                Face API Integration
                            </h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Changes will be applied to the local database only</li>
                                <li>• Gallery ID cannot be changed once created</li>
                                <li>• Use the sync function to update Face API data</li>
                                <li>• Status changes affect local operations only</li>
                            </ul>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.face-galleries.show', $faceGallery) }}"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                Update Gallery
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>