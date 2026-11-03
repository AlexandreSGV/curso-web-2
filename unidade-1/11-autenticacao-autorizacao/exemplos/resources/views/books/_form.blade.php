<div class="grid gap-1">
    <label for="title" class="font-medium">Título</label>
    <input id="title" name="title" value="{{ old('title', $book->title ?? '') }}" maxlength="255" required
        class="w-full rounded border border-slate-300 p-2">
    @error('title')
        <p class="text-sm text-red-700">{{ $message }}</p>
    @enderror
</div>

<div class="grid gap-1">
    <label for="isbn" class="font-medium">ISBN (13 caracteres, sem espaços ou hífens)</label>
    <input id="isbn" name="isbn" value="{{ old('isbn', $book->isbn ?? '') }}" minlength="13" maxlength="13" required
        class="w-full rounded border border-slate-300 p-2">
</div>

<div class="grid gap-1">
    <label for="published_year" class="font-medium">Ano de publicação (opcional)</label>
    <input id="published_year" name="published_year" type="number" min="1000" max="2100"
        value="{{ old('published_year', $book->published_year ?? '') }}"
        class="w-full rounded border border-slate-300 p-2">
</div>

<div class="grid gap-1">
    <label for="author_id" class="font-medium">Autor</label>
    <select id="author_id" name="author_id" required class="w-full rounded border border-slate-300 p-2">
        <option value="">Selecione</option>
        @foreach ($authors as $author)
            <option value="{{ $author->id }}"
                @selected((string) old('author_id', $book->author_id ?? '') === (string) $author->id)>
                {{ $author->name }}
            </option>
        @endforeach
    </select>
    @if ($authors->isEmpty())
        <p>Cadastre primeiro um <a href="{{ route('authors.create') }}" class="text-blue-700 underline">autor</a>.</p>
    @endif
</div>
