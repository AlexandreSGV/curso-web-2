@if ($errors->any())
    <p>Confira os campos informados:</p>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<p>
    <label for="title">Título</label>
    <input id="title" name="title" value="{{ old('title', $book->title ?? '') }}"
        maxlength="255" required>
</p>
<p>
    <label for="isbn">ISBN (13 caracteres, sem espaços ou hífens)</label>
    <input id="isbn" name="isbn" value="{{ old('isbn', $book->isbn ?? '') }}"
        minlength="13" maxlength="13" required>
</p>
<p>
    <label for="published_year">Ano de publicação</label>
    <input id="published_year" name="published_year" type="number"
        value="{{ old('published_year', $book->published_year ?? '') }}"
        min="1000" max="2100">
</p>
<p>
    <label for="author_id">Autor</label>
    <select id="author_id" name="author_id" required>
        <option value="">Selecione</option>
        @foreach ($authors as $author)
            <option value="{{ $author->id }}"
                @selected((string) old('author_id', $book->author_id ?? '') === (string) $author->id)>
                {{ $author->name }}
            </option>
        @endforeach
    </select>
</p>
