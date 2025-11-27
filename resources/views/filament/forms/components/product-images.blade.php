@if($images->count() > 0)
    <div class="fi-fo-field-wrp-label">
        <label class="fi-fo-field-wrp-label-text text-sm font-medium leading-6 text-gray-950 dark:text-white">
            <b>{{ $getLabel() }}</b>
        </label>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-top: 0.5rem;">
        @foreach($images as $image)
            <div style="position: relative;">
                <a href="{{ $image->getFullUrl() }}" target="_blank" style="display: block;">
                    <img 
                        src="{{ $image->getFullUrl() }}" 
                        alt="{{ $image->name }}"
                        width="300"
                        height="200"
                        style="width: 100%; height: 200px; object-fit: cover; border-radius: 0.5rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: opacity 0.2s, box-shadow 0.2s;"
                        onmouseover="this.style.opacity='0.8'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)';"
                        onmouseout="this.style.opacity='1'; this.style.boxShadow='0 1px 2px 0 rgba(0, 0, 0, 0.05)';"
                    />
                </a>
            </div>
        @endforeach
    </div>
@endif

