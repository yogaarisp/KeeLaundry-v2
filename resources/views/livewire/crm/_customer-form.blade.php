<div class="p-5 space-y-4" x-data="customerMapHandler()">
    <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-800 text-base sm:text-lg">{{ $editingId ? 'Edit Pelanggan' : 'Tambah Pelanggan Baru' }}</h3>
        <button wire:click="closeForm" class="btn-icon">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div class="col-span-2">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" wire:model="name" class="form-input" placeholder="cth. Dewi Kusuma">
            @error('name') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">No. HP / WhatsApp</label>
            <input type="text" wire:model="phone" class="form-input" placeholder="08xxxxxxxxxx">
            @error('phone') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Tier Member</label>
            <select wire:model="tier" class="form-input">
                <option value="REGULAR">Regular</option>
                <option value="SILVER">Silver</option>
                <option value="VIP">VIP</option>
            </select>
        </div>

        {{-- ══ ALAMAT JEMPUT & GOOGLE MAPS INTEGRATION ══ --}}
        <div class="col-span-2 space-y-2">
            <div class="flex items-center justify-between flex-wrap gap-1">
                <label class="form-label mb-0">Alamat Jemput / Rumah (opsional)</label>
                <div class="flex items-center gap-1.5">
                    {{-- Button: GPS Location --}}
                    <button type="button"
                            @click="detectLocation()"
                            :disabled="isLoadingGps"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 text-xs font-semibold border border-sky-200 transition-colors">
                        <span x-show="!isLoadingGps">📍</span>
                        <span x-show="isLoadingGps" class="size-3 animate-spin border-2 border-sky-600 border-t-transparent rounded-full"></span>
                        <span x-text="isLoadingGps ? 'Mencari...' : 'GPS Lokasi Saya'"></span>
                    </button>

                    {{-- Button: Open Interactive Map Picker --}}
                    <button type="button"
                            @click="openMapModal()"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold border border-indigo-200 transition-colors">
                        <span>🗺️</span>
                        <span>Pilih di Peta</span>
                    </button>
                </div>
            </div>

            <textarea wire:model="address" rows="2" class="form-input resize-none text-xs sm:text-sm" placeholder="Nama jalan, nomor rumah, RT/RW, patokan lokasi..."></textarea>
            @error('address') <p class="form-error">{{ $message }}</p> @enderror

            {{-- Google Maps URL / Shareloc --}}
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[11px] font-semibold text-slate-500">Titik / Link Google Maps</span>
                    <template x-if="$wire.maps_url || $wire.address">
                        <a :href="$wire.maps_url || ('https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent($wire.address))"
                           target="_blank"
                           class="text-[11px] text-sky-600 hover:text-sky-700 font-semibold inline-flex items-center gap-1 hover:underline">
                            <span>🔍 Buka di Google Maps</span>
                            <svg class="size-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25"/></svg>
                        </a>
                    </template>
                </div>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">🔗</span>
                    <input type="url"
                           wire:model="maps_url"
                           placeholder="https://maps.google.com/?q=... atau shareloc WA"
                           class="form-input pl-8 text-xs font-mono">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Bisa paste link shareloc WhatsApp atau gunakan tombol "Pilih di Peta" di atas.</p>
            </div>
        </div>

        <div class="col-span-2">
            <label class="form-label">Preferensi Cucian (opsional)</label>
            <textarea wire:model="preferences" rows="2" class="form-input resize-none" placeholder="cth. Parfum Sakura, jangan dilipat…"></textarea>
        </div>
    </div>

    <div class="flex gap-3 pt-2 border-t border-slate-100">
        <button wire:click="saveCustomer" wire:loading.attr="disabled" class="btn-primary flex-1">
            <span wire:loading.remove wire:target="saveCustomer">{{ $editingId ? 'Simpan Perubahan' : 'Daftarkan' }}</span>
            <span wire:loading wire:target="saveCustomer">Menyimpan…</span>
        </button>
        <button wire:click="closeForm" class="btn-secondary">Batal</button>
    </div>

    {{-- ══ INTERACTIVE MAP PICKER MODAL (Leaflet + Google Maps Sync) ══ --}}
    <div x-show="showMapModal" x-cloak style="display:none">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[70]" @click="closeMapModal()"></div>
        <div class="fixed inset-0 z-[75] flex items-center justify-center p-3 sm:p-4 pointer-events-none">
            <div class="pointer-events-auto w-full max-w-2xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">
                {{-- Header --}}
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between shrink-0 bg-slate-50/50">
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm sm:text-base flex items-center gap-1.5">
                            <span>🗺️</span> Pilih Titik Alamat di Peta
                        </h4>
                        <p class="text-[11px] text-slate-500">Geser atau klik peta untuk menentukan lokasi penjemputan cucian</p>
                    </div>
                    <button type="button" @click="closeMapModal()" class="btn-icon">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Search & Controls bar inside map --}}
                <div class="p-3 bg-white border-b border-slate-100 flex gap-2 shrink-0">
                    <div class="relative flex-1">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                        <input type="text"
                               x-model="searchQuery"
                               @keydown.enter.prevent="searchLocation()"
                               placeholder="Cari jalan, kelurahan, atau nama tempat..."
                               class="form-input pl-9 text-xs">
                    </div>
                    <button type="button"
                            @click="searchLocation()"
                            :disabled="isSearching"
                            class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition-colors">
                        <span x-show="!isSearching">Cari</span>
                        <span x-show="isSearching">...</span>
                    </button>
                </div>

                {{-- Map Container --}}
                <div class="relative flex-1 min-h-[300px] sm:min-h-[380px] bg-slate-100">
                    <div id="customer-picker-map" class="absolute inset-0 z-0"></div>

                    {{-- Target instruction overlay --}}
                    <div class="absolute bottom-3 left-3 right-3 z-10 pointer-events-none">
                        <div class="p-2.5 rounded-xl bg-white/95 backdrop-blur-md shadow-lg border border-slate-200/80 pointer-events-auto">
                            <p class="text-[11px] font-bold text-slate-800 flex items-center gap-1 truncate">
                                <span>📍</span>
                                <span x-text="selectedAddressName || 'Klik titik pada peta untuk memilih lokasi'"></span>
                            </p>
                            <p class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="selectedCoords ? (selectedCoords.lat.toFixed(5) + ', ' + selectedCoords.lng.toFixed(5)) : ''"></p>
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="p-3.5 sm:p-4 border-t border-slate-100 flex items-center justify-between gap-3 bg-white shrink-0">
                    <button type="button" @click="closeMapModal()" class="btn-secondary text-xs sm:text-sm">Batal</button>
                    <button type="button"
                            @click="applySelectedLocation()"
                            :disabled="!selectedCoords"
                            class="btn-primary text-xs sm:text-sm px-5 py-2.5 shadow-md">
                        <span>✅ Gunakan Titik Lokasi Ini</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function customerMapHandler() {
    return {
        showMapModal: false,
        isLoadingGps: false,
        isSearching: false,
        searchQuery: '',
        selectedCoords: null,
        selectedAddressName: '',
        mapInstance: null,
        markerInstance: null,

        // 1. Detect location via GPS
        detectLocation() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung deteksi lokasi Geolocation.');
                return;
            }
            this.isLoadingGps = true;
            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    const mapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;
                    @this.set('maps_url', mapsUrl);

                    // Reverse geocode to get road name
                    try {
                        const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                        const data = await res.json();
                        if (data && data.display_name) {
                            @this.set('address', data.display_name);
                        }
                    } catch (e) {
                        console.warn('Geocoding error:', e);
                        if (!@this.address) {
                            @this.set('address', `Titik Koordinat: ${lat.toFixed(5)}, ${lng.toFixed(5)}`);
                        }
                    }

                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'success', title: 'Lokasi Terdeteksi', message: 'Titik koordinat dan alamat berhasil didapatkan.' }
                    }));
                    this.isLoadingGps = false;
                },
                (err) => {
                    console.warn('GPS error:', err);
                    alert('Gagal mengambil lokasi GPS. Pastikan izin akses lokasi telah diaktifkan pada browser Anda.');
                    this.isLoadingGps = false;
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        },

        // 2. Open interactive Map modal
        openMapModal() {
            this.showMapModal = true;
            this.$nextTick(() => {
                this.initMap();
            });
        },

        closeMapModal() {
            this.showMapModal = false;
        },

        initMap() {
            const mapEl = document.getElementById('customer-picker-map');
            if (!mapEl) return;

            // Default center: Semarang / Kee Laundry Pusat (-7.005145, 110.438125)
            let defaultLat = -7.005145;
            let defaultLng = 110.438125;

            // Check if current maps_url has coordinates
            const currentUrl = @this.maps_url || '';
            const coordMatch = currentUrl.match(/q=([-+]?[0-9]*\.?[0-9]+),([-+]?[0-9]*\.?[0-9]+)/);
            if (coordMatch) {
                defaultLat = parseFloat(coordMatch[1]);
                defaultLng = parseFloat(coordMatch[2]);
            }

            if (!this.mapInstance) {
                this.mapInstance = L.map('customer-picker-map').setView([defaultLat, defaultLng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(this.mapInstance);

                this.markerInstance = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(this.mapInstance);

                this.markerInstance.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.setPoint(pos.lat, pos.lng);
                });

                this.mapInstance.on('click', (e) => {
                    this.markerInstance.setLatLng(e.latlng);
                    this.setPoint(e.latlng.lat, e.latlng.lng);
                });
            } else {
                this.mapInstance.invalidateSize();
                this.mapInstance.setView([defaultLat, defaultLng], 15);
                this.markerInstance.setLatLng([defaultLat, defaultLng]);
            }

            this.setPoint(defaultLat, defaultLng);
        },

        async setPoint(lat, lng) {
            this.selectedCoords = { lat, lng };
            this.selectedAddressName = 'Memuat nama alamat...';

            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                const data = await res.json();
                if (data && data.display_name) {
                    this.selectedAddressName = data.display_name;
                } else {
                    this.selectedAddressName = `Koordinat: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                }
            } catch (e) {
                this.selectedAddressName = `Koordinat: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            }
        },

        async searchLocation() {
            if (!this.searchQuery.trim()) return;
            this.isSearching = true;

            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.searchQuery)}&limit=1`);
                const data = await res.json();
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);

                    this.mapInstance.setView([lat, lng], 16);
                    this.markerInstance.setLatLng([lat, lng]);
                    this.setPoint(lat, lng);
                } else {
                    alert('Lokasi tidak ditemukan. Coba ketik nama jalan atau daerah yang lebih spesifik.');
                }
            } catch (e) {
                console.warn('Search error:', e);
            } finally {
                this.isSearching = false;
            }
        },

        applySelectedLocation() {
            if (!this.selectedCoords) return;

            const lat = this.selectedCoords.lat;
            const lng = this.selectedCoords.lng;
            const mapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;

            @this.set('maps_url', mapsUrl);
            if (this.selectedAddressName) {
                @this.set('address', this.selectedAddressName);
            }

            this.closeMapModal();

            window.dispatchEvent(new CustomEvent('toast', {
                detail: { type: 'success', title: 'Alamat Dipilih', message: 'Titik Google Maps berhasil disalin ke form pelanggan.' }
            }));
        }
    };
}
</script>
