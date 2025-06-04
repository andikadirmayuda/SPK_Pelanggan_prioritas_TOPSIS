<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Sub Kriteria') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('sub-kriteria.store') }}" class="space-y-6">
                        @csrf

                        <div class="mb-4">
                            <label for="kriteria_id" class="block text-sm font-medium text-gray-700">Pilih Kriteria</label>
                            <select id="kriteria_id" name="kriteria_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Pilih Kriteria</option>
                                @foreach ($kriterias as $kriteria)
                                    <option value="{{ $kriteria->id }}" {{ old('kriteria_id') == $kriteria->id ? 'selected' : '' }}>
                                        {{ $kriteria->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('kriteria_id')" class="mt-2" />
                        </div>                        <div class="mb-4">
                            <label for="nama" class="block text-sm font-medium text-gray-700">Nama Sub Kriteria</label>
                            <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama')" required autofocus />
                            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                            <p class="mt-2 text-sm text-gray-500">Akan dibuat otomatis untuk nilai 1-4 (Kurang, Cukup, Baik, Sangat Baik)</p>
                        </div>                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                            <a href="{{ route('sub-kriteria.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Batal') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>