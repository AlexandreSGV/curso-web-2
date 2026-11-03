<div class="grid gap-1">
    @if (isset($category))
        <p>Categoria: <strong>{{ $category->name }}</strong></p>
    @else
        <label for="category_id" class="font-medium">Categoria</label>
        <select id="category_id" name="category_id" required class="w-full rounded border border-slate-300 p-2">
            <option value="">Selecione uma categoria ainda não associada</option>
            @foreach ($categories as $option)
                <option value="{{ $option->id }}" @selected(old('category_id') == $option->id)>{{ $option->name }}</option>
            @endforeach
        </select>
        @if ($categories->isEmpty())
            <p>Cadastre primeiro uma <a href="{{ route('categories.create') }}" class="text-blue-700 underline">categoria</a>.</p>
        @endif
    @endif
</div>

<div>
    {{-- Checkbox desmarcado não é enviado; o campo oculto fornece o valor 0. --}}
    <input type="hidden" name="featured" value="0">
    <label for="featured" class="flex items-center gap-2">
        <input id="featured" type="checkbox" name="featured" value="1"
            @checked(old('featured', $category->pivot->featured ?? 0))>
        Categoria em destaque neste livro
    </label>
</div>

<div class="grid gap-1">
    <label for="position" class="font-medium">Posição (opcional)</label>
    <input id="position" name="position" type="number" min="1" max="65535"
        value="{{ old('position', $category->pivot->position ?? '') }}"
        class="w-full rounded border border-slate-300 p-2">
</div>
