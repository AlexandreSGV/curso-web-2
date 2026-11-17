@if ($errors->any())
    <div role="alert" class="rounded border border-red-300 bg-red-50 p-3 text-red-900">
        <p class="font-semibold">Confira os campos informados:</p>
        <ul class="ml-5 list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
