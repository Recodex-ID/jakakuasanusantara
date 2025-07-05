<!-- Flash Messages Component -->
@if (session('success') || session('error') || session('warning') || session('info'))
    <div id="flash-container" class="fixed top-4 right-4 z-50 space-y-2">
        @if (session('success'))
            <div class="flash-message bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg max-w-sm" role="alert">
                <div class="flex items-center">
                    <x-fas-check-circle class="w-5 h-5 mr-2" />
                    <span class="font-medium">{{ session('success') }}</span>
                    <button type="button" class="ml-3 -mx-1.5 -my-1.5 text-green-500 hover:text-green-600 rounded-lg focus:ring-2 focus:ring-green-300 p-1.5 hover:bg-green-200 inline-flex h-8 w-8" onclick="this.parentElement.parentElement.remove()">
                        <x-fas-times class="w-3 h-3" />
                    </button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="flash-message bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg max-w-sm" role="alert">
                <div class="flex items-center">
                    <x-fas-times-circle class="w-5 h-5 mr-2" />
                    <span class="font-medium">{{ session('error') }}</span>
                    <button type="button" class="ml-3 -mx-1.5 -my-1.5 text-red-500 hover:text-red-600 rounded-lg focus:ring-2 focus:ring-red-300 p-1.5 hover:bg-red-200 inline-flex h-8 w-8" onclick="this.parentElement.parentElement.remove()">
                        <x-fas-times class="w-3 h-3" />
                    </button>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="flash-message bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg shadow-lg max-w-sm" role="alert">
                <div class="flex items-center">
                    <x-fas-exclamation-triangle class="w-5 h-5 mr-2" />
                    <span class="font-medium">{{ session('warning') }}</span>
                    <button type="button" class="ml-3 -mx-1.5 -my-1.5 text-yellow-500 hover:text-yellow-600 rounded-lg focus:ring-2 focus:ring-yellow-300 p-1.5 hover:bg-yellow-200 inline-flex h-8 w-8" onclick="this.parentElement.parentElement.remove()">
                        <x-fas-times class="w-3 h-3" />
                    </button>
                </div>
            </div>
        @endif

        @if (session('info'))
            <div class="flash-message bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg shadow-lg max-w-sm" role="alert">
                <div class="flex items-center">
                    <x-fas-info-circle class="w-5 h-5 mr-2" />
                    <span class="font-medium">{{ session('info') }}</span>
                    <button type="button" class="ml-3 -mx-1.5 -my-1.5 text-blue-500 hover:text-blue-600 rounded-lg focus:ring-2 focus:ring-blue-300 p-1.5 hover:bg-blue-200 inline-flex h-8 w-8" onclick="this.parentElement.parentElement.remove()">
                        <x-fas-times class="w-3 h-3" />
                    </button>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Auto-hide flash messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessages = document.querySelectorAll('.flash-message');
            flashMessages.forEach(function(message) {
                setTimeout(function() {
                    if (message.parentElement) {
                        message.style.opacity = '0';
                        message.style.transform = 'translateX(100%)';
                        message.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        setTimeout(function() {
                            if (message.parentElement) {
                                message.remove();
                            }
                        }, 300);
                    }
                }, 5000);
            });
        });
    </script>
@endif