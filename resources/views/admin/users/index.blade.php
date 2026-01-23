@extends('admin.layout')

@section('title', 'Utilisateurs')
@section('page-title', 'Gestion des Utilisateurs')

@section('content')
    <!-- Actions Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #3A3A3A;">Tous les utilisateurs</h2>
            <p style="color: #666; margin-top: 0.25rem;">Gérez les comptes utilisateurs de la plateforme</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            + Ajouter un utilisateur
        </a>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="padding: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">
            <select style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                <option value="">Tous les rôles</option>
                <option value="admin">Admin</option>
                <option value="entreprise">Entreprise</option>
                <option value="livreur">Livreur</option>
                <option value="employe">Employé</option>
            </select>
            
            <select style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; background: white;">
                <option value="">Tous les statuts</option>
                <option value="1">Actif</option>
                <option value="0">Désactivé</option>
            </select>
            
            <input type="text" placeholder="Rechercher par nom, email..." style="padding: 0.5rem; border: 2px solid #E5E5E5; border-radius: 6px; flex: 1; min-width: 200px;">
        </div>
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
                                    <span class="badge" style="background-color: #3A3A3A; color: white;">Admin</span>
                                @elseif($user->role === 'entreprise')
                                    <span class="badge" style="background-color: #FFF3E0; color: #E65100;">Entreprise</span>
                                @elseif($user->role === 'livreur')
                                    <span class="badge" style="background-color: #E3F2FD; color: #1976D2;">Livreur</span>
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
                                        <button type="submit" class="badge badge-success" style="border: none; cursor: pointer;">
                                            ✓ Actif
                                        </button>
                                    @else
                                        <button type="submit" class="badge badge-danger" style="border: none; cursor: pointer;">
                                            ✗ Désactivé
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" style="padding: 0.25rem 0.75rem; background-color: #F7B801; color: white; border-radius: 4px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">
                                        Modifier
                                    </a>
                                    <a href="{{ route('admin.users.change-password', $user->id) }}" style="padding: 0.25rem 0.75rem; background-color: #3A3A3A; color: white; border-radius: 4px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">
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
