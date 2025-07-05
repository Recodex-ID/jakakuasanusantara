<x-layouts.app>
    <x-slot name="title">Face Enrollment - {{ $employee->full_name }}</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Face Enrollment</h1>
                            <p class="text-gray-600">Register face recognition for {{ $employee->full_name }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.employees.show', $employee) }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-eye class="w-5 h-5 mr-2" />
                                View Details
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
                <!-- Face Enrollment Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Camera Setup</h3>

                            <!-- Camera View -->
                            <div class="mb-6">
                                <div class="relative bg-gray-100 rounded-lg overflow-hidden">
                                    <video id="video" class="w-full h-80 object-cover bg-gray-200" autoplay
                                        muted></video>
                                    <canvas id="canvas" class="hidden"></canvas>

                                    <!-- Camera overlay guides -->
                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <div
                                            class="w-64 h-80 border-2 border-dashed border-white opacity-50 rounded-lg flex items-center justify-center">
                                            <div class="text-white text-center">
                                                <x-fas-user class="w-12 h-12 mx-auto mb-2 opacity-60" />
                                                <p class="text-sm font-medium">Position face here</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Camera Status -->
                                <div id="cameraStatus" class="mt-4 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <x-fas-info-circle class="w-5 h-5 text-blue-500 mr-2" />
                                        <span class="text-sm text-gray-700">Click "Start Camera" to begin face
                                            enrollment</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Camera Controls -->
                            <div class="flex flex-wrap gap-3">
                                <button id="startCameraBtn" onclick="startCamera()"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                                    <x-fas-video class="w-4 h-4 mr-2 inline" />
                                    Start Camera
                                </button>

                                <button id="captureBtn" onclick="capturePhoto()" disabled
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                                    <x-fas-camera class="w-4 h-4 mr-2 inline" />
                                    Capture Photo
                                </button>

                                <button id="retakeBtn" onclick="retakePhoto()" style="display: none;"
                                    class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors">
                                    <x-fas-redo class="w-4 h-4 mr-2 inline" />
                                    Retake
                                </button>

                                <button id="stopCameraBtn" onclick="stopCamera()" disabled
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                                    <x-fas-stop class="w-4 h-4 mr-2 inline" />
                                    Stop Camera
                                </button>
                            </div>

                            <!-- Captured Photo Preview -->
                            <div id="photoPreview" class="mt-6 hidden">
                                <h4 class="text-md font-semibold text-gray-900 mb-3">Captured Photo</h4>
                                <div class="relative">
                                    <img id="capturedImage" class="w-full max-w-md h-64 object-cover rounded-lg border">
                                    <div class="absolute top-2 right-2">
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
                                            Ready to enroll
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Gallery Selection -->
                            <div class="mt-6">
                                <x-forms.select label="Face Gallery" name="gallery_id" id="gallerySelect"
                                    placeholder="Select Gallery" :options="[
                                        'default_gallery' => 'Default Gallery',
                                        'main_gallery' => 'Main Gallery',
                                        'backup_gallery' => 'Backup Gallery',
                                    ]" selected="default_gallery" />
                                <p class="text-sm text-gray-500 mt-1">Choose which gallery to enroll the face into</p>
                            </div>

                            <!-- Enrollment Button -->
                            <div class="mt-6">
                                <button id="enrollBtn" onclick="submitFaceEnrollment()" disabled
                                    class="w-full px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                                    <x-fas-user-plus class="w-5 h-5 mr-2 inline" />
                                    Enroll Face Recognition
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Info & Instructions -->
                <div class="space-y-6">
                    <!-- Employee Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Employee Information</h3>

                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                    <span class="text-lg font-medium text-blue-900">
                                        {{ $employee->user->initials() }}
                                    </span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $employee->full_name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $employee->employee_id }}</p>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Department:</span>
                                    <span class="text-gray-900">{{ $employee->department ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Position:</span>
                                    <span class="text-gray-900">{{ $employee->position ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <x-fas-lightbulb class="w-5 h-5 mr-2 inline text-yellow-500" />
                                Instructions
                            </h3>

                            <div class="space-y-3 text-sm">
                                <div class="flex items-start">
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full mr-3 mt-0.5">1</span>
                                    <p class="text-gray-700">Click <strong>"Start Camera"</strong> to activate the
                                        webcam</p>
                                </div>

                                <div class="flex items-start">
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full mr-3 mt-0.5">2</span>
                                    <p class="text-gray-700">Position the employee's face within the guide frame</p>
                                </div>

                                <div class="flex items-start">
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full mr-3 mt-0.5">3</span>
                                    <p class="text-gray-700">Ensure good lighting and clear face visibility</p>
                                </div>

                                <div class="flex items-start">
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full mr-3 mt-0.5">4</span>
                                    <p class="text-gray-700">Click <strong>"Capture Photo"</strong> when ready</p>
                                </div>

                                <div class="flex items-start">
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full mr-3 mt-0.5">5</span>
                                    <p class="text-gray-700">Review the photo and click <strong>"Enroll Face
                                            Recognition"</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tips -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h4 class="font-medium text-yellow-800 mb-2">
                            <x-fas-exclamation-triangle class="w-4 h-4 mr-1 inline" />
                            Tips for Best Results
                        </h4>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li>• Face should be clearly visible and well-lit</li>
                            <li>• Look directly at the camera</li>
                            <li>• Remove glasses or masks if possible</li>
                            <li>• Avoid shadows on the face</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let stream = null;
        let capturedImageData = null;

        function startCamera() {
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(function(mediaStream) {
                    stream = mediaStream;
                    document.getElementById('video').srcObject = stream;

                    // Update UI
                    document.getElementById('startCameraBtn').disabled = true;
                    document.getElementById('captureBtn').disabled = false;
                    document.getElementById('stopCameraBtn').disabled = false;
                    document.getElementById('cameraStatus').innerHTML = `
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                            <span class="text-sm text-gray-700">Camera is active - Position face and capture photo</span>
                        </div>
                    `;
                })
                .catch(function(err) {
                    console.error('Camera error:', err);
                    document.getElementById('cameraStatus').innerHTML = `
                        <div class="flex items-center">
                            <x-fas-exclamation-triangle class="w-5 h-5 text-red-500 mr-2" />
                            <span class="text-sm text-red-700">Camera access denied or not available</span>
                        </div>
                    `;
                });
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }

            // Reset UI
            document.getElementById('startCameraBtn').disabled = false;
            document.getElementById('captureBtn').disabled = true;
            document.getElementById('stopCameraBtn').disabled = true;
            document.getElementById('cameraStatus').innerHTML = `
                <div class="flex items-center">
                    <x-fas-info-circle class="w-5 h-5 text-gray-500 mr-2" />
                    <span class="text-sm text-gray-700">Camera stopped</span>
                </div>
            `;
        }

        function capturePhoto() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0);

            // Show preview
            const capturedImage = document.getElementById('capturedImage');
            capturedImageData = canvas.toDataURL('image/jpeg', 0.8);
            capturedImage.src = capturedImageData;
            document.getElementById('photoPreview').classList.remove('hidden');

            // Update UI
            document.getElementById('captureBtn').disabled = true;
            document.getElementById('retakeBtn').style.display = 'inline-block';
            document.getElementById('enrollBtn').disabled = false;

            // Stop camera after capture
            stopCamera();
        }

        function retakePhoto() {
            // Hide preview
            document.getElementById('photoPreview').classList.add('hidden');
            capturedImageData = null;

            // Reset UI
            document.getElementById('retakeBtn').style.display = 'none';
            document.getElementById('enrollBtn').disabled = true;

            // Restart camera
            startCamera();
        }

        function submitFaceEnrollment() {
            if (!capturedImageData) {
                alert('Please capture a photo first');
                return;
            }

            const galleryId = document.getElementById('gallerySelect').value;
            const base64data = capturedImageData.split(',')[1];

            // Show loading state
            const enrollBtn = document.getElementById('enrollBtn');
            const originalText = enrollBtn.innerHTML;
            enrollBtn.disabled = true;
            enrollBtn.innerHTML = `
                <svg class="animate-spin w-5 h-5 mr-2 inline" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Enrolling...
            `;

            fetch(`/admin/employees/{{ $employee->id }}/enroll-face`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        face_image: base64data,
                        gallery_id: galleryId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    enrollBtn.disabled = false;
                    enrollBtn.innerHTML = originalText;

                    if (data.success) {
                        // Success notification
                        const successDiv = document.createElement('div');
                        successDiv.className =
                            'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg z-50';
                        successDiv.innerHTML = `
                        <div class="flex items-center">
                            <x-fas-check-circle class="w-5 h-5 mr-2" />
                            <span>Face enrolled successfully!</span>
                        </div>
                    `;
                        document.body.appendChild(successDiv);

                        // Auto remove notification and redirect
                        setTimeout(() => {
                            successDiv.remove();
                            window.location.href = "{{ route('admin.employees.show', $employee) }}";
                        }, 2000);
                    } else {
                        alert('Face enrollment failed: ' + data.message);
                    }
                })
                .catch(error => {
                    enrollBtn.disabled = false;
                    enrollBtn.innerHTML = originalText;
                    console.error('Enrollment error:', error);
                    alert('An error occurred during face enrollment');
                });
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        });
    </script>
</x-layouts.app>
