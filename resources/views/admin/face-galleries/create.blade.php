<x-layouts.app>
    <x-slot name="title">Create Face Gallery</x-slot>

    <!-- Flash Messages -->
    <x-flash-messages />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Create Face Gallery</h1>
                            <p class="text-gray-600">Create a new face recognition gallery for the Biznet Face API</p>
                        </div>
                        <a href="{{ route('admin.face-galleries.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                            <x-fas-arrow-left class="w-5 h-5 mr-2" />
                            Back to Galleries
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.face-galleries.store') }}" class="space-y-6">
                        @csrf

                        <!-- Gallery Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Gallery Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <x-forms.input label="Gallery ID *" name="gallery_id" type="text"
                                        placeholder="e.g., main_office_gallery" value="{{ old('gallery_id') }}"
                                        required />
                                    <p class="mt-1 text-sm text-gray-500">
                                        <x-fas-info-circle class="w-4 h-4 inline mr-1" />
                                        Unique identifier for the gallery. Use alphanumeric characters, dots, underscores, or hyphens only.
                                    </p>
                                </div>

                                <div class="md:col-span-2">
                                    <x-forms.input label="Gallery Name *" name="name" type="text"
                                        placeholder="e.g., Main Office Gallery" value="{{ old('name') }}"
                                        required />
                                </div>

                                <div class="md:col-span-2">
                                    <x-forms.textarea label="Description" name="description"
                                        placeholder="Optional description of the gallery..." rows="3">{{ old('description') }}</x-forms.textarea>
                                </div>

                                <div>
                                    <x-forms.select label="Location" name="location_id" 
                                        :options="$locations->pluck('name', 'id')->prepend('Select Location (Optional)', '')" 
                                        :selected="old('location_id')" />
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
                                <li>• The gallery will be created in the Biznet Face API system</li>
                                <li>• Gallery ID must be unique across all galleries</li>
                                <li>• Once created, the Gallery ID cannot be changed</li>
                                <li>• You can add employee faces to this gallery after creation</li>
                            </ul>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.face-galleries.index') }}"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                Create Gallery
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>