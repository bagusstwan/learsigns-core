<x-filament-panels::page>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/hands/hands.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js" crossorigin="anonymous"></script>

    <div 
        x-data="datasetRecorderComponent()" 
        x-init="initRecorder()" 
        @trigger-export-json.window="exportJsonData()"
        @trigger-clear-memory.window="clearMemory(true)"
        style="height: calc(100vh - 220px); min-height: 600px;" 
        class="flex flex-col lg:flex-row gap-6 w-full"
    >
        
        <div class="w-full lg:w-1/2 h-full rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4 flex flex-col">
            <div class="relative w-full h-full overflow-hidden rounded-lg bg-black ring-1 ring-gray-950/10 dark:ring-white/20 shadow-inner flex-1">
                
                <video id="webcam" autoplay playsinline class="absolute inset-0 w-full h-full object-cover transform -scale-x-100"></video>
                <canvas id="output_canvas" class="absolute inset-0 w-full h-full object-cover z-10 transform -scale-x-100"></canvas>
                
                <div x-show="!isCameraReady" class="absolute inset-0 bg-gray-900 z-20 flex flex-col items-center justify-center text-white space-y-4">
                    <svg class="animate-spin h-8 w-8 text-primary-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span class="text-sm tracking-widest text-gray-300 font-bold uppercase">Mengakses Sensor Visual Terintegrasi</span>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-1/2 h-full flex flex-col gap-6">
            
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6 flex-shrink-0">
                <div class="space-y-4">
                    
                    <div>
                        <label class="block text-sm font-medium leading-6 text-gray-950 dark:text-white">Kategori Modul Pembelajaran</label>
                        <select x-model="selectedCategory" @change="filterLabels()" class="mt-2 block w-full rounded-lg border-0 bg-white py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:bg-gray-800 dark:text-white dark:ring-white/20 dark:focus:ring-primary-500 sm:text-sm sm:leading-6">
                            <option value="all">Semua Kategori</option>
                            <option value="abjad">Abjad Dasar</option>
                            <option value="kata">Kosa Kata Menengah</option>
                            <option value="kalimat">Kalimat Lanjutan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block mt-2 text-sm font-medium leading-6 text-gray-950 dark:text-white">Pilih Target Klasifikasi Data</label>
                        <select x-model="selectedLabel" class="mt-2 block w-full rounded-lg border-0 bg-white py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:bg-gray-800 dark:text-white dark:ring-white/20 dark:focus:ring-primary-500 sm:text-sm sm:leading-6">
                            <option value="">Pilih Label Kosa Kata</option>
                            <template x-for="label in filteredLabels" :key="label">
                                <option :value="label" x-text="label"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block mt-2 text-sm font-medium leading-6 text-gray-950 dark:text-white">Tentukan Karakteristik Rekaman</label>
                        <select x-model="gestureType" class="mt-2 block w-full rounded-lg border-0 bg-white py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:bg-gray-800 dark:text-white dark:ring-white/20 dark:focus:ring-primary-500 sm:text-sm sm:leading-6">
                            <option value="static">Statis Objek Diam</option>
                            <option value="dynamic">Dinamis Rangkaian Gerak Beruntun</option>
                        </select>
                    </div>

                    <div class="pt-4 flex flex-col gap-3">
                        <button 
                            @click="toggleRecording()" 
                            :disabled="!selectedLabel || !isCameraReady"
                            :class="isRecording ? 'bg-danger-600 hover:bg-danger-500 text-white ring-danger-600' : 'bg-primary-600 hover:bg-primary-500 text-white ring-primary-600'"
                            class="w-full flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-bold tracking-wide shadow-sm ring-1 ring-inset transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <div x-show="isRecording" class="w-2.5 h-2.5 rounded-full bg-white animate-pulse"></div>
                            <span x-text="isRecording ? 'HENTIKAN TANGKAPAN SENSOR' : 'MULAI TANGKAPAN SENSOR BARU'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6 flex-1 flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">Panel Indikator Penyimpanan Lokal</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gunakan tombol di sudut kanan atas untuk mengekspor atau membersihkan data.</p>
                </div>

                <div class="grid grid-cols-2 gap-4 my-5 flex-1 content-center">
                    <div class="rounded-lg mt-3 bg-gray-50 p-4 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10 text-center flex flex-col justify-center">
                        <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Kamera Studio</span>
                        <span x-text="isCameraReady ? 'DARING' : 'LURING'" :class="isCameraReady ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400'" class="block text-lg font-bold mt-1"></span>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10 text-center flex flex-col justify-center">
                        <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deteksi Titik Tangan</span>
                        <span x-text="handDetected ? 'TERKUNCI' : 'KEHILANGAN JEJAK'" :class="handDetected ? 'text-success-600 dark:text-success-400' : 'text-gray-500 dark:text-gray-400'" class="block text-lg font-bold mt-1"></span>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-6 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10 col-span-2 text-center flex flex-col justify-center items-center">
                        <span class="block text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Total Frame Tersimpan di Memori</span>
                        <span x-text="savedFrames" class="block text-5xl font-black text-gray-950 dark:text-white">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function datasetRecorderComponent() {
            return {
                /** State Data Opsi Dinamis */
                rawModules: {!! $modulesDataJson !!},
                filteredLabels: [],
                
                selectedCategory: 'all',
                selectedLabel: '',
                gestureType: 'dynamic',
                isRecording: false,
                isCameraReady: false,
                handDetected: false,
                savedFrames: 0,
                
                /** Memori utama penyimpanan lokal di peramban */
                recordedDataset: [],
                currentSequence: [],
                
                initRecorder() {
                    /** Memuat opsi label pertama kali saat halaman dibuka */
                    this.filterLabels();

                    const videoElement = document.getElementById('webcam');
                    const canvasElement = document.getElementById('output_canvas');
                    const canvasCtx = canvasElement.getContext('2d');
                    
                    const hands = new Hands({ locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/hands/${file}` });
                    
                    hands.setOptions({ 
                        maxNumHands: 2, 
                        modelComplexity: 1, 
                        minDetectionConfidence: 0.7, 
                        minTrackingConfidence: 0.7 
                    });
                    
                    hands.onResults((results) => {
                        canvasElement.width = videoElement.videoWidth; 
                        canvasElement.height = videoElement.videoHeight;
                        canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);
                        
                        if (results.multiHandLandmarks && results.multiHandLandmarks.length > 0) {
                            this.handDetected = true;
                            
                            for (const landmarks of results.multiHandLandmarks) {
                                drawConnectors(canvasCtx, landmarks, HAND_CONNECTIONS, {color: '#000000', lineWidth: 2});
                                drawLandmarks(canvasCtx, landmarks, {color: '#FFFFFF', lineWidth: 1, radius: 2});
                            }
                            
                            if (this.isRecording) {
                                let flattenedLandmarks = [];
                                for (const hand of results.multiHandLandmarks) {
                                    for (const point of hand) {
                                        flattenedLandmarks.push(point.x, point.y, point.z);
                                    }
                                }
                                
                                if (this.gestureType === 'static') {
                                    this.recordedDataset.push({
                                        label: this.selectedLabel,
                                        type: this.gestureType,
                                        landmarks: flattenedLandmarks
                                    });
                                    this.savedFrames++;
                                } else {
                                    this.currentSequence.push(flattenedLandmarks);
                                    this.savedFrames++;
                                }
                            }
                        } else { 
                            this.handDetected = false; 
                        }
                    });
                    
                    const camera = new Camera(videoElement, { onFrame: async () => { await hands.send({image: videoElement}); }, width: 640, height: 480 });
                    camera.start().then(() => { this.isCameraReady = true; }).catch(err => { console.error("Hardware Blocked", err); });
                },
                
                /** Logika filter dinamis sisi klien berdasarkan level_type basis data */
                filterLabels() {
                    this.selectedLabel = ''; 
                    
                    if (this.selectedCategory === 'all') {
                        this.filteredLabels = [...new Set(this.rawModules.map(m => m.target_gesture))];
                    } else {
                        this.filteredLabels = [...new Set(
                            this.rawModules
                                .filter(m => m.level_type === this.selectedCategory)
                                .map(m => m.target_gesture)
                        )];
                    }
                },

                toggleRecording() { 
                    this.isRecording = !this.isRecording; 
                    
                    /** Menggabungkan matriks saat mode dinamis dihentikan */
                    if (!this.isRecording && this.gestureType === 'dynamic') {
                        if (this.currentSequence.length > 0) {
                            this.recordedDataset.push({
                                label: this.selectedLabel,
                                type: this.gestureType,
                                sequence: this.currentSequence
                            });
                            this.currentSequence = [];
                        }
                    }
                },

                exportJsonData() {
                    if (this.recordedDataset.length === 0) {
                        new FilamentNotification()
                            .title('Dataset Kosong')
                            .body('Belum ada dataset yang direkam ke dalam memori.')
                            .warning()
                            .send();
                        return;
                    }
                    
                    const dataString = JSON.stringify(this.recordedDataset);
                    const blob = new Blob([dataString], { type: 'application/json' });
                    const blobUrl = URL.createObjectURL(blob);
                    
                    const triggerLink = document.createElement('a');
                    triggerLink.href = blobUrl;
                    triggerLink.download = `dataset_studio_${this.selectedLabel}.json`;
                    document.body.appendChild(triggerLink);
                    triggerLink.click();
                    document.body.removeChild(triggerLink);
                    URL.revokeObjectURL(blobUrl);
                    
                    /** Memanggil pembersihan memori dan notifikasi sukses bawaan Filament */
                    this.clearMemory(false);
                    
                    new FilamentNotification()
                        .title('Ekspor Berhasil')
                        .body('Dataset JSON berhasil diunduh dan memori telah disterilkan.')
                        .success()
                        .send();
                },

                clearMemory(showPrompt = true) {
                    this.recordedDataset = [];
                    this.currentSequence = [];
                    this.savedFrames = 0;
                    
                    if (showPrompt) {
                        new FilamentNotification()
                            .title('Memori Dikosongkan')
                            .body('Seluruh matriks data pada memori peramban berhasil dibersihkan.')
                            .success()
                            .send();
                    }
                }
            }
        }
    </script>
</x-filament-panels::page>