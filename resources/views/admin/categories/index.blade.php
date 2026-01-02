@extends('layouts.app')

@section('title', 'Kelola Kategori')

@section('content')
<div class="py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Kelola Kategori</h1>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Form tambah kategori --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <form method="POST" action="{{ route('admin.categories.store') }}" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Elektronik"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-300"
                            required
                        >
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md">
                            Tambah
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tabel kategori --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($categories as $cat)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $loop->iteration }}</td>

                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <form method="POST" action="{{ route('admin.categories.update', $cat) }}" class="flex gap-2 items-center">
                                        @csrf
                                        @method('PUT')
                                        <input name="name" value="{{ $cat->name }}"
                                            class="border border-gray-300 rounded-md px-3 py-2 text-sm w-64">
                                        <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-md text-sm">
                                            Update
                                        </button>
                                    </form>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">{{ $cat->slug }}</td>

                                <td class="px-6 py-4 text-sm">
                                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}"
                                        onsubmit="return confirm('Yakin hapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    Belum ada kategori.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $categories->links() }}
            </div>

            <div class="mt-6">
                <a href="{{ route('admin.items.create') }}" class="text-purple-700 hover:underline">
                    ← Kembali ke Tambah Barang
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
