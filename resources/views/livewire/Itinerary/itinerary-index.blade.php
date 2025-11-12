<div x-data="itineraryApp()" x-init="init()" class="max-w-6xl mx-auto p-6">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Itineraries</h1>
            <div class="flex gap-2">
                <button @click="showAIForm = !showAIForm; showForm = false"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                    <span x-text="showAIForm ? 'Close AI Form' : 'Plan Itinerary with AI'"></span>
                </button>
                <button @click="showForm = !showForm; showAIForm = false"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <span x-text="showForm ? 'Close Form' : 'Add New Itinerary'"></span>
            </button>
        </div>
        </div>

        {{-- AI ITINERARY FORM --}}
        <form x-show="showAIForm" x-transition @submit.prevent="submitAIForm"
            class="space-y-4 border-b border-gray-200 pb-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Plan Itinerary with AI</h2>
            
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Place</label>
                <input type="text" x-model="aiForm.place" 
                    placeholder="Enter destination (e.g., Paris, Tokyo)" 
                    class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Days</label>
                <input type="number" x-model.number="aiForm.days" 
                    placeholder="Number of days" 
                    min="1" max="30"
                    class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="flex justify-end">
                <button type="submit" 
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg"
                    :disabled="aiFormSubmitting">
                    <span x-text="aiFormSubmitting ? 'Generating...' : 'Generate Itinerary'"></span>
                </button>
            </div>
        </form>

        {{-- ADD / EDIT FORM --}}
        <form x-show="showForm" x-transition @submit.prevent="submitForm"
            class="space-y-4 border-b border-gray-200 pb-6 mb-6">
            <input type="hidden" x-model="form.id">

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Title</label>
                <input type="text" x-model="form.title" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Description</label>
                <textarea x-model="form.description" class="w-full border rounded px-3 py-2"></textarea>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Day Count</label>
                <input type="number" x-model.number="form.day_count" @change="generateDays" min="1"
                    class="w-32 border rounded px-3 py-2">
            </div>

            {{-- Dynamic Day Inputs --}}
            <template x-for="(day, index) in form.itinerary_days" :key="index">
                <div class="border rounded-lg p-4 mt-4 bg-gray-50">
                    <h2 class="font-semibold text-lg mb-2 text-gray-700">
                        Day <span x-text="index + 1"></span> Details
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-600 font-semibold mb-1">Topic</label>
                            <input type="text" x-model="day.topic" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-1">Place</label>
                            <input type="text" x-model="day.place" class="w-full border rounded px-3 py-2">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="block text-gray-600 font-semibold mb-1">Description</label>
                        <textarea x-model="day.description" class="w-full border rounded px-3 py-2"></textarea>
                    </div>

                    <div class="mt-3">
                        <label class="block text-gray-600 font-semibold mb-1">Upload Images</label>
                        <input type="file" multiple @change="handleFileUpload($event, index)"
                            class="w-full border rounded px-3 py-2">

                        <template x-if="day.preview && day.preview.length">
                            <div class="flex flex-wrap gap-2 mt-2">
                                <template x-for="(img, imgIndex) in day.preview" :key="imgIndex">
                                    <img :src="img" class="w-20 h-20 object-cover rounded">
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <div class="flex justify-between">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <span x-text="form.id ? 'Update Itinerary' : 'Save Itinerary'"></span>
                </button>
                <button type="button" x-show="form.id" @click="resetForm()"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Cancel Edit
                </button>
            </div>
        </form>

        {{-- SEARCH + FILTER --}}
        <div class="flex justify-between items-center mb-4">
            <input type="text" x-model="search" @input.debounce.300ms="filterItineraries"
                placeholder="Search by title..." class="border rounded px-3 py-2 w-1/3">
        </div>

        {{-- LIST VIEW --}}
