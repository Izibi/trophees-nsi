@extends('layout', ['containerstyle' => 'max-width: 760px;'])

@section('head')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.getElementById('export-form');
            if (!form) {
                return;
            }
            form.addEventListener('submit', function (event) {
                // Build a short URL: "all=1" when every field is selected,
                // otherwise "fields=..." where a fully checked group is
                // collapsed to its group name (e.g. "team"), plus the
                // laureate filter.
                event.preventDefault();
                var boxes = form.querySelectorAll('input[name="fields[]"]');
                var checked = [];
                for (var i = 0; i < boxes.length; i++) {
                    if (boxes[i].checked) {
                        checked.push(boxes[i].value);
                    }
                }
                var query = [];
                if (boxes.length && checked.length === boxes.length) {
                    query.push('all=1');
                } else if (checked.length) {
                    var totals = {};
                    var checkedByGroup = {};
                    for (var j = 0; j < boxes.length; j++) {
                        var group = boxes[j].getAttribute('data-group');
                        totals[group] = (totals[group] || 0) + 1;
                        if (boxes[j].checked) {
                            checkedByGroup[group] = (checkedByGroup[group] || 0) + 1;
                        }
                    }
                    var parts = [];
                    Object.keys(totals).forEach(function (group) {
                        if (checkedByGroup[group] === totals[group]) {
                            parts.push(group);
                        } else {
                            for (var k = 0; k < boxes.length; k++) {
                                if (boxes[k].getAttribute('data-group') === group && boxes[k].checked) {
                                    parts.push(boxes[k].value);
                                }
                            }
                        }
                    });
                    query.push('fields=' + parts.join(','));
                }
                var laureate = form.querySelector('select[name="laureate"]');
                if (laureate && laureate.value) {
                    query.push('laureate=' + laureate.value);
                }
                window.location = form.getAttribute('action') + (query.length ? '?' + query.join('&') : '');
            });
        });
    </script>
@endsection

@section('content')
    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h2>{{ $title }}</h2>
        </div>
        <div class="card-body">
            <form action="{{ $downloadUrl }}" method="get" id="export-form">
                @if($withLaureateFilter)
                    <div class="form-group">
                        <label for="laureate"><strong>Filtrer les projets</strong></label>
                        <select class="form-control" name="laureate" id="laureate">
                            <option value="">Tous les projets</option>
                            <option value="territorial">Lauréats régionaux uniquement</option>
                            <option value="national">Lauréats nationaux uniquement</option>
                            <option value="none">Projets non lauréats uniquement</option>
                        </select>
                    </div>
                @endif

                <div class="mb-4">
                    <button type="submit" class="btn btn-primary">Télécharger le CSV</button>
                    <a href="/" class="btn btn-secondary">Retour</a>
                </div>

                @foreach($groups as $groupKey => $columns)
                    @if($groupKey === 'team')
                        {{-- All or nothing: one checkbox stands for every member field. --}}
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fields[]"
                                   value="team" id="field_team" data-group="team" checked>
                            <label class="form-check-label" for="field_team">Membres de l'équipe</label>
                        </div>
                    @else
                        @foreach($columns as $column)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="fields[]"
                                       value="{{ $column->key }}" id="field_{{ $column->key }}"
                                       data-group="{{ $column->group }}" checked>
                                <label class="form-check-label" for="field_{{ $column->key }}">
                                    {{ $column->label }}
                                </label>
                            </div>
                        @endforeach
                    @endif
                @endforeach
            </form>
        </div>
    </div>
@endsection
