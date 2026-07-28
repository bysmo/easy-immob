<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Signaler un incident ou demande de réparation</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Décrivez le problème rencontré dans votre logement.</p>
        </div>
        <a href="{{ route('incidents.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 text-xs font-semibold transition flex items-center gap-1.5">
            <x-icon name="arrow-left" class="w-4 h-4" />
            <span>Retour</span>
        </a>
    </div>

    <form wire:submit="save" class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-6">
        
        <!-- Sélection du logement / bail -->
        <div>
            <x-label for="lease_id" :required="true">Bien / Logement concerné</x-label>
            <select wire:model="lease_id" id="lease_id" class="w-full px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
                <option value="">-- Sélectionnez votre logement --</option>
                @foreach ($leases as $lease)
                    <option value="{{ $lease->id }}">
                        {{ $lease->property?->title ?? 'Bien loué' }}@if($lease->property?->address) ({{ $lease->property->address }})@endif — Bail {{ $lease->reference }}
                    </option>
                @endforeach
            </select>
            @error('lease_id') <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
        </div>

        <!-- Titre -->
        <div>
            <x-label for="title" :required="true">Objet / Intitulé du problème</x-label>
            <x-input wire:model="title" type="text" id="title" placeholder="Ex: Fuite d'eau sous l'évier de la cuisine" :error="$errors->first('title')" />
        </div>

        <!-- Description -->
        <div>
            <x-label for="description" :required="true">Explication détaillée du problème</x-label>
            <textarea wire:model="description" id="description" rows="4" placeholder="Expliquez en détail l'incident, sa localisation et son impact..." class="w-full px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-hidden"></textarea>
            @error('description') <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
        </div>

        <!-- Priorité -->
        <div>
            <x-label for="priority" :required="true">Niveau d'urgence</x-label>
            <select wire:model="priority" id="priority" class="w-full px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-hidden">
                <option value="low">Faible — Peut attendre quelques jours</option>
                <option value="medium">Moyenne — Nécessite une intervention normale</option>
                <option value="high">Haute — Gênant au quotidien</option>
                <option value="urgent">Urgente — Danger ou dégât des eaux actif</option>
            </select>
            @error('priority') <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
        </div>

        <!-- Enregistrement / Note vocale Directe Mobile -->
        <div x-data="{
                recording: false,
                recordedBlob: null,
                audioUrl: null,
                mediaRecorder: null,
                audioChunks: [],
                timer: 0,
                timerInterval: null,
                mimeType: '',
                async startRecording() {
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        
                        const types = [
                            'audio/webm;codecs=opus',
                            'audio/webm',
                            'audio/mp4',
                            'audio/aac',
                            'audio/ogg',
                            'audio/wav'
                        ];
                        this.mimeType = types.find(t => typeof MediaRecorder !== 'undefined' && MediaRecorder.isTypeSupported(t)) || '';
                        
                        const options = this.mimeType ? { mimeType: this.mimeType } : {};
                        this.mediaRecorder = new MediaRecorder(stream, options);
                        this.audioChunks = [];
                        
                        this.mediaRecorder.ondataavailable = (e) => {
                            if (e.data && e.data.size > 0) this.audioChunks.push(e.data);
                        };

                        this.mediaRecorder.onstop = () => {
                            const actualType = this.mediaRecorder.mimeType || this.mimeType || 'audio/webm';
                            const blob = new Blob(this.audioChunks, { type: actualType });
                            this.recordedBlob = blob;
                            if (this.audioUrl) URL.revokeObjectURL(this.audioUrl);
                            this.audioUrl = URL.createObjectURL(blob);

                            let ext = 'webm';
                            if (actualType.includes('mp4') || actualType.includes('aac')) {
                                ext = 'm4a';
                            } else if (actualType.includes('ogg')) {
                                ext = 'ogg';
                            } else if (actualType.includes('wav')) {
                                ext = 'wav';
                            }

                            const file = new File([blob], 'note_vocale_' + Date.now() + '.' + ext, { type: actualType });
                            $wire.upload('audio', file, 
                                () => console.log('Audio uploaded to Livewire successfully'), 
                                (err) => console.error('Audio upload error:', err)
                            );
                        };

                        this.mediaRecorder.start();
                        this.recording = true;
                        this.timer = 0;
                        this.timerInterval = setInterval(() => this.timer++, 1000);
                    } catch (err) {
                        alert('Impossible d\'accéder au microphone : ' + err.message);
                    }
                },
                stopRecording() {
                    if (this.mediaRecorder && this.recording) {
                        this.mediaRecorder.stop();
                        if (this.mediaRecorder.stream) {
                            this.mediaRecorder.stream.getTracks().forEach(track => track.stop());
                        }
                        this.recording = false;
                        clearInterval(this.timerInterval);
                    }
                },
                formatTimer(seconds) {
                    const m = String(Math.floor(seconds / 60)).padStart(2, '0');
                    const s = String(seconds % 60).padStart(2, '0');
                    return `${m}:${s}`;
                }
            }" 
            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-3">
            
            <x-label for="audio">Note vocale audio (Enregistreur vocal téléphone)</x-label>
            <p class="text-xs text-slate-500">Parlez directement dans le micro de votre téléphone ou importez un fichier audio préparé.</p>

            <!-- Buttons Enregistreur Vocal Natif -->
            <div class="flex flex-wrap items-center gap-3">
                <template x-if="!recording">
                    <button type="button" @click="startRecording()" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                        </svg>
                        <span>🎙️ Enregistrer une note vocale</span>
                    </button>
                </template>

                <template x-if="recording">
                    <button type="button" @click="stopRecording()" class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold text-xs transition flex items-center gap-2 animate-pulse shadow-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-white"></span>
                        <span>Arrêter l'enregistrement (<span x-text="formatTimer(timer)">00:00</span>)</span>
                    </button>
                </template>

                <template x-if="audioUrl">
                    <div class="flex items-center gap-2 bg-emerald-950/20 dark:bg-emerald-950/60 p-2 rounded-xl border border-emerald-500/30">
                        <audio :src="audioUrl" controls class="h-8 max-w-[200px]"></audio>
                        <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold">Prêt à l'envoi</span>
                    </div>
                </template>
            </div>

            <!-- Alternative Fichier Fixe -->
            <div class="pt-2 border-t border-slate-200/50 dark:border-slate-700/50">
                <span class="text-[11px] text-slate-400 block mb-1">Ou choisissez un fichier audio existant :</span>
                <input type="file" wire:model="audio" id="audio" accept="audio/*,video/webm,video/mp4" class="text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer" />
            </div>

            @error('audio') <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
        </div>

        <!-- Photos -->
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
            <x-label for="photos">Photos de l'incident (Optionnel)</x-label>
            <p class="text-xs text-slate-500">Prenez des photos montrant le problème.</p>
            <input type="file" wire:model="photos" id="photos" multiple accept="image/*" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer" />
            @error('photos.*') <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
        </div>

        <!-- Vidéos -->
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
            <x-label for="videos">Vidéos de l'incident (Optionnel)</x-label>
            <p class="text-xs text-slate-500">Ajoutez des courtes séquences vidéos si nécessaire.</p>
            <input type="file" wire:model="videos" id="videos" multiple accept="video/*" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer" />
            @error('videos.*') <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p> @enderror
        </div>

        <!-- Submit Button -->
        <div class="pt-4 flex justify-end gap-3">
            <a href="{{ route('incidents.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition">
                Annuler
            </a>
            <x-button variant="primary" class="shadow-md shadow-emerald-600/20" wire:loading.attr="disabled">
                <span wire:loading.remove>Transmettre le signalement</span>
                <span wire:loading>Enregistrement en cours...</span>
            </x-button>
        </div>
    </form>
</div>
