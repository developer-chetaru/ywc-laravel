<div x-data="itineraryApp()" x-init="init()" class="max-w-6xl mx-auto p-6">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Itineraries</h1>
            <button @click="showForm = !showForm"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <span x-text="showForm ? 'Close Form' : 'Add New Itinerary'"></span>
            </button>
        </div>

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

                    form: {
                        id: null,
                        title: '',
                        description: '',
                        day_count: 1,
                        itinerary_days: []
                    },
                    viewModal: false,
                    viewData: {},

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