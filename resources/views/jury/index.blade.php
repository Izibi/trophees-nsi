@extends('layout')

@section('content')
    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h2>Gestion du jury et coordination</h2>
        </div>

        @if(Auth::user()->role == 'admin')
            <div class="mb-2 mt-2 ml-3">
                <a href="{{ route('jury.exportAll') }}" class="btn btn-primary">
                    Exporter tous les enseignants
                </a>
            </div>
        @endif

        @foreach($data as $target)
            <h3>
                @if($target['type'] == 'prize')
                    Prix
                @else
                    Région
                @endif
                {{ $target['name'] }}
            </h3>
            <div class="mb-2">
                <a href="{{ route('jury.export', ['target' => $target['id'], 'type' => $target['type']]) }}" class="btn btn-sm btn-primary">
                    Exporter les enseignants
                </a>
            </div>
            @if($target['president'])
                <div>Président du jury : {{ $target['president']->name }}</div>
            @else
                <div style="color: red; font-weight: bold;">Il n'y a pas de président du jury.</div>
            @endif
            <div>
                Membres du jury :
                <ul>
                    @foreach($target['members'] as $member)
                        <li>
                            @if($member == $target['president'])
                                <b>{{ $member->name }}
                                (Président)</b>
                            @else
                                {{ $member->name }}
                                <a href="{{ route('jury.nominate', ['target' => $target['id'], 'type' => $target['type'], 'user' => $member->id]) }}">Nommer président(e) du jury</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="mb-2"></div>
        @endforeach
    </div>
@endsection
