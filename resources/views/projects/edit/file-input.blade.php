<div class="{{ $class }}">
    @if($title)
        <span class="file-box-title mb-2">{{ $title }}</span>
    @endif
    @if($description)
        <small>{!! $description !!}</small>
    @endif
    @if($file)
        <div class="custom-file-controls">
            <a href="{{ Storage::disk('uploads')->url($file) }}" target="_blank">Télécharger</a> ou
            <a href="#" class="link-delete-file" data-file="{{ $key }}">Supprimer</a>
            <input style="display: none;" name="{{ $key }}" type="file"
                accept="{{ $extensions }}"
                class="custom-file-input {{ $errors->get($key) ? 'is-invalid' : '' }}">
            @if($key === 'image_file')
                <div class="mt-2">
                    <img src="{{ Storage::disk('uploads')->url($file) }}"
                        alt="Image du projet"
                        class="img-fluid"
                        style="max-width: 200px;">
                </div>
            @endif
        </div>
    @else
        <div class="custom-file">
            <span class="custom-file-clear" title="Supprimer">&times;</span>
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
    @endif
</div>