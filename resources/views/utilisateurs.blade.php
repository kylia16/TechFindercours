@extends('template')

@section('main')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Utilisateurs</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAjout">
            <i class="fa fa-plus me-1"></i>Ajouter
        </button>
    </div>
   @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    {{-- BARRE DE RECHERCHE --}}
    <div class="mb-4">
        <div class="input-group">
            <input type="text" id="searchInput" class="form-control"
                   placeholder="Rechercher par nom, prénom, login, rôle...">
            <span class="input-group-text bg-primary text-white">
                <i class="fa fa-search"></i>
            </span>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Code</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Login</th>
                <th>Rôle</th>
                <th>État</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($utilisateurs_list as $utilisateur)
            <tr>
                <td>{{ $utilisateur->code_user }}</td>
                <td>{{ $utilisateur->nom_user }}</td>
                <td>{{ $utilisateur->prenom_user }}</td>
                <td>{{ $utilisateur->login_user }}</td>
                <td>{{ $utilisateur->role_user }}</td>
                <td>
                    @if($utilisateur->etat_user === 'actif')
                        <span class="badge bg-success">actif</span>
                    @elseif($utilisateur->etat_user === 'bloque')
                        <span class="badge bg-danger">bloqué</span>
                    @else
                        <span class="badge bg-secondary">inactif</span>
                    @endif
                </td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-2">

                        <button class="btn btn-sm btn-warning"
                            data-bs-toggle="modal"
                            data-bs-target="#modalModifier"
                            data-id="{{ $utilisateur->code_user }}"
                            data-nom="{{ $utilisateur->nom_user }}"
                            data-prenom="{{ $utilisateur->prenom_user }}"
                            data-tel="{{ $utilisateur->tel_user }}"
                            data-sexe="{{ $utilisateur->sexe_user }}"
                            data-role="{{ $utilisateur->role_user }}"
                            data-etat="{{ $utilisateur->etat_user }}">
                            <i class="fa fa-edit"></i> Modifier
                        </button>

                        <form action="/web/users/{{ $utilisateur->code_user }}/delete" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Supprimer cet utilisateur ?')">
                                <i class="fa fa-trash"></i> Supprimer
                            </button>
                        </form>

                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
        {{ $utilisateurs_list->links() }}
    </div>

</div>

{{-- MODAL AJOUTER --}}
<div class="modal fade" id="modalAjout" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fa fa-plus me-2"></i>Ajouter un utilisateur
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/web/users/store" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom_user" class="form-control" required>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="prenom_user" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Login <span class="text-danger">*</span></label>
                        <input type="text" name="login_user" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" name="password_user" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Téléphone</label>
                        <input type="text" name="tel_user" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Sexe</label>
                            <select name="sexe_user" class="form-select">
                                <option value="">--</option>
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Rôle <span class="text-danger">*</span></label>
                            <select name="role_user" class="form-select" required>
                                <option value="client">Client</option>
                                <option value="technicien">Technicien</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label fw-bold">État <span class="text-danger">*</span></label>
                            <select name="etat_user" class="form-select" required>
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                                <option value="bloque">Bloqué</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL MODIFIER --}}
<div class="modal fade" id="modalModifier" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fa fa-edit me-2"></i>Modifier l'utilisateur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formModifier" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom_user" id="modifierNom" class="form-control" required>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="prenom_user" id="modifierPrenom" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Téléphone</label>
                        <input type="text" name="tel_user" id="modifierTel" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Sexe</label>
                            <select name="sexe_user" id="modifierSexe" class="form-select">
                                <option value="">--</option>
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Rôle <span class="text-danger">*</span></label>
                            <select name="role_user" id="modifierRole" class="form-select" required>
                                <option value="client">Client</option>
                                <option value="technicien">Technicien</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label fw-bold">État <span class="text-danger">*</span></label>
                            <select name="etat_user" id="modifierEtat" class="form-select" required>
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                                <option value="bloque">Bloqué</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-save me-1"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('modalModifier').addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;
        document.getElementById('modifierNom').value    = btn.getAttribute('data-nom');
        document.getElementById('modifierPrenom').value = btn.getAttribute('data-prenom');
        document.getElementById('modifierTel').value    = btn.getAttribute('data-tel') ?? '';
        document.getElementById('modifierSexe').value   = btn.getAttribute('data-sexe') ?? '';
        document.getElementById('modifierRole').value   = btn.getAttribute('data-role');
        document.getElementById('modifierEtat').value   = btn.getAttribute('data-etat');
        document.getElementById('formModifier').action  = '/web/users/' + btn.getAttribute('data-id') + '/update';
    });

    document.getElementById('searchInput').addEventListener('keyup', function () {
        const search = this.value.toLowerCase();
        const rows   = document.querySelectorAll('tbody tr');
        rows.forEach(function (row) {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(search) ? '' : 'none';
        });
    });
</script>

@endsection
