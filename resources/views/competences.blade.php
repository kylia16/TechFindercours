@extends('template')

@section('main')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Compétences</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAjout">
            <i class="fa fa-plus me-1"></i>Ajouter
        </button>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Code</th>
                <th>Nom</th>
                <th>Description</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($competences_list as $competence)
            <tr>
                <td>{{ $competence->code_comp }}</td>
                <td>{{ $competence->label_comp }}</td>
                <td>{{ $competence->description_comp }}</td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-2">

                        <button class="btn btn-sm btn-warning"
                            data-bs-toggle="modal"
                            data-bs-target="#modalModifier"
                            data-id="{{ $competence->code_comp }}"
                            data-label="{{ $competence->label_comp }}"
                            data-desc="{{ $competence->description_comp }}">
                            <i class="fa fa-edit"></i> Modifier
                        </button>

                        <form action="/web/competences/{{ $competence->code_comp }}/delete" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Supprimer cette compétence ?')">
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
        {{ $competences_list->links() }}
    </div>

</div>

{{-- MODAL AJOUTER --}}
<div class="modal fade" id="modalAjout" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fa fa-plus me-2"></i>Ajouter une compétence
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/web/competences/store" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="label_comp" class="form-control"
                               placeholder="Ex: Réparation PC" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description_comp" class="form-control"
                                  rows="3" placeholder="Description..."></textarea>
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
                    <i class="fa fa-edit me-2"></i>Modifier la compétence
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formModifier" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="label_comp" id="modifierLabel" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description_comp" id="modifierDesc" class="form-control" rows="3"></textarea>
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
        const btn   = event.relatedTarget;
        const id    = btn.getAttribute('data-id');
        const label = btn.getAttribute('data-label');
        const desc  = btn.getAttribute('data-desc');
        document.getElementById('modifierLabel').value = label;
        document.getElementById('modifierDesc').value  = desc ?? '';
        document.getElementById('formModifier').action = '/web/competences/' + id + '/update';
    });
</script>

@endsection
