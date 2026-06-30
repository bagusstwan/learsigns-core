<x-filament-panels::page>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/hands/hands.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js" crossorigin="anonymous"></script>

    <!-- WRAPPER UTAMA: Dikunci tingginya agar pas 1 layar dan tidak ada scrollbar -->
    <div x-data="datasetRecorderComponent()" x-init="initRecorder()" style="height: calc(100vh - 220px); min-height: 550px;" class="flex flex-col lg:flex-row gap-6 w-full">
        
        <!-- ========================================== -->
        <!-- KOLOM KIRI (1:1 / 50% Lebar): KAMERA FULL  -->
        <!-- ========================================== -->
        <div class="w-full lg:w-1/2 h-full rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4 flex flex-col">
            <div class="relative w-full h-full overflow-hidden rounded-lg bg-black ring-1 ring-gray-950/10 dark:ring-white/20 shadow-inner flex-1">
                
                <!-- Video Element (object-cover agar mengisi kotak tanpa gepeng) -->
                <video id="webcam" autoplay playsinline class="absolute inset-0 w-full h-full object-cover transform -scale-x-100"></video>
                <canvas id="output_canvas" class="absolute inset-0 w-full h-full object-cover z-10 transform -scale-x-100"></canvas>
                
                <!-- Loading State Kamera -->
                <div x-show="!isCameraReady" class="absolute inset-0 bg-gray-900 z-20 flex flex-col items-center justify-center text-white space-y-4">
                    <svg class="animate-spin h-8 w-8 text-primary-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span class="text-sm tracking-widest text-gray-300 font-bold uppercase">Mengakses Kamera...</span>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- KOLOM KANAN (1:1 / 50% Lebar): FORM & STATUS -->
        <!-- ========================================== -->
        <div class="w-full lg:w-1/2 h-full flex flex-col gap-6">
            
            <!-- KANAN ATAS: FORM KONTROL -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6 flex-shrink-0">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium leading-6 text-gray-950 dark:text-white">Pilih Target Label</label>
                        <select x-model="selectedLabel" class="mt-2 block w-full rounded-lg border-0 bg-white py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:bg-gray-800 dark:text-white dark:ring-white/20 dark:focus:ring-primary-500 sm:text-sm sm:leading-6">
                            <option value="">Pilih Label Modul</option>
                            @foreach($availableLabels as $label)
                                <option value="{{ $label }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mt-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">Karakteristik Gestur</label>
                        <select x-model="gestureType" class="mt-2 block w-full rounded-lg border-0 bg-white py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:bg-gray-800 dark:text-white dark:ring-white/20 dark:focus:ring-primary-500 sm:text-sm sm:leading-6">
                            <option value="static">Static (Abjad / Diam)</option>
                            <option value="dynamic">Dynamic (Kata & Kalimat / Bergerak)</option>
                        </select>
                    </div>

                    <div class="pt-4">
                        <button 
                            @click="toggleRecording()" 
                            :disabled="!selectedLabel || !isCameraReady"
                            :class="isRecording ? 'bg-danger-600 hover:bg-danger-500 text-white ring-danger-600' : 'bg-primary-600 hover:bg-primary-500 text-white ring-primary-600'"
                            class="w-full flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-bold tracking-wide shadow-sm ring-1 ring-inset transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <div x-show="isRecording" class="w-2.5 h-2.5 rounded-full bg-white animate-pulse"></div>
                            <span x-text="isRecording ? 'HENTIKAN PEREKAMAN' : 'MULAI REKAM DATASET'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- KANAN BAWAH: STATUS PEREKAMAN (Mengisi sisa tinggi secara otomatis) -->
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6 flex-1 flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">Status Perekaman Data Studio</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sistem secara otomatis menangkap matriks koordinat setiap frame.</p>
                </div>

                <div class="grid grid-cols-2 gap-4 my-5 flex-1 content-center">
                    <div class="rounded-lg mt-3 bg-gray-50 p-4 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10 text-center">
                        <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kamera Studio</span>
                        <span x-text="isCameraReady ? 'ONLINE' : 'OFFLINE'" :class="isCameraReady ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400'" class="block text-lg font-bold mt-1"></span>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10 text-center">
                        <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sensor Tracking</span>
                        <span x-text="handDetected ? 'AKTIF' : 'KOSONG'" :class="handDetected ? 'text-success-600 dark:text-success-400' : 'text-gray-500 dark:text-gray-400'" class="block text-lg font-bold mt-1"></span>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-6 ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10 col-span-2 text-center flex flex-col justify-center items-center">
                        <span class="block text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Total Frame Dataset Tersimpan</span>
                        <span x-text="savedFrames" class="block text-5xl font-black text-gray-950 dark:text-white">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC COMPONENT -->
    <script>
        function datasetRecorderComponent() {
            return {
                selectedLabel: '', gestureType: 'static', isRecording: false, isCameraReady: false,
                handDetected: false, savedFrames: 0, lastSavedTime: 0,
                
                initRecorder() {
                    const videoElement = document.getElementById('webcam');
                    const canvasElement = document.getElementById('output_canvas');
                    const canvasCtx = canvasElement.getContext('2d');
                    
                    const hands = new Hands({ locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/hands/${file}` });
                    
                    // Definisikan opsi untuk kamera dan tangan
                    hands.setOptions({ 
                        maxNumHands: 2, 
                        modelComplexity: 1, 
                        minDetectionConfidence: 0.7, 
                        minTrackingConfidence: 0.7 
                    });
                    
                    hands.onResults((results) => {
                        canvasElement.width = videoElement.videoWidth; canvasElement.height = videoElement.videoHeight;
                        canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);
                        
                        if (results.multiHandLandmarks && results.multiHandLandmarks.length > 0) {
                            this.handDetected = true;
                            
                            // Loop melalui setiap tangan yang terdeteksi dan gambar konektor serta landmark
                            for (const landmarks of results.multiHandLandmarks) {
                                drawConnectors(canvasCtx, landmarks, HAND_CONNECTIONS, {color: '#000000', lineWidth: 2});
                                drawLandmarks(canvasCtx, landmarks, {color: '#FFFFFF', lineWidth: 1, radius: 2});
                            }
                            
                            // Kirim data ke backend jika sedang merekam dan sudah lewat 500ms sejak terakhir kali disimpan
                            if (this.isRecording && Date.now() - this.lastSavedTime >= 500) {
                                this.lastSavedTime = Date.now();
                                
                                // Kirim koordinat (sekarang mengirimkan seluruh array tangan yang terdeteksi)
                                this.$wire.dispatch('save-dataset-record', { 
                                    label: this.selectedLabel, 
                                    type: this.gestureType, 
                                    landmarks: results.multiHandLandmarks 
                                });
                                this.savedFrames++;
                            }
                        } else { 
                            this.handDetected = false; 
                        }
                    });
                    
                    const camera = new Camera(videoElement, { onFrame: async () => { await hands.send({image: videoElement}); }, width: 640, height: 480 });
                    camera.start().then(() => this.isCameraReady = true).catch(err => console.error("Error Kamera:", err));
                },
                
                toggleRecording() { this.isRecording = !this.isRecording; this.lastSavedTime = Date.now(); }
            }
        }
    </script>
</x-filament-panels::page>