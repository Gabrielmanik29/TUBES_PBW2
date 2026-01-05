<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Barang Baru') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-4">
                    <div class="p-3 bg-blue-100 text-blue-600 rounded-full">
                        <i class="fas fa-box-open text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Informasi Barang</h3>
                        <p class="text-sm text-gray-500">Lengkapi detail barang untuk menambahkannya ke inventaris.</p>
                    </div>
                </div>

                <div class="p-8">
                    <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="col-span-1 md:col-span-2">
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Barang</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-tag text-gray-400"></i>
                                    </div>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        class="pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-200"
                                        placeholder="Contoh: Proyektor Epson EB-X05">
                                </div>
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-list text-gray-400"></i>
                                    </div>
                                    <select name="category_id" id="category_id" class="pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-200">
                                        <option value="">-- Pilih Kategori --</option>
                                        @if(isset($categories))
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="stock" class="block text-sm font-semibold text-gray-700 mb-2">Stok Awal</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-cubes text-gray-400"></i>
                                    </div>
                                    <input type="number" name="stock" id="stock" value="{{ old('stock', 1) }}" min="1" required
                                        class="pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-200">
                                </div>
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                                <textarea name="description" id="description" rows="4"
                                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition duration-200"
                                    placeholder="Tuliskan spesifikasi atau kondisi barang...">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Barang</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:bg-gray-50 transition bg-white">
                                    <div class="space-y-1 text-center">
                                        <div class="mx-auto h-12 w-12 text-gray-400">
                                            <i class="fas fa-image text-4xl"></i>
                                        </div>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="photo" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>Upload file</span>
                                                <input id="photo" name="photo" type="file" class="sr-only">
                                            </label>
                                            <p class="pl-1">atau drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            PNG, JPG, GIF up to 2MB
                                        </p>
                                        <p id="file-name" class="text-xs text-green-600 font-semibold mt-2 hidden"></p>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-100 gap-4">
                            <a href="{{ route('admin.items.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-blue-500/30">
                                <i class="fas fa-save mr-2"></i> Simpan Barang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('photo').addEventListener('change', function(e) {
            var fileName = e.target.files[0].name;
            var fileNameDisplay = document.getElementById('file-name');
            fileNameDisplay.textContent = 'File terpilih: ' + fileName;
            fileNameDisplay.classList.remove('hidden');
        });
    </script>
</x-app-layout>