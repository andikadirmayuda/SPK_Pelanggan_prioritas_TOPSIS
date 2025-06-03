<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Kriteria') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">                <div class="p-6 text-gray-900">                    <form method="POST" action="{{ route('kriteria.update', $kriteria) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="nama" class="block text-sm font-medium text-gray-700">Nama Kriteria</label>
                            <input type="text" name="nama" id="nama" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required value="{{ old('nama', $kriteria->nama) }}">
                            @error('nama')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="bobot" class="block text-sm font-medium text-gray-700">
                                Bobot (0-1)
                                <span class="text-sm text-gray-500">
                                    - Sisa bobot yang tersedia: {{ number_format($sisaBobot + $kriteria->bobot, 2) }}
                                </span>
                            </label>
                            <input type="number" step="0.01" min="0" max="{{ $sisaBobot + $kriteria->bobot }}" name="bobot" id="bobot" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required value="{{ old('bobot', $kriteria->bobot) }}">
                            @error('bobot')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="tipe" class="block text-sm font-medium text-gray-700">Tipe</label>
                            <select name="tipe" id="tipe" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Pilih Tipe</option>
                                <option value="benefit" {{ (old('tipe', $kriteria->tipe) == 'benefit') ? 'selected' : '' }}>Benefit</option>
                                <option value="cost" {{ (old('tipe', $kriteria->tipe) == 'cost') ? 'selected' : '' }}>Cost</option>
                            </select>
                            @error('tipe')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('kriteria.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
