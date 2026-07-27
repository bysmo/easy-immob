<div class="space-y-6">
    
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 dark:border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Modèles de Contrats & Mandats</h1>
                <x-badge color="indigo">{{ $templates->total() }} modèles</x-badge>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Gabarits de baux de location et mandats de gestion avec éditeur Rich Text, import Word (.docx) et variables automatiques.</p>
        </div>

        <x-button wire:click="openCreateModal" variant="primary" class="shadow-md shadow-emerald-600/20">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Nouveau modèle</span>
        </x-button>
    </div>

    <!-- Flash Message Notification -->
    @if(session('success'))
        <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 p-4 text-sm text-emerald-800 dark:text-emerald-200 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <x-icon name="check" class="w-4 h-4" />
                </div>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- DataTables Controls Top Bar -->
    <x-datatable.controls placeholder="Rechercher un modèle de contrat..." :perPage="$perPage" :search="$search">
        <x-slot:filters>
            <select wire:model.live="typeFilter" class="rounded-xl border-slate-200/80 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs font-medium py-2 px-3 focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                <option value="">Tous les types</option>
                <option value="lease">Bail locatif</option>
                <option value="management">Mandat de gestion</option>
            </select>
        </x-slot:filters>
    </x-datatable.controls>

    <!-- Data Table Container -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-200/80 dark:border-slate-800">
                    <tr>
                        <x-datatable.th field="name" :sortField="$sortField" :sortDirection="$sortDirection">Intitulé du gabarit</x-datatable.th>
                        <x-datatable.th field="type" :sortField="$sortField" :sortDirection="$sortDirection">Catégorie</x-datatable.th>
                        <x-datatable.th field="version" :sortField="$sortField" :sortDirection="$sortDirection">Version</x-datatable.th>
                        <x-datatable.th field="status" :sortField="$sortField" :sortDirection="$sortDirection">Statut</x-datatable.th>
                        <x-datatable.th align="right">Actions</x-datatable.th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($templates as $template)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $template->name }}
                                </div>
                                <div class="text-xs text-slate-400 font-medium">
                                    {{ $template->description ?? 'Aucune description' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :color="$template->type === 'management' ? 'purple' : 'emerald'">
                                    {{ $template->type === 'management' ? 'Mandat de Gestion' : 'Bail Locatif' }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-600 dark:text-slate-400">
                                v{{ $template->version }}
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :variant="$template->status === 'active' ? 'success' : 'muted'">
                                    {{ $template->status === 'active' ? 'Actif' : 'Inactif' }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="openEditModal({{ $template->id }})" 
                                        class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition-colors"
                                        title="Modifier">
                                    <x-icon name="edit" class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Aucun modèle de contrat disponible.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($templates->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                {{ $templates->links() }}
            </div>
        @endif
    </div>

    <!-- Styles Quill personnalisés pour le défilement interne et le thème sombre -->
    <style>
        .ql-container.ql-snow {
            border-bottom-left-radius: 1rem;
            border-bottom-right-radius: 1rem;
            border-color: #e2e8f0;
        }
        .ql-toolbar.ql-snow {
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            border-color: #e2e8f0;
            background-color: #f8fafc;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .ql-editor {
            max-height: 420px;
            min-height: 250px;
            overflow-y: auto;
            font-size: 0.875rem;
            line-height: 1.6;
        }
        .dark .ql-toolbar.ql-snow {
            background-color: #1e293b;
            border-color: #334155;
            color: #f8fafc;
        }
        .dark .ql-container.ql-snow {
            border-color: #334155;
            background-color: #0f172a;
            color: #f8fafc;
        }
        .dark .ql-stroke {
            stroke: #94a3b8 !important;
        }
        .dark .ql-fill {
            fill: #94a3b8 !important;
        }
        .dark .ql-picker {
            color: #94a3b8 !important;
        }
        .dark .ql-picker-options {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }
    </style>

    <!-- Modal d'édition/création avec Défilement Interne & Barre d'Actions Fixe -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-3 sm:p-6"
             x-data="{
                content: @entangle('content'),
                quill: null,
                init() {
                    this.$nextTick(() => {
                        if (!this.$refs.quillEditor) return;
                        this.quill = new Quill(this.$refs.quillEditor, {
                            theme: 'snow',
                            placeholder: 'Rédigez ou collez le contenu du contrat ici...',
                            modules: {
                                toolbar: [
                                    [{ 'header': [1, 2, 3, false] }],
                                    ['bold', 'italic', 'underline', 'strike'],
                                    [{ 'color': [] }, { 'background': [] }],
                                    [{ 'align': [] }],
                                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                    ['clean']
                                ]
                            }
                        });

                        if (this.content) {
                            this.quill.root.innerHTML = this.content;
                        }

                        this.quill.on('text-change', () => {
                            this.content = this.quill.root.innerHTML;
                        });

                        this.$watch('content', (newVal) => {
                            if (this.quill && newVal !== this.quill.root.innerHTML) {
                                this.quill.root.innerHTML = newVal || '';
                            }
                        });
                    });
                },
                insertVariable(varCode) {
                    if (!this.quill) return;
                    const range = this.quill.getSelection(true) || { index: this.quill.getLength() };
                    this.quill.insertText(range.index, varCode);
                    this.quill.setSelection(range.index + varCode.length);
                }
             }">
            
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 max-w-4xl w-full max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
                
                <!-- Modal Header Pinned Top -->
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between shrink-0 bg-white dark:bg-slate-900 z-10">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                            {{ $editingTemplateId ? 'Modifier le gabarit' : 'Créer un gabarit de contrat' }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Éditeur de texte riche avec défilement interne et insertion de variables.</p>
                    </div>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg">
                        <x-icon name="x" class="w-6 h-6" />
                    </button>
                </div>

                @if(session('success_docx'))
                    <div class="mx-6 mt-4 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-xs font-semibold text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 flex items-center gap-2 shrink-0">
                        <x-icon name="check" class="w-4 h-4 text-emerald-600" />
                        <span>{{ session('success_docx') }}</span>
                    </div>
                @endif

                <!-- Modal Body Scrollable -->
                <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6 scrollbar-thin">
                    <form id="templateForm" wire:submit="save" class="space-y-6">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <x-label for="tplName">Intitulé du gabarit</x-label>
                                <x-input id="tplName" type="text" wire:model="name" placeholder="ex: Contrat de bail habitation meublée, Mandat de gestion..." required />
                                @error('name') <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <x-label for="tplType">Type de contrat</x-label>
                                <select id="tplType" wire:model="type" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-bold py-2.5 px-3 focus:ring-2 focus:ring-emerald-500">
                                    <option value="lease">Bail Locatif</option>
                                    <option value="management">Mandat de Gestion (Bailleur)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Zone d'importation Word (.docx) -->
                        <div class="p-4 rounded-2xl bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-950/30 dark:to-purple-950/30 border border-indigo-200/80 dark:border-indigo-900/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-md shadow-indigo-600/20">
                                    DOCX
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white">Importer un fichier Word (.docx)</h4>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Extraire le texte, les titres et paragraphes Word dans l'éditeur ci-dessous.</p>
                                </div>
                            </div>

                            <div class="relative shrink-0">
                                <label class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm cursor-pointer transition inline-flex items-center gap-2">
                                    <x-icon name="upload" class="w-4 h-4" />
                                    <span>Sélectionner le fichier Word</span>
                                    <input type="file" wire:model.live="wordFile" accept=".docx" class="hidden">
                                </label>
                                <div wire:loading wire:target="wordFile" class="absolute inset-0 bg-indigo-600 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-2">
                                    <span>Importation...</span>
                                </div>
                            </div>
                        </div>
                        @error('wordFile') <span class="text-xs text-rose-600 font-medium block">{{ $message }}</span> @enderror

                        <!-- Helper Variables Automatiques Cliquables -->
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 dark:text-white">Variables automatiques (Cliquez pour insérer à la position du curseur) :</span>
                            </div>
                            <div class="flex flex-wrap gap-1.5 pt-1 max-h-36 overflow-y-auto scrollbar-thin">
                                @foreach($availableVariables as $groupName => $vars)
                                    @foreach($vars as $code => $label)
                                        <button type="button" 
                                                @click="insertVariable('{{ $code }}')"
                                                title="Cliquez pour insérer : {{ $label }}" 
                                                class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 font-mono text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition shrink-0">
                                            {{ $code }}
                                        </button>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>

                        <!-- Éditeur Rich Text (Quill.js) avec Défilement Interne -->
                        <div>
                            <x-label class="mb-2 block font-bold">Contenu du contrat (Éditeur Rich Text)</x-label>
                            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
                                <div x-ref="quillEditor" class="text-sm leading-relaxed text-slate-900 dark:text-slate-100"></div>
                            </div>
                            @error('content') <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </form>
                </div>

                <!-- Modal Footer Pinned Bottom (Always Visible) -->
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3 shrink-0 bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-xs z-10">
                    <x-button type="button" variant="secondary" wire:click="$set('showModal', false)">Annuler</x-button>
                    <x-button type="submit" form="templateForm" variant="primary">Enregistrer le modèle</x-button>
                </div>
            </div>
        </div>
    @endif
</div>
