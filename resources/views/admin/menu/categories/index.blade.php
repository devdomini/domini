@extends('admin.layout')

@section('title', 'Catégories')
@section('page-title', 'Gestion des Catégories')

@section('content')
    <!-- Messages de succès/erreur -->
    @if(session('success'))
        <div style="background-color: #10B981; color: white; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span style="font-weight: 600;">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="background-color: #EF4444; color: white; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span style="font-weight: 600;">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div style="background-color: #FEE2E2; color: #991B1B; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #EF4444;">
            <div style="font-weight: 700; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                Erreurs de validation
            </div>
            <ul style="list-style: disc; margin-left: 1.5rem; margin-top: 0.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <a href="{{ route('admin.menu.index') }}" style="color: #D9542A; text-decoration: none; font-weight: 600; display: inline-block; margin-bottom: 0.5rem;">
                ← Retour au menu
            </a>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #3A3A3A;">Catégories de plats</h2>
            <p style="color: #666; margin-top: 0.25rem;">{{ $categories->count() }} catégorie(s) au total</p>
        </div>
    </div>

    <!-- Categories Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
        
        <!-- Add Category Card (Modal Trigger) -->
        <div onclick="openAddModal()" style="background: linear-gradient(135deg, #D9542A, #F7B801); border-radius: 16px; padding: 2rem; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 300px; cursor: pointer; transition: all 0.3s; border: 3px dashed rgba(255, 255, 255, 0.5);">
            <svg style="width: 64px; height: 64px; color: white; margin-bottom: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: white; text-align: center;">
                Ajouter une catégorie
            </h3>
            <p style="color: rgba(255, 255, 255, 0.9); font-size: 0.875rem; text-align: center; margin-top: 0.5rem;">
                Cliquez pour créer
            </p>
        </div>

        <!-- Existing Categories -->
        @foreach($categories as $categorie)
        <div class="card" style="overflow: hidden; position: relative;">
            <!-- Image/Logo -->
            <div style="height: 180px; background: linear-gradient(135deg, #FDFBF8, #F5F5F5); display: flex; align-items: center; justify-content: center; position: relative;">
                @if($categorie->logo)
                    <img src="{{ asset('storage/' . $categorie->logo) }}" alt="{{ $categorie->nom }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <div style="font-size: 4rem; font-weight: 900; color: #D9542A;">
                        {{ strtoupper(substr($categorie->nom, 0, 1)) }}
                    </div>
                @endif
                
                <!-- Status Badge -->
                <div style="position: absolute; top: 0.5rem; right: 0.5rem;">
                    @if($categorie->est_disponible)
                        <span class="badge badge-success">Disponible</span>
                    @else
                        <span class="badge badge-danger">Indisponible</span>
                    @endif
                </div>
            </div>

            <!-- Content -->
            <div style="padding: 1.5rem;">
                <!-- Nom -->
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #3A3A3A; margin-bottom: 1.5rem; text-align: center;">
                    {{ $categorie->nom }}
                </h3>

                <!-- Actions -->
                <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 0.5rem; align-items: center;">
                    <!-- Switch -->
                    <form action="{{ route('admin.menu.categories.toggle', $categorie->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <label class="switch">
                            <input type="checkbox" {{ $categorie->est_disponible ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="slider round"></span>
                        </label>
                    </form>

                    <!-- Edit Button -->
                    <button onclick="openEditModal({{ $categorie->id }}, '{{ $categorie->nom }}', {{ $categorie->est_disponible ? 'true' : 'false' }})" style="width: 40px; height: 40px; border-radius: 8px; border: none; background-color: #F7B801; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>

                    <!-- Delete Button -->
                    <form action="{{ route('admin.menu.categories.destroy', $categorie->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="width: 40px; height: 40px; border-radius: 8px; border: none; background-color: #C62828; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Add Modal -->
    <div id="addModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 16px; padding: 2rem; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.5rem; font-weight: 700; color: #3A3A3A;">Ajouter une catégorie</h3>
                <button onclick="closeAddModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #666;">×</button>
            </div>

            <form action="{{ route('admin.menu.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="display: grid; gap: 1.5rem;">
                    <!-- Nom -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                            Nom de la catégorie <span style="color: #D9542A;">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nom" 
                            required
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            placeholder="Ex: Plats principaux"
                        >
                    </div>

                    <!-- Logo -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                            Logo/Image
                        </label>
                        <input 
                            type="file" 
                            name="logo" 
                            accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            onchange="validateFileSize(this, 5)"
                        >
                        <small style="color: #666; font-size: 0.75rem;">Format: JPG, PNG, GIF, WebP (Max 5 Mo)</small>
                    </div>

                    <!-- Disponible -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="est_disponible" value="1" checked style="width: 18px; height: 18px;">
                            <span style="font-weight: 600; color: #3A3A3A;">Catégorie disponible</span>
                        </label>
                    </div>
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #E5E5E5;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        Créer la catégorie
                    </button>
                    <button type="button" onclick="closeAddModal()" class="btn btn-secondary">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 16px; padding: 2rem; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.5rem; font-weight: 700; color: #3A3A3A;">Modifier la catégorie</h3>
                <button onclick="closeEditModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #666;">×</button>
            </div>

            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div style="display: grid; gap: 1.5rem;">
                    <!-- Nom -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                            Nom de la catégorie <span style="color: #D9542A;">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nom" 
                            id="editNom"
                            required
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                        >
                    </div>

                    <!-- Logo -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                            Nouveau logo/image (optionnel)
                        </label>
                        <input 
                            type="file" 
                            name="logo" 
                            accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            onchange="validateFileSize(this, 5)"
                        >
                        <small style="color: #666; font-size: 0.75rem;">Format: JPG, PNG, GIF, WebP (Max 5 Mo) - Laisser vide pour conserver l'image actuelle</small>
                    </div>

                    <!-- Disponible -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="est_disponible" value="1" id="editDisponible" style="width: 18px; height: 18px;">
                            <span style="font-weight: 600; color: #3A3A3A;">Catégorie disponible</span>
                        </label>
                    </div>
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #E5E5E5;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        Enregistrer
                    </button>
                    <button type="button" onclick="closeEditModal()" class="btn btn-secondary">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Toggle Switch */
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

        /* Hover effects */
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        button:hover {
            transform: scale(1.05);
        }
    </style>

    <script>
        function openAddModal() {
            document.getElementById('addModal').style.display = 'flex';
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function openEditModal(id, nom, disponible) {
            document.getElementById('editForm').action = `/admin/menu/categories/${id}`;
            document.getElementById('editNom').value = nom;
            document.getElementById('editDisponible').checked = disponible;
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Validate file size before upload
        function validateFileSize(input, maxSizeMB) {
            if (input.files && input.files[0]) {
                const fileSize = input.files[0].size / 1024 / 1024; // Convert to MB
                
                if (fileSize > maxSizeMB) {
                    alert(`La taille du fichier ne peut pas dépasser ${maxSizeMB} Mo.\n\nTaille actuelle: ${fileSize.toFixed(2)} Mo\n\nVeuillez choisir une image plus petite.`);
                    input.value = ''; // Clear the input
                    return false;
                }
                
                // Show preview and file info
                const fileName = input.files[0].name;
                const fileInfo = document.createElement('div');
                fileInfo.style.cssText = 'margin-top: 0.5rem; padding: 0.5rem; background: #F0FDF4; border: 1px solid #86EFAC; border-radius: 6px; font-size: 0.875rem; color: #166534;';
                fileInfo.innerHTML = `✓ ${fileName} (${fileSize.toFixed(2)} Mo)`;
                
                // Remove previous file info if exists
                const prevInfo = input.parentElement.querySelector('.file-info');
                if (prevInfo) prevInfo.remove();
                
                fileInfo.className = 'file-info';
                input.parentElement.appendChild(fileInfo);
            }
            return true;
        }

        // Close modals on outside click
        document.getElementById('addModal').addEventListener('click', function(e) {
            if (e.target === this) closeAddModal();
        });

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });

        // Close modals on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
            }
        });
    </script>
@endsection
