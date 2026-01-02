@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4">Tambah Barang</h1>

    <form method="POST" action="{{ route('admin.items.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block mb-1">Nama</label>
            <input name="name" class="w-full border rounded p-2" required>
        </div>

        <div class="mb-4">
            <label class="block mb-1">Kategori</label>
            <select name="category_id" class="w-full border rounded p-2" required>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block mb-1">Stok</label>
            <input type="number" name="stock" class="w-full border rounded p-2" required min="0">
        </div>

        <button class="bg-purple-600 text-white px-4 py-2 rounded">
            Simpan
        </button>
    </form>
</div>
@endsection
