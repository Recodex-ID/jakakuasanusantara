<x-layouts.app>
    <x-slot name="title">{{ $faceGallery->name }} - Face Gallery</x-slot>

    <!-- Flash Messages -->
    <x-flash-messages />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $faceGallery->name }}</h1>
                            <p class="text-gray-600">Gallery ID: {{ $faceGallery->gallery_id }}</p>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $faceGallery->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($faceGallery->status) }}
                                </span>
                                @if($faceGallery->location)
                                    <span class="inline-flex items-center text-sm text-gray-600">
                                        <x-fas-map-marker-alt class="w-4 h-4 mr-1" />
                                        {{ $faceGallery->location->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button onclick="syncGallery()" 
                                class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-sync class="w-5 h-5 mr-2" />
                                Sync with API
                            </button>
                            <a href="{{ route('admin.face-galleries.edit', $faceGallery) }}"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-edit class="w-5 h-5 mr-2" />
                                Edit Gallery
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

            <!-- Gallery Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <x-fas-users class="w-8 h-8 text-blue-600 mr-3" />
                            <div>
                                <p class="text-sm text-gray-500">Enrolled Faces</p>
                                <p class="text-2xl font-bold text-gray-900">{{ count($enrolledFaces) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <x-fas-user-check class="w-8 h-8 text-green-600 mr-3" />
                            <div>
                                <p class="text-sm text-gray-500">Employees</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $faceGallery->employees_count ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <x-fas-chart-line class="w-8 h-8 text-purple-600 mr-3" />
                            <div>
                                <p class="text-sm text-gray-500">Verifications</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $faceGallery->verifications_count ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <x-fas-clock class="w-8 h-8 text-orange-600 mr-3" />
                            <div>
                                <p class="text-sm text-gray-500">Created</p>
                                <p class="text-lg font-bold text-gray-900">{{ $faceGallery->created_at->format('M j, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gallery Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Gallery Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Gallery ID</dt>
                                    <dd class="text-sm text-gray-900 font-mono">{{ $faceGallery->gallery_id }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="text-sm text-gray-900">{{ $faceGallery->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $faceGallery->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($faceGallery->status) }}
                                        </span>
                                    </dd>
                                </div>
                                @if($faceGallery->location)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                                        <dd class="text-sm text-gray-900">{{ $faceGallery->location->name }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                        <div>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                                    <dd class="text-sm text-gray-900">{{ $faceGallery->created_at->format('M j, Y g:i A') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="text-sm text-gray-900">{{ $faceGallery->updated_at->format('M j, Y g:i A') }}</dd>
                                </div>
                                @if($faceGallery->last_sync_at)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Last Sync</dt>
                                        <dd class="text-sm text-gray-900">{{ $faceGallery->last_sync_at->format('M j, Y g:i A') }}</dd>
                                    </div>
                                @endif
                                @if($faceGallery->description)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                                        <dd class="text-sm text-gray-900">{{ $faceGallery->description }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrolled Faces -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Enrolled Faces</h3>
                        <span class="text-sm text-gray-500">{{ count($enrolledFaces) }} faces enrolled</span>
                    </div>
                    
                    @if(count($enrolledFaces) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Face ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Label</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quality</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($enrolledFaces as $face)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                                {{ $face['face_id'] ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $face['label'] ?? 'No Label' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if(isset($face['quality']))
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        {{ $face['quality'] >= 0.8 ? 'bg-green-100 text-green-800' : 
                                                           ($face['quality'] >= 0.6 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                        {{ number_format($face['quality'] * 100, 1) }}%
                                                    </span>
                                                @else
                                                    <span class="text-gray-500">Unknown</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Enrolled
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <button onclick="viewFace('{{ $face['face_id'] ?? '' }}')" 
                                                    class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                                                <button onclick="deleteFace('{{ $face['face_id'] ?? '' }}')" 
                                                    class="text-red-600 hover:text-red-900">Delete</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-fas-user-slash class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No faces enrolled</h3>
                            <p class="text-gray-500 mb-4">This gallery doesn't have any enrolled faces yet.</p>
                            <p class="text-sm text-gray-500">
                                Add faces through the employee management system or use the Face API directly.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Danger Zone -->
            @if(count($enrolledFaces) == 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-red-900 mb-4">Danger Zone</h3>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-red-800">Delete this gallery</h4>
                                    <p class="text-sm text-red-600">This action cannot be undone. The gallery will be removed from both the database and Face API.</p>
                                </div>
                                <form method="POST" action="{{ route('admin.face-galleries.destroy', $faceGallery) }}" class="inline"
                                    onsubmit="return confirmDelete()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                                        Delete Gallery
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function syncGallery() {
            if (confirm('Are you sure you want to sync this gallery with the Face API?')) {
                // Show loading state
                const button = document.querySelector('button[onclick="syncGallery()"]');
                const originalText = button.innerHTML;
                button.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Syncing...';
                button.disabled = true;

                fetch('{{ route("admin.face-galleries.syncWithApi") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Gallery synced successfully!');
                        location.reload();
                    } else {
                        alert('Sync failed: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('An error occurred during sync');
                })
                .finally(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }
        }

        function viewFace(faceId) {
            alert('View face functionality not implemented yet. Face ID: ' + faceId);
        }

        function deleteFace(faceId) {
            if (confirm('Are you sure you want to delete this face? This action cannot be undone.')) {
                alert('Delete face functionality not implemented yet. Face ID: ' + faceId);
            }
        }

        function confirmDelete() {
            return confirm('Are you sure you want to delete this gallery? This action cannot be undone and will remove the gallery from both the database and Face API.');
        }
    </script>
</x-layouts.app>