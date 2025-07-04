<x-layouts.app>
    <x-slot name="title">Employee Details</x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $employee->full_name }}</h1>
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
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->full_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Employee ID</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->employee_id }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">NIK</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $employee->nik }}</p>
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
                                        {{ $employee->user->email_verified_at ? 'Verified' : 'Unverified' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assigned Locations -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Locations</h3>
                            @if ($employee->locations->isEmpty())
                                <p class="text-gray-500">No locations assigned.</p>
                            @else
                                <div class="space-y-3">
                                    @foreach ($employee->locations as $location)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $location->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $location->address }}</p>
                                            </div>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                         {{ $location->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($location->status) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
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
                                <button onclick="enrollFace({{ $employee->id }})"
                                    class="w-full flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                                    <x-fas-camera class="w-5 h-5 mr-2" />
                                    Enroll Face
                                </button>
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
                                                <p class="text-gray-600">{{ $attendance->date->format('M j, Y') }}</p>
                                            </div>
                                            <div class="text-right">
                                                @if ($attendance->check_in)
                                                    <p class="text-green-600">In:
                                                        {{ $attendance->check_in->format('H:i') }}</p>
                                                @endif
                                                @if ($attendance->check_out)
                                                    <p class="text-red-600">Out:
                                                        {{ $attendance->check_out->format('H:i') }}</p>
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

    <!-- Face Enrollment Modal -->
    <div id="faceEnrollmentModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900">Enroll Face</h3>
                <p class="text-sm text-gray-500 mt-2">Take a photo for face recognition enrollment</p>

                <div class="mt-4">
                    <video id="video" class="w-full h-64 bg-gray-200 rounded-lg" autoplay></video>
                    <canvas id="canvas" class="hidden"></canvas>
                </div>

                <div class="mt-4 flex justify-center space-x-3">
                    <button onclick="capturePhoto()"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                        Capture Photo
                    </button>
                    <button onclick="closeEnrollmentModal()"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let stream = null;

        function enrollFace(employeeId) {
            document.getElementById('faceEnrollmentModal').classList.remove('hidden');
            startCamera();
        }

        function closeEnrollmentModal() {
            document.getElementById('faceEnrollmentModal').classList.add('hidden');
            stopCamera();
        }

        function startCamera() {
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(function(mediaStream) {
                    stream = mediaStream;
                    document.getElementById('video').srcObject = stream;
                })
                .catch(function(err) {
                    alert('Camera access denied or not available');
                });
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        function capturePhoto() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0);

            canvas.toBlob(function(blob) {
                const reader = new FileReader();
                reader.onloadend = function() {
                    const base64data = reader.result.split(',')[1];
                    submitFaceEnrollment(base64data);
                };
                reader.readAsDataURL(blob);
            }, 'image/jpeg', 0.8);
        }

        function submitFaceEnrollment(faceImage) {
            const galleryId = 'default_gallery';

            fetch(`/admin/employees/{{ $employee->id }}/enroll-face`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        face_image: faceImage,
                        gallery_id: galleryId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Face enrolled successfully!');
                    } else {
                        alert('Face enrollment failed: ' + data.message);
                    }
                    closeEnrollmentModal();
                })
                .catch(error => {
                    alert('An error occurred during face enrollment');
                    closeEnrollmentModal();
                });
        }
    </script>
</x-layouts.app>
