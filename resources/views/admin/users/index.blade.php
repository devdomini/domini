@extends('admin.layout')

@section('title', 'Utilisateurs')
@section('page-title', 'Gestion des Utilisateurs')

@section('content')
    <!-- Actions Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000;">Tous les utilisateurs</h2>
            <p style="color: #666; margin-top: 0.25rem;">Gérez les comptes utilisateurs de la plateforme</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            + Ajouter un utilisateur
        </a>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('admin.users.index') }}" style="padding: 1rem; display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
            <select name="role" style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                <option value="">Tous les rôles</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="entreprise" {{ request('role') === 'entreprise' ? 'selected' : '' }}>Entreprise</option>
                <option value="livreur" {{ request('role') === 'livreur' ? 'selected' : '' }}>Livreur</option>
                <option value="employe" {{ request('role') === 'employe' ? 'selected' : '' }}>Employé</option>
                <option value="commercial" {{ request('role') === 'commercial' ? 'selected' : '' }}>Commercial</option>
            </select>
            
            <select name="is_active" style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                <option value="">Tous les statuts</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Actif</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Désactivé</option>
            </select>
            
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Rechercher par nom, email, téléphone..."
                style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; flex: 1; min-width: 220px;"
            >

            <button type="submit" class="btn btn-primary" style="padding: 0.55rem 1rem;">
                Filtrer
            </button>

            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary" style="padding: 0.55rem 1rem;">
                Réinitialiser
            </a>
        </form>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Rôle</th>
                            <th>Entreprise</th>
                            <th>Statut</th>
                            <th>Date création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users ?? [] as $user)
                        <tr>
                            <td>#{{ $user->id }}</td>
                            <td>
                                <div style="font-weight: 600;">{{ $user->name }}</div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->telephone ?? '-' }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge" style="background-color: #000000; color: white;">Admin</span>
                                @elseif($user->role === 'entreprise')
                                    <span class="badge" style="background-color: #FFF3E0; color: #E65100;">Entreprise</span>
                                @elseif($user->role === 'livreur')
                                    <span class="badge" style="background-color: #E3F2FD; color: #1976D2;">Livreur</span>
                                @elseif($user->role === 'commercial')
                                    <span class="badge" style="background-color: #EDE9FE; color: #7C3AED;">Commercial</span>
                                @else
                                    <span class="badge badge-info">Employé</span>
                                @endif
                            </td>
                            <td>{{ $user->id_entreprise ? 'Entreprise #' . $user->id_entreprise : '-' }}</td>
                            <td>
                                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    @if($user->is_active)
                                        <button type="submit" class="badge badge-success" style="border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            @include('admin.partials.icon', ['name' => 'check', 'size' => 12]) Actif
                                        </button>
                                    @else
                                        <button type="submit" class="badge badge-danger" style="border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            @include('admin.partials.icon', ['name' => 'x', 'size' => 12]) Désactivé
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" style="padding: 0.25rem 0.75rem; background-color: #CC0000; color: white; border-radius: 4px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">
                                        Modifier
                                    </a>
                                    <a href="{{ route('admin.users.change-password', $user->id) }}" style="padding: 0.25rem 0.75rem; background-color: #000000; color: white; border-radius: 4px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">
                                        MDP
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 3rem; color: #666;">
                                Aucun utilisateur trouvé
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    {{ $users->links('vendor.pagination.domini') }}
@endsection

@section('scripts')
<script>
    // Confirmation avant de changer le statut
    document.querySelectorAll('form[action*="toggle-status"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir changer le statut de cet utilisateur ?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
