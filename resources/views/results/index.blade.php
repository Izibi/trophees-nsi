@extends('layout')

@section('content')
    @if(count($rows))
        <div class="card mt-3 mb-3">
            <div class="card-header">
                <h2>Notes des projets</h2>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-borderless active-table">
                    <thead>
                        <tr>
                            <th>Nom du projet</th>
                            <th>Note moyenne du projet</th>
                        </tr>
                    </thead>
                    @foreach ($rows as $project)
                        <tr>
                            <td>{{ $project->name }}</td>
                            <td>{{ $project->score_total }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        @include('common.paginator')
    @else
        @include('common.empty_list')
    @endif
@endsection