<x-filament-panels::page>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/hands/hands.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js" crossorigin="anonymous"></script>

    <!-- WRAPPER UTAMA: Dikunci tingginya agar pas 1 layar dan tidak ada scrollbar -->
    <div x-data="aiTestingComponent()" x-init="initAi()" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        
        <!-- BAGIAN KIRI: SENSOR KAMERA -->
        <x-filament::section>
            <x-slot name="heading">
                Sensor Kamera Live
            </x-slot>
            <x-slot name="description">
                Arahkan tangan Anda ke layar.
            </x-slot>

            <div style="position: relative; width: 100%; height: 380px; background-color: #0b0f19; border-radius: 0.5rem; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <video id="webcam_test" autoplay playsinline style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
                <canvas id="canvas_test" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 10; transform: scaleX(-1);"></canvas>
                
                <!-- Loading State Dinamis dengan Pesan Status -->
                <div x-show="!isModelReady" style="position: absolute; inset: 0; z-index: 20; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: rgba(17, 24, 39, 0.95); color: white; font-size: 0.75rem; font-weight: bold; letter-spacing: 0.1em; text-align: center; padding: 1rem;">
                    <x-filament::loading-indicator class="h-8 w-8 text-primary-500 mb-4" x-show="!hasError" />
                    <span x-text="statusText">MEMULAI INISIALISASI...</span>
                </div>
            </div>
        </x-filament::section>

        <!-- BAGIAN KANAN: SISTEM ANALISIS -->
        <x-filament::section>
            <x-slot name="heading">
                Sistem Analisis Prediksi
            </x-slot>
            <x-slot name="description">
                Output identifikasi real-time.
            </x-slot>

            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem 1rem;">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Huruf Terdeteksi</div>
                
                <div class="text-8xl font-black text-primary-500 mb-8 drop-shadow-md leading-none">
                    <span x-text="predictedLabel">_</span>
                </div>
                
                <div class="w-full">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Tingkat Akurasi</span>
                        <span class="text-sm font-black text-primary-500" x-text="(confidenceScore * 100).toFixed(1) + '%'">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-3 overflow-hidden shadow-inner">
                        <div class="bg-primary-500 h-3 transition-all duration-150 ease-out rounded-full" :style="'width: ' + (confidenceScore * 100) + '%'"></div>
                    </div>
                </div>
            </div>
        </x-filament::section>

    </div>

    <script>
        function aiTestingComponent() {
            return {
                isModelReady: false,
                hasError: false,
                statusText: 'MEMULAI INISIALISASI...',
                predictedLabel: '-',
                confidenceScore: 0,
                
                labels: ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'],
                
                async initAi() {
                    try {
                        // Fase 1: Memuat Model
                        this.statusText = 'MENGUNDUH FILE MODEL.JSON...';
                        const model = await tf.loadLayersModel('/ai-models/abjad/model.json?v=' + new Date().getTime());
                        
                        // Fase 2: Memuat Sensor MediaPipe
                        this.statusText = 'MEMUAT SENSOR TRACKING JARI...';
                        const videoElement = document.getElementById('webcam_test');
                        const canvasElement = document.getElementById('canvas_test');
                        const canvasCtx = canvasElement.getContext('2d');
                        
                        const hands = new Hands({ locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/hands/${file}` });
                        hands.setOptions({ 
                            maxNumHands: 2, 
                            modelComplexity: 1,
                            minDetectionConfidence: 0.75,
                            minTrackingConfidence: 0.75
                        });
                        
                        hands.onResults(async (results) => {
                            canvasElement.width = videoElement.videoWidth; 
                            canvasElement.height = videoElement.videoHeight;
                            canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);
                            
                            if (results.multiHandLandmarks && results.multiHandLandmarks.length > 0) {
                                let flattened = [];
                                for (const landmarks of results.multiHandLandmarks) {
                                    drawConnectors(canvasCtx, landmarks, HAND_CONNECTIONS, {color: '#000000', lineWidth: 2});
                                    drawLandmarks(canvasCtx, landmarks, {color: '#FFFFFF', lineWidth: 1, radius: 2});
                                    
                                    for (const point of landmarks) {
                                        flattened.push(point.x, point.y, point.z);
                                    }
                                }
                                
                                while (flattened.length < 126) {
                                    flattened.push(0.0, 0.0, 0.0);
                                }
                                
                                tf.tidy(() => {
                                    const inputTensor = tf.tensor2d([flattened.slice(0, 126)]);
                                    const prediction = model.predict(inputTensor);
                                    
                                    const maxIndex = prediction.argMax(-1).dataSync()[0];
                                    const maxConfidence = prediction.max().dataSync()[0];
                                    
                                    if (maxConfidence > 0.6) {
                                        this.predictedLabel = this.labels[maxIndex] || '-';
                                        this.confidenceScore = maxConfidence;
                                    } else {
                                        this.predictedLabel = '-';
                                        this.confidenceScore = 0;
                                    }
                                });
                            } else {
                                this.predictedLabel = '-';
                                this.confidenceScore = 0;
                            }
                        });
                        
                        // Fase 3: Mengaktifkan Kamera
                        this.statusText = 'MEMBUKA KAMERA (MOHON IZINKAN AKSES DI BROWSER)...';
                        const camera = new Camera(videoElement, { onFrame: async () => { await hands.send({image: videoElement}); }, width: 640, height: 480 });
                        
                        await camera.start();
                        this.isModelReady = true;

                    } catch (error) {
                        console.error("Kesalahan Sistem AI:", error);
                        this.hasError = true;
                        this.statusText = 'GAGAL MEMUAT: ' + error.message;
                    }
                }
            }
        }
    </script>
</x-filament-panels::page>