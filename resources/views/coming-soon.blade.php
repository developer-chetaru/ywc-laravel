<x-app-layout>
    <div>
        <div class="max-w-8xl mx-auto sm:px-4 lg:px-4">
            <div class="bg-white p-10 rounded-lg shadow-md">
                <div class="flex flex-col items-center justify-center min-h-[60vh]">
                    <div class="text-center">
                        <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ $title ?? 'Coming Soon' }}</h1>
                        <p class="text-xl text-gray-600 mb-8">We're working hard to bring you this feature.</p>
                        <div class="inline-block p-4 bg-blue-50 rounded-lg">
                            <svg class="w-16 h-16 text-blue-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 mt-6">Please check back soon!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