<div>
    {{-- ✅ If current user is super admin --}}
    @if(auth()->user()?->hasRole('super_admin'))
        <template x-if="itineraries.length === 0">
            <div class="text-gray-500">No itineraries found.</div>
        </template>

        <template x-for="item in paginatedItineraries" :key="item.id">
            <div class="border rounded-lg p-4 mb-3 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800" x-text="item.title"></h3>
                <p class="text-gray-600 mb-2" x-text="item.description"></p>
                <p class="text-sm text-gray-500">
                    Days: <span x-text="item.day_count"></span>
                </p>
                <p class="text-sm mt-1">
                    <span class="px-2 py-1 rounded text-xs font-semibold"
                        :class="{
                            'bg-yellow-100 text-yellow-800': item.status === 'pending',
                            'bg-green-100 text-green-800': item.status === 'approved',
                            'bg-red-100 text-red-800': item.status === 'rejected'
                        }"
                        x-text="item.status.toUpperCase()">
                    </span>
                </p>

                <div class="mt-2 flex gap-2">
                    <button @click="viewItinerary(item)"
                        class="bg-green-600 text-white px-3 py-1 rounded">View</button>

                    <button @click="editItinerary(item)"
                        class="bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>

                    <button @click="deleteItinerary(item.id)"
                        class="bg-red-600 text-white px-3 py-1 rounded">Delete</button>

                    <button @click="updateStatus(item.id, 'approved')"
                        class="flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">
                        Approve
                    </button>

                    <button @click="updateStatus(item.id, 'rejected')"
                        class="flex items-center gap-1 bg-gray-700 hover:bg-gray-800 text-white px-3 py-1 rounded">
                        Reject
                    </button>
                </div>
            </div>
        </template>
    @else
        {{-- ✅ For normal users: only show approved + own itineraries --}}
        <template
            x-if="itineraries.filter(i => i.status === 'approved' || i.user_id === {{ auth()->id() }}).length === 0">
            <div class="text-gray-500">No itineraries found.</div>
        </template>

        <template
            x-for="item in itineraries.filter(i => i.status === 'approved' || i.user_id === {{ auth()->id() }})"
            :key="item.id">
            <div class="border rounded-lg p-4 mb-3 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800" x-text="item.title"></h3>
                <p class="text-gray-600 mb-2" x-text="item.description"></p>
                <p class="text-sm text-gray-500">
                    Days: <span x-text="item.day_count"></span>
                </p>
                <p class="text-sm mt-1">
                    <span class="px-2 py-1 rounded text-xs font-semibold"
                        :class="{
                            'bg-yellow-100 text-yellow-800': item.status === 'pending',
                            'bg-green-100 text-green-800': item.status === 'approved',
                            'bg-red-100 text-red-800': item.status === 'rejected'
                        }"
                        x-text="item.status.toUpperCase()">
                    </span>
                </p>

                <div class="mt-2 flex gap-2">
                    <button @click="viewItinerary(item)"
                        class="bg-green-600 text-white px-3 py-1 rounded">View</button>

                    {{-- Allow editing only if user created it --}}
                    <template x-if="item.user_id === {{ auth()->id() }}">
                        <button @click="editItinerary(item)"
                            class="bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>
                    </template>
                </div>
            </div>
        </template>
    @endif
