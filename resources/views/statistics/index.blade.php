@extends('layout')

@section('content')
    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h2>Statisctics</h2>
        </div>

        @if(count($data))
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th colspan="2">Region</th>
                            <th colspan="2">Academy</th>
                            <th colspan="4">Projects</th>
                        </tr>
                        <tr>
                            <th rowspan="2" class="align-top">Name</th>
                            <th rowspan="2" class="align-top">Teachers</th>
                            <th rowspan="2" class="align-top">Name</th>
                            <th rowspan="2" class="align-top">Teachers</th>
                            <th rowspan="2" class="align-top">Draft</th>
                            <th colspan="3">Finalized</th>
                        </tr>
                        <tr>
                            <th>Première</th>
                            <th>Terminale</th>
                            <th>All</th>
                        </tr>
                    </thead>
                    @foreach($data as $region)
                        @foreach($region['academies'] as $academy)
                            <tr {!! $academy['accent_row'] ? 'class="accent"' : '' !!}>
                                @if($loop->first)
                                    <td rowspan="{{ count($region['academies']) }}">{{ $region['name'] }}</td>
                                    <td rowspan="{{ count($region['academies']) }}">{{ $region['teachers'] }}</td>
                                @endif
                                <td>{{ $academy['name'] }}</td>
                                <td>{{ $academy['teachers'] }}</td>
                                <td>{{ $academy['projects_draft'] }}</td>
                                <td>{{ $academy['projects_finalized_premier'] }}</td>
                                <td>{{ $academy['projects_finalized_terminal'] }}</td>
                                <td>{{ $academy['projects_finalized'] }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>
            </div>
        @else
            @include('common.empty_list')
        @endif
    </div>
@endsection