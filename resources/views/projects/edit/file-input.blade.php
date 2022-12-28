<div class="{{ $class }}">
    <span class="file-box-title">{{ $title }}</span>
    @if($file)
        - <a href="{{ Storage::disk('uploads')->url($file) }}" target="_blank">télécharger</a> ou
        <a href="#" class="link-delete-file" data-file="{{ $key }}">supprimer</a>
    @else
        <div class="custom-file mt-2">
            <span class="custom-file-clear" title="Clear">&times;</span>
            <input name="{{ $key }}" type="file"
                accept="{{ $extensions }}"
                class="custom-file-input {{ $errors->get($key) ? 'is-invalid' : '' }}">
            <label class="custom-file-label text-truncate">Choisir un fichier...</label>
        </div>
        @error($key)
            <div class="text-danger">
                <small>{{ $message }}</small>
            </div>
        @enderror
        <small>{!! $description !!}</small>
    @endif
</div>