</div>


        {{-- PAGINATION --}}
        <div class="flex justify-center items-center mt-6 gap-2">
            <button @click="prevPage" :disabled="page === 1"
                class="px-3 py-1 border rounded text-gray-600 hover:bg-gray-100 disabled:opacity-50">Prev</button>
            <span class="text-gray-700">Page <span x-text="page"></span> of <span x-text="totalPages"></span></span>
            <button @click="nextPage" :disabled="page === totalPages"
                class="px-3 py-1 border rounded text-gray-600 hover:bg-gray-100 disabled:opacity-50">Next</button>
        </div>

        {{-- VIEW MODAL --}}
        <template x-if="viewModal">
            <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg max-w-3xl w-full p-6 relative">
                    <button @click="viewModal = false" class="absolute top-2 right-3 text-gray-500 text-2xl">&times;</button>

                    <template x-if="viewData && Object.keys(viewData).length">
                        <div>
                            <h2 class="text-2xl font-bold mb-4" x-text="viewData.title"></h2>
                            <p class="text-gray-700 mb-4" x-text="viewData.description"></p>

                            <template x-for="(day, i) in viewData.itinerary_days" :key="i">
                                <div class="border rounded-lg p-4 mb-3 bg-gray-50">
                                    <h3 class="font-semibold text-lg mb-2 text-gray-700">
                                        Day <span x-text="i+1"></span> - <span x-text="day.topic"></span>
                                    </h3>
                                    <p class="text-gray-600 mb-1"><strong>Place:</strong> <span x-text="day.place"></span></p>
                                    <p class="text-gray-600 mb-2" x-text="day.description"></p>

                                    <div class="flex flex-wrap gap-2" x-show="day.images && day.images.length">
                                        <template x-for="img in day.images" :key="img">
                                            <img :src="img.startsWith('http') ? img : `/storage/${img}`"
                                                class="w-24 h-24 object-cover rounded">
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- AI PREVIEW MODAL --}}
        <template x-if="aiPreviewModal && aiPreviewData">
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-xl max-w-5xl w-full max-h-[90vh] overflow-y-auto relative">
                    <button @click="aiPreviewModal = false" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-3xl font-bold">&times;</button>

                    <div class="p-6 space-y-6">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-800 mb-2" x-text="aiPreviewData.title"></h2>
                            <p class="text-gray-600 mb-4" x-text="aiPreviewData.description"></p>

                            <div class="flex flex-wrap gap-3 text-sm">
                                <template x-if="aiPreviewData.wikipedia_url">
                                    <a :href="aiPreviewData.wikipedia_url" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-blue-100 text-blue-700 rounded-full">
                                        <span>Overview on Wikipedia</span>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.293 2.293a1 1 0 011.414 0l4 4a1 1 0 01-.707 1.707H16v6a3 3 0 01-3 3H5a3 3 0 01-3-3V7a3 3 0 013-3h6V2.293zM11 4v2a1 1 0 001 1h2.586L11 4.414zM4 7a1 1 0 011-1h6v2a3 3 0 003 3h2v4a1 1 0 01-1 1H5a1 1 0 01-1-1V7z" clip-rule="evenodd" /></svg>
                                    </a>
                                </template>

                                <template x-if="aiPreviewData.coordinates">
                                    <span class="inline-flex items-center gap-2 px-3 py-2 bg-purple-100 text-purple-700 rounded-full">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                                        <span x-text="'Lat ' + Number(aiPreviewData.coordinates.latitude || 0).toFixed(2) + ', Lon ' + Number(aiPreviewData.coordinates.longitude || 0).toFixed(2)"></span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        <template x-if="aiPreviewData.weather_summary">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-blue-800 mb-2">Weather Overview</h3>
                                <p class="text-blue-700" x-text="aiPreviewData.weather_summary.summary"></p>
                            </div>
                        </template>

                        <template x-if="aiPreviewData.itinerary_days && aiPreviewData.itinerary_days.length">
                            <div class="space-y-6">
                                <template x-for="(day, index) in aiPreviewData.itinerary_days" :key="index">
                                    <div class="border-2 border-purple-200 rounded-lg p-6 bg-gradient-to-br from-purple-50 to-blue-50 shadow-sm">
                                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                            <div class="flex-1">
                                                <h3 class="text-2xl font-bold text-gray-800 mb-3" x-text="day.topic"></h3>
                                                <p class="text-gray-700 mb-4 leading-relaxed" x-text="day.description"></p>

                                                <template x-if="day.more_info_url">
                                                    <a :href="day.more_info_url" target="_blank"
                                                        class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-800 mb-4">
                                                        <span x-text="day.wikipedia_info && day.wikipedia_info.title ? 'Learn more about ' + day.wikipedia_info.title : 'View on Wikipedia'"></span>
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M12.293 2.293a1 1 0 011.414 0l4 4a1 1 0 01-.707 1.707H16v6a3 3 0 01-3 3H5a3 3 0 01-3-3V7a3 3 0 013-3h6V2.293zM11 4v2a1 1 0 001 1h2.586L11 4.414zM4 7a1 1 0 011-1h6v2a3 3 0 003 3h2v4a1 1 0 01-1 1H5a1 1 0 01-1-1V7z" clip-rule="evenodd" />
                                                        </svg>
                                                    </a>
                                                </template>
                                            </div>

                                            <template x-if="day.weather">
                                                <div class="bg-white bg-opacity-80 border border-blue-200 rounded-xl p-4 w-full md:w-60">
                                                    <div class="flex items-center gap-2 mb-2 text-blue-700 text-lg font-semibold">
                                                        <span x-text="day.weather.icon"></span>
                                                        <span x-text="day.weather.description"></span>
                                                    </div>
                                                    <p class="text-sm text-gray-700">High: <span x-text="`${day.weather.temperature_max}°C`"></span></p>
                                                    <p class="text-sm text-gray-700">Low: <span x-text="`${day.weather.temperature_min}°C`"></span></p>
                                                    <p class="text-sm text-gray-700">Precipitation chance: <span x-text="`${day.weather.precipitation_probability}%`"></span></p>
                                                    <p class="text-xs text-gray-500 mt-2" x-text="day.weather.date"></p>
                                                </div>
                                            </template>
                                        </div>

                                        <template x-if="day.schedule">
                                            <div class="mt-4 grid md:grid-cols-3 gap-3">
                                                <div class="bg-white bg-opacity-80 rounded-lg p-3 shadow-sm">
                                                    <h4 class="font-semibold text-gray-700 mb-1">Morning</h4>
                                                    <p class="text-sm text-gray-600" x-text="day.schedule.morning"></p>
                                                </div>
                                                <div class="bg-white bg-opacity-80 rounded-lg p-3 shadow-sm">
                                                    <h4 class="font-semibold text-gray-700 mb-1">Afternoon</h4>
                                                    <p class="text-sm text-gray-600" x-text="day.schedule.afternoon"></p>
                                                </div>
                                                <div class="bg-white bg-opacity-80 rounded-lg p-3 shadow-sm">
                                                    <h4 class="font-semibold text-gray-700 mb-1">Evening</h4>
                                                    <p class="text-sm text-gray-600" x-text="day.schedule.evening"></p>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="day.images && day.images.length">
                                            <div class="mt-5">
                                                <h4 class="font-semibold text-gray-700 mb-2">Images</h4>
                                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                    <template x-for="(img, imgIndex) in day.images" :key="imgIndex">
                                                        <div class="relative group">
                                                            <img :src="img.url" :alt="img.alt" 
                                                                class="w-full h-36 object-cover rounded-lg shadow-md hover:shadow-xl transition-shadow cursor-pointer"
                                                                loading="lazy"
                                                                @click="window.open(img.url, '_blank')">
                                                            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 rounded-b-lg opacity-0 group-hover:opacity-100 transition-opacity">
                                                                <span x-text="img.source"></span>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="day.youtube_videos && day.youtube_videos.length">
                                            <div class="mt-5">
                                                <h4 class="font-semibold text-gray-700 mb-2">Related Videos</h4>
                                                <div class="space-y-2">
                                                    <template x-for="(video, vidIndex) in day.youtube_videos" :key="vidIndex">
                                                        <a :href="video.search_url" target="_blank" 
                                                            class="flex items-center gap-2 p-3 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                                            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                            </svg>
                                                            <span class="text-gray-700 font-medium" x-text="video.title"></span>
                                                        </a>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <div class="grid md:grid-cols-3 gap-4">
                            <template x-if="aiPreviewData.local_cuisine && aiPreviewData.local_cuisine.length">
                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-orange-700 mb-2">Local Cuisine</h3>
                                    <ul class="list-disc list-inside space-y-1 text-sm text-orange-800">
                                        <template x-for="(item, cIdx) in aiPreviewData.local_cuisine" :key="cIdx">
                                            <li x-text="item"></li>
                                        </template>
                                    </ul>
                                </div>
                            </template>

                            <template x-if="aiPreviewData.travel_tips && aiPreviewData.travel_tips.length">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-green-700 mb-2">Travel Tips</h3>
                                    <ul class="list-disc list-inside space-y-1 text-sm text-green-800">
                                        <template x-for="(tip, tIdx) in aiPreviewData.travel_tips" :key="tIdx">
                                            <li x-text="tip"></li>
                                        </template>
                                    </ul>
                                </div>
                            </template>

                            <template x-if="aiPreviewData.packing_list && aiPreviewData.packing_list.length">
                                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-indigo-700 mb-2">Packing Checklist</h3>
                                    <ul class="list-disc list-inside space-y-1 text-sm text-indigo-800">
                                        <template x-for="(item, pIdx) in aiPreviewData.packing_list" :key="pIdx">
                                            <li x-text="item"></li>
                                        </template>
                                    </ul>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- ✅ Alpine App Script --}}
    <script>
        document.addEventListener('alpine:init', () => {
            window.itineraryApp = function() {
                return {
                    userId: {{ auth()->id() }}, 
                    itineraries: [],
                    filteredItineraries: [],
                    page: 1,
                    perPage: 5,
                    totalPages: 1,
                    search: '',
                    showForm: false,
                    showAIForm: false,
                    aiFormSubmitting: false,
                    aiForm: {
                        place: '',
                        days: 1
                    },

                    form: {
                        id: null,
                        title: '',
                        description: '',
                        day_count: 1,
                        itinerary_days: []
                    },
                    viewModal: false,
                    viewData: {},
                    aiPreviewModal: false,
                    aiPreviewData: null,

                    get paginatedItineraries() {
                        const start = (this.page - 1) * this.perPage;
                        return this.filteredItineraries.slice(start, start + this.perPage);
                    },

                    nextPage() {
                        if (this.page < this.totalPages) this.page++;
                    },
                    prevPage() {
                        if (this.page > 1) this.page--;
                    },

                    filterItineraries() {
                        const query = this.search.toLowerCase();
                        this.filteredItineraries = this.itineraries.filter(i =>
                            i.title.toLowerCase().includes(query)
                        );
                        this.totalPages = Math.ceil(this.filteredItineraries.length / this.perPage) || 1;
                        this.page = 1;
                    },

                    async fetchItineraries() {
                        const res = await fetch('/api/itineraries');
                        let data = await res.json();
                        this.itineraries = (data || []).map(item => {
                            let days = item.itinerary_days;
                            if (typeof days === 'string') {
                                try {
                                    days = JSON.parse(days);
                                } catch {
                                    days = [];
                                }
                            }
                            return {
                                ...item,
                                itinerary_days: Array.isArray(days) ? days : []
                            };
                        });
                        this.filteredItineraries = this.itineraries;
                        this.totalPages = Math.ceil(this.filteredItineraries.length / this.perPage) || 1;
                    },

                    generateDays() {
                        this.form.itinerary_days = Array.from({
                            length: this.form.day_count
                        }, () => ({
                            topic: '',
                            place: '',
                            description: '',
                            files: [],
                            preview: []
                        }));
                    },

                    handleFileUpload(event, index) {
                        const files = Array.from(event.target.files);
                        this.form.itinerary_days[index].files = files;
                        this.form.itinerary_days[index].preview = files.map(f => URL.createObjectURL(f));
                    },

                    async submitAIForm() {
                        if (!this.aiForm.place || !this.aiForm.days) {
                            alert('Please fill in both place and days');
                            return;
                        }

                        this.aiFormSubmitting = true;
                        try {
                            const res = await fetch('/api/itineraries/ai-generate', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    place: this.aiForm.place,
                                    days: this.aiForm.days
                                })
                            });

                            const data = await res.json();
                            
                            if (res.ok && data.success) {
                                this.aiPreviewData = data;
                                this.aiPreviewModal = true;
                                this.showAIForm = false;
                                this.aiForm = { place: '', days: 1 };
                            } else {
                                alert(data.message || 'Failed to generate itinerary. Please try again.');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('An error occurred. Please try again.');
                        } finally {
                            this.aiFormSubmitting = false;
                        }
                    },

                    async submitForm() {
                        const formData = new FormData();
                        formData.append('title', this.form.title);
                        formData.append('description', this.form.description);
                        formData.append('day_count', this.form.day_count);
                        formData.append('user_id', this.userId); // 👈 include the user ID here
                        formData.append(
                            'itinerary_days',
                            JSON.stringify(
                                this.form.itinerary_days.map(d => ({
                                    topic: d.topic,
                                    place: d.place,
                                    description: d.description
                                }))
                            )
                        );

                        this.form.itinerary_days.forEach((day, i) => {
                            (day.files || []).forEach(file =>
                                formData.append(`day_${i}_files[]`, file)
                            );
                        });

                        if (this.form.id) formData.append('_method', 'PUT');
                        const url = this.form.id ? `/api/itineraries/${this.form.id}` : '/api/itineraries';

                        const res = await fetch(url, {
                            method: 'POST',
                            body: formData
                        });
                        if (res.ok) {
                            alert(this.form.id ? 'Updated!' : 'Saved!');
                            this.showForm = false;
                            this.resetForm();
                            this.fetchItineraries();
                        } else alert('Something went wrong.');
                    },

                    editItinerary(item) {
                        this.showForm = true;
                        const clone = JSON.parse(JSON.stringify(item));
                        let days = typeof clone.itinerary_days === 'string' ?
                            JSON.parse(clone.itinerary_days) :
                            clone.itinerary_days;

                        this.form = {
                            id: clone.id,
                            title: clone.title,
                            description: clone.description,
                            day_count: clone.day_count,
                            itinerary_days: (days || []).map(d => ({
                                topic: d.topic,
                                place: d.place,
                                description: d.description,
                                files: [],
                                preview: (d.images || []).map(img =>
                                    img.startsWith('http') ? img : `/storage/${img}`
                                )
                            }))
                        };
                    },

                    viewItinerary(item) {
                        this.viewData = JSON.parse(JSON.stringify(item));
                        this.viewModal = true;
                    },

                    async deleteItinerary(id) {
                        if (!confirm('Delete this itinerary?')) return;
                        await fetch(`/api/itineraries/${id}`, {
                            method: 'DELETE'
                        });
                        this.fetchItineraries();
                    },

                    async updateStatus(id, status) {
                        const res = await fetch(`/api/itineraries/${id}/status`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                status
                            })
                        });
                        if (res.ok) {
                            alert(`Itinerary ${status}!`);
                            this.fetchItineraries();
                        } else alert('Failed to update.');
                    },
                    resetForm() {
                        this.form = {
                            id: null,
                            title: '',
                            description: '',
                            day_count: 1,
                            itinerary_days: []
                        };
                        this.generateDays();
                    },

                    init() {
                        this.generateDays();
                        this.fetchItineraries();
                    }
                };
            };
        });
    </script>
</div>