@extends('admin.layout')

@section('title', 'Gestion des Plats')
@section('page-title', 'Gestion des Plats')

@section('content')
    <!-- Messages -->
    @if(session('success'))
        <div style="background-color: #10B981; color: white; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span style="font-weight: 600;">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="background-color: #EF4444; color: white; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span style="font-weight: 600;">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div style="background-color: #FEE2E2; color: #991B1B; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #EF4444;">
            <div style="font-weight: 700; margin-bottom: 0.5rem;">Erreurs de validation</div>
            <ul style="list-style: disc; margin-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <a href="{{ route('admin.menu.index') }}" style="color: #D9542A; text-decoration: none; font-weight: 600; margin-bottom: 0.5rem; display: inline-block;">
                ← Retour au menu
            </a>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #3A3A3A;">Gestion des Plats</h2>
            <p style="color: #666; margin-top: 0.25rem;">{{ $plats->count() }} plat(s) au total</p>
        </div>
        <button onclick="openSidebar('create')" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter un plat
        </button>
    </div>

    <!-- Plats List -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
        @forelse($plats as $plat)
        <div class="card" style="overflow: hidden;">
            <!-- Image -->
            <div style="height: 180px; background: linear-gradient(135deg, #FDFBF8, #F5F5F5); display: flex; align-items: center; justify-content: center; position: relative;">
                @if($plat->image)
                    <img src="{{ asset('storage/' . $plat->image) }}" alt="{{ $plat->nom }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <svg style="width: 80px; height: 80px; color: #D9542A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                @endif
                
                <!-- Status Badge -->
                <div style="position: absolute; top: 0.5rem; right: 0.5rem;">
                    @if($plat->est_disponible)
                        <span class="badge badge-success">Disponible</span>
                    @else
                        <span class="badge badge-danger">Indisponible</span>
                    @endif
                </div>
            </div>

            <!-- Content -->
            <div style="padding: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #3A3A3A; margin-bottom: 0.5rem;">
                    {{ $plat->nom }}
                </h3>
                <p style="color: #666; font-size: 0.875rem; margin-bottom: 0.5rem;">
                    <span class="badge" style="background-color: #FDFBF8; color: #D9542A; padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                        {{ $plat->categorie->nom }}
                    </span>
                </p>
                <div style="font-size: 1.5rem; font-weight: 800; color: #D9542A; margin-bottom: 1rem;">
                    {{ number_format($plat->prix, 0, ',', ' ') }} FCFA
                </div>

                @if($plat->detail)
                <p style="color: #666; font-size: 0.875rem; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $plat->detail }}
                </p>
                @endif

                <!-- Extras Info -->
                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.75rem;">
                    @if($plat->accompagnements->count() > 0)
                        <span style="background-color: #F0FDF4; color: #166534; padding: 0.25rem 0.5rem; border-radius: 4px;">
                            {{ $plat->accompagnements->count() }} accompagnement(s)
                        </span>
                    @endif
                    @if($plat->options->count() > 0)
                        <span style="background-color: #FEF3C7; color: #92400E; padding: 0.25rem 0.5rem; border-radius: 4px;">
                            {{ $plat->options->count() }} option(s)
                        </span>
                    @endif
                </div>

                <!-- Actions -->
                <div style="display: grid; grid-template-columns: 1fr auto auto auto; gap: 0.5rem; align-items: center;">
                    <!-- Switch -->
                    <form action="{{ route('admin.menu.plats.toggle', $plat->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <label class="switch">
                            <input type="checkbox" {{ $plat->est_disponible ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="slider round"></span>
                        </label>
                    </form>

                    <!-- Edit Button -->
                    <button onclick='openSidebar("edit", {{ $plat->id }}, @json($plat))' class="btn-icon btn-icon-warning">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>

                    <!-- Manage Extras Button -->
                    <button onclick='openSidebar("extras", {{ $plat->id }}, @json($plat))' class="btn-icon" style="background-color: #3A3A3A;">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </button>

                    <!-- Delete Button -->
                    <form action="{{ route('admin.menu.plats.destroy', $plat->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce plat ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-icon btn-icon-danger">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #666;">
            <svg style="width: 80px; height: 80px; margin: 0 auto 1rem; color: #D9542A; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <p style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">Aucun plat disponible</p>
            <p>Cliquez sur "Ajouter un plat" pour commencer</p>
        </div>
        @endforelse
    </div>

    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9998;" onclick="closeSidebar()"></div>

    <!-- Sidebar Panel -->
    <div id="sidebarPanel" style="display: none; position: fixed; top: 0; right: 0; width: 600px; max-width: 90vw; height: 100%; background: white; box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15); z-index: 9999; overflow-y: auto; transform: translateX(100%); transition: transform 0.3s ease;">
        <div style="padding: 2rem;">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 2px solid #E5E5E5;">
                <h3 id="sidebarTitle" style="font-size: 1.5rem; font-weight: 700; color: #3A3A3A;"></h3>
                <button onclick="closeSidebar()" style="background: none; border: none; font-size: 2rem; cursor: pointer; color: #666; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">×</button>
            </div>

            <!-- Create/Edit Plat Form -->
            <form id="platForm" method="POST" enctype="multipart/form-data" style="display: none;">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div style="display: grid; gap: 1.5rem;">
                    <!-- Nom -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                            Nom du plat <span style="color: #D9542A;">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nom" 
                            id="platNom"
                            required
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            placeholder="Ex: Riz au gras"
                        >
                    </div>

                    <!-- Catégorie -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                            Catégorie <span style="color: #D9542A;">*</span>
                        </label>
                        <select 
                            name="categorie_id" 
                            id="platCategorie"
                            required
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                        >
                            <option value="">Sélectionnez une catégorie</option>
                            @foreach($categories as $categorie)
                                <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Prix -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                            Prix (FCFA) <span style="color: #D9542A;">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="prix" 
                            id="platPrix"
                            required
                            min="0"
                            step="0.01"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            placeholder="Ex: 2500"
                        >
                    </div>

                    <!-- Image -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                            Image du plat
                        </label>
                        <input 
                            type="file" 
                            name="image" 
                            accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            onchange="validateFileSize(this, 5)"
                        >
                        <small style="color: #666; font-size: 0.75rem;">Format: JPG, PNG, GIF, WebP (Max 5 Mo)</small>
                    </div>

                    <!-- Détails -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                            Détails / Description
                        </label>
                        <textarea 
                            name="detail" 
                            id="platDetail"
                            rows="4"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem; resize: vertical;"
                            placeholder="Description du plat, ingrédients, etc."
                        ></textarea>
                    </div>

                    <!-- Disponible -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="est_disponible" value="1" id="platDisponible" checked style="width: 18px; height: 18px;">
                            <span style="font-weight: 600; color: #3A3A3A;">Plat disponible</span>
                        </label>
                    </div>

                    <!-- Accompagnements Section -->
                    <div style="border-top: 2px solid #E5E5E5; padding-top: 1.5rem; margin-top: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <h4 style="font-size: 1rem; font-weight: 700; color: #3A3A3A; margin: 0;">Accompagnements</h4>
                            <button type="button" onclick="addAccompagnementField()" class="btn btn-secondary" style="padding: 0.5rem 0.75rem; font-size: 0.875rem;">
                                + Ajouter
                            </button>
                        </div>
                        <div id="accompagnementsContainer"></div>
                    </div>

                    <!-- Options Section -->
                    <div style="border-top: 2px solid #E5E5E5; padding-top: 1.5rem; margin-top: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <h4 style="font-size: 1rem; font-weight: 700; color: #3A3A3A; margin: 0;">Options</h4>
                            <button type="button" onclick="addOptionField()" class="btn btn-secondary" style="padding: 0.5rem 0.75rem; font-size: 0.875rem;">
                                + Ajouter
                            </button>
                        </div>
                        <div id="optionsContainer"></div>
                    </div>
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #E5E5E5;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <span id="submitBtnText">Créer le plat</span>
                    </button>
                    <button type="button" onclick="closeSidebar()" class="btn btn-secondary">
                        Annuler
                    </button>
                </div>
            </form>

            <!-- Manage Extras Section -->
            <div id="extrasSection" style="display: none;">
                <div id="extrasContent"></div>
            </div>
        </div>
    </div>

    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #D9542A;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .slider.round {
            border-radius: 24px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-icon-warning {
            background-color: #F7B801;
        }

        .btn-icon-danger {
            background-color: #C62828;
        }

        .btn-icon:hover {
            transform: scale(1.1);
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        #sidebarPanel.open {
            transform: translateX(0) !important;
        }
    </style>

    <script>
        let currentPlat = null;
        let accompagnementCount = 0;
        let optionCount = 0;

        function openSidebar(mode, platId = null, platData = null) {
            const overlay = document.getElementById('sidebarOverlay');
            const sidebar = document.getElementById('sidebarPanel');
            const title = document.getElementById('sidebarTitle');
            const platForm = document.getElementById('platForm');
            const extrasSection = document.getElementById('extrasSection');

            // Reset visibility
            platForm.style.display = 'none';
            extrasSection.style.display = 'none';

            if (mode === 'create') {
                title.textContent = 'Ajouter un plat';
                platForm.action = '{{ route("admin.menu.plats.store") }}';
                document.getElementById('formMethod').value = 'POST';
                document.getElementById('submitBtnText').textContent = 'Créer le plat';
                platForm.reset();
                
                // Reset containers
                document.getElementById('accompagnementsContainer').innerHTML = '';
                document.getElementById('optionsContainer').innerHTML = '';
                accompagnementCount = 0;
                optionCount = 0;
                
                platForm.style.display = 'block';
            } else if (mode === 'edit') {
                title.textContent = 'Modifier le plat';
                platForm.action = `/admin/menu/plats/${platId}`;
                document.getElementById('formMethod').value = 'PUT';
                document.getElementById('submitBtnText').textContent = 'Enregistrer';
                
                // Fill form with data
                document.getElementById('platNom').value = platData.nom;
                document.getElementById('platCategorie').value = platData.categorie_id;
                document.getElementById('platPrix').value = platData.prix;
                document.getElementById('platDetail').value = platData.detail || '';
                document.getElementById('platDisponible').checked = platData.est_disponible;
                
                // Reset containers for edit mode (can add new ones)
                document.getElementById('accompagnementsContainer').innerHTML = '';
                document.getElementById('optionsContainer').innerHTML = '';
                accompagnementCount = 0;
                optionCount = 0;
                
                platForm.style.display = 'block';
            } else if (mode === 'extras') {
                title.textContent = `Accompagnements & Options - ${platData.nom}`;
                currentPlat = platData;
                loadExtrasContent(platId);
                extrasSection.style.display = 'block';
            }

            overlay.style.display = 'block';
            sidebar.style.display = 'block';
            setTimeout(() => {
                sidebar.classList.add('open');
            }, 10);
        }

        function closeSidebar() {
            const overlay = document.getElementById('sidebarOverlay');
            const sidebar = document.getElementById('sidebarPanel');
            
            sidebar.classList.remove('open');
            setTimeout(() => {
                overlay.style.display = 'none';
                sidebar.style.display = 'none';
            }, 300);
        }

        function addAccompagnementField() {
            const container = document.getElementById('accompagnementsContainer');
            const index = accompagnementCount++;
            
            const html = `
                <div id="accompagnement-${index}" style="background: #F0FDF4; padding: 1rem; border-radius: 8px; margin-bottom: 0.75rem; border: 2px solid #D9542A;">
                    <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 0.75rem;">
                        <h5 style="font-weight: 600; color: #3A3A3A; margin: 0; flex: 1;">Accompagnement #${index + 1}</h5>
                        <button type="button" onclick="removeAccompagnement(${index})" style="background: #C62828; color: white; border: none; border-radius: 4px; padding: 0.25rem 0.5rem; cursor: pointer; font-size: 0.875rem;">
                            × Retirer
                        </button>
                    </div>
                    <div style="display: grid; gap: 0.75rem;">
                        <input 
                            type="text" 
                            name="accompagnements[${index}][nom]" 
                            placeholder="Nom (ex: Alloco)" 
                            required
                            style="width: 100%; padding: 0.5rem; border: 1px solid #E5E5E5; border-radius: 6px; font-size: 0.875rem;"
                        >
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                            <input 
                                type="number" 
                                name="accompagnements[${index}][qte_gratuit]" 
                                placeholder="Qté gratuite" 
                                required
                                min="0"
                                value="1"
                                style="width: 100%; padding: 0.5rem; border: 1px solid #E5E5E5; border-radius: 6px; font-size: 0.875rem;"
                            >
                            <input 
                                type="number" 
                                name="accompagnements[${index}][prix_unitaire]" 
                                placeholder="Prix (FCFA)" 
                                required
                                min="0"
                                step="0.01"
                                value="0"
                                style="width: 100%; padding: 0.5rem; border: 1px solid #E5E5E5; border-radius: 6px; font-size: 0.875rem;"
                            >
                        </div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                            <input type="checkbox" name="accompagnements[${index}][disponible]" value="1" checked>
                            <span>Disponible</span>
                        </label>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', html);
        }

        function removeAccompagnement(index) {
            const element = document.getElementById(`accompagnement-${index}`);
            if (element) {
                element.remove();
            }
        }

        function addOptionField() {
            const container = document.getElementById('optionsContainer');
            const index = optionCount++;
            
            const html = `
                <div id="option-${index}" style="background: #FEF3C7; padding: 1rem; border-radius: 8px; margin-bottom: 0.75rem; border: 2px solid #F7B801;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <h5 style="font-weight: 600; color: #3A3A3A; margin: 0; flex: 1;">Option #${index + 1}</h5>
                        <button type="button" onclick="removeOption(${index})" style="background: #C62828; color: white; border: none; border-radius: 4px; padding: 0.25rem 0.5rem; cursor: pointer; font-size: 0.875rem;">
                            × Retirer
                        </button>
                    </div>
                    <div style="display: grid; gap: 0.75rem;">
                        <input 
                            type="text" 
                            name="options[${index}][nom]" 
                            placeholder="Nom (ex: Fromage)" 
                            required
                            style="width: 100%; padding: 0.5rem; border: 1px solid #E5E5E5; border-radius: 6px; font-size: 0.875rem;"
                        >
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                            <input 
                                type="number" 
                                name="options[${index}][qte_gratuit]" 
                                placeholder="Qté gratuite" 
                                required
                                min="0"
                                value="0"
                                style="width: 100%; padding: 0.5rem; border: 1px solid #E5E5E5; border-radius: 6px; font-size: 0.875rem;"
                            >
                            <input 
                                type="number" 
                                name="options[${index}][prix_unitaire]" 
                                placeholder="Prix (FCFA)" 
                                required
                                min="0"
                                step="0.01"
                                value="0"
                                style="width: 100%; padding: 0.5rem; border: 1px solid #E5E5E5; border-radius: 6px; font-size: 0.875rem;"
                            >
                        </div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                            <input type="checkbox" name="options[${index}][disponible]" value="1" checked>
                            <span>Disponible</span>
                        </label>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', html);
        }

        function removeOption(index) {
            const element = document.getElementById(`option-${index}`);
            if (element) {
                element.remove();
            }
        }

        function loadExtrasContent(platId) {
            const content = document.getElementById('extrasContent');
            const accompagnements = currentPlat.accompagnements || [];
            const options = currentPlat.options || [];

            content.innerHTML = `
                <!-- Accompagnements Section -->
                <div style="margin-bottom: 2rem;">
                    <h4 style="font-size: 1.125rem; font-weight: 700; color: #3A3A3A; margin-bottom: 1rem;">
                        Accompagnements (${accompagnements.length})
                    </h4>
                    
                    ${accompagnements.map(acc => `
                        <div style="background: #FDFBF8; padding: 1rem; border-radius: 8px; margin-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 600; color: #3A3A3A;">${acc.nom}</div>
                                <div style="font-size: 0.875rem; color: #666;">
                                    ${acc.qte_gratuit} gratuit(s) • ${acc.prix_unitaire} FCFA/unité
                                </div>
                            </div>
                            <form action="/admin/menu/accompagnements/${acc.id}" method="POST" onsubmit="return confirm('Supprimer cet accompagnement ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon btn-icon-danger" style="width: 32px; height: 32px;">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    `).join('')}

                    <button onclick="showAddAccompagnementForm(${platId})" class="btn btn-secondary" style="width: 100%; margin-top: 0.5rem;">
                        + Ajouter un accompagnement
                    </button>
                    <div id="addAccompagnementForm" style="display: none; margin-top: 1rem;"></div>
                </div>

                <!-- Options Section -->
                <div>
                    <h4 style="font-size: 1.125rem; font-weight: 700; color: #3A3A3A; margin-bottom: 1rem;">
                        Options (${options.length})
                    </h4>
                    
                    ${options.map(opt => `
                        <div style="background: #FEF3C7; padding: 1rem; border-radius: 8px; margin-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 600; color: #3A3A3A;">${opt.nom}</div>
                                <div style="font-size: 0.875rem; color: #666;">
                                    ${opt.qte_gratuit} gratuit(s) • ${opt.prix_unitaire} FCFA/unité
                                </div>
                            </div>
                            <form action="/admin/menu/options/${opt.id}" method="POST" onsubmit="return confirm('Supprimer cette option ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon btn-icon-danger" style="width: 32px; height: 32px;">
                                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    `).join('')}

                    <button onclick="showAddOptionForm(${platId})" class="btn btn-secondary" style="width: 100%; margin-top: 0.5rem;">
                        + Ajouter une option
                    </button>
                    <div id="addOptionForm" style="display: none; margin-top: 1rem;"></div>
                </div>
            `;
        }

        function showAddAccompagnementForm(platId) {
            const formContainer = document.getElementById('addAccompagnementForm');
            formContainer.style.display = 'block';
            formContainer.innerHTML = `
                <form action="/admin/menu/plats/${platId}/accompagnements" method="POST" enctype="multipart/form-data" style="background: white; border: 2px solid #D9542A; padding: 1.5rem; border-radius: 8px;">
                    @csrf
                    <div style="display: grid; gap: 1rem;">
                        <input type="text" name="nom" placeholder="Nom de l'accompagnement" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                        <input type="number" name="qte_gratuit" placeholder="Quantité gratuite" required min="0" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                        <input type="number" name="prix_unitaire" placeholder="Prix unitaire (FCFA)" required min="0" step="0.01" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="disponible" value="1" checked>
                            Disponible
                        </label>
                        <div style="display: flex; gap: 0.5rem;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Ajouter</button>
                            <button type="button" onclick="document.getElementById('addAccompagnementForm').style.display='none'" class="btn btn-secondary">Annuler</button>
                        </div>
                    </div>
                </form>
            `;
        }

        function showAddOptionForm(platId) {
            const formContainer = document.getElementById('addOptionForm');
            formContainer.style.display = 'block';
            formContainer.innerHTML = `
                <form action="/admin/menu/plats/${platId}/options" method="POST" enctype="multipart/form-data" style="background: white; border: 2px solid #F7B801; padding: 1.5rem; border-radius: 8px;">
                    @csrf
                    <div style="display: grid; gap: 1rem;">
                        <input type="text" name="nom" placeholder="Nom de l'option" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                        <input type="number" name="qte_gratuit" placeholder="Quantité gratuite" required min="0" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                        <input type="number" name="prix_unitaire" placeholder="Prix unitaire (FCFA)" required min="0" step="0.01" style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px;">
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="disponible" value="1" checked>
                            Disponible
                        </label>
                        <div style="display: flex; gap: 0.5rem;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">Ajouter</button>
                            <button type="button" onclick="document.getElementById('addOptionForm').style.display='none'" class="btn btn-secondary">Annuler</button>
                        </div>
                    </div>
                </form>
            `;
        }

        function validateFileSize(input, maxSizeMB) {
            if (input.files && input.files[0]) {
                const fileSize = input.files[0].size / 1024 / 1024;
                if (fileSize > maxSizeMB) {
                    alert(`La taille du fichier ne peut pas dépasser ${maxSizeMB} Mo.\n\nTaille actuelle: ${fileSize.toFixed(2)} Mo`);
                    input.value = '';
                    return false;
                }
            }
            return true;
        }

        // Close sidebar on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });
    </script>
@endsection
