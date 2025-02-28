@extends('layout')

@section('content')
    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h2>Gestion du jury</h2>
        </div>

        @foreach($data as $target)
            <h3>
                @if($target['type'] == 'prize')
                    Prix
                @else
                    Région
                @endif
                {{ $target['name'] }}
            </h3>
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
