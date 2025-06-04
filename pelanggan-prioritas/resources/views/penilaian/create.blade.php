<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Penilaian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('penilaian.store') }}">
                        @csrf

                        <div class="mb-6">
                            <label for="pelanggan_id" class="block text-sm font-medium text-gray-700">Pelanggan</label>
                            <select name="pelanggan_id" id="pelanggan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Pilih Pelanggan</option>
                                @foreach ($pelanggans as $pelanggan)
                                    <option value="{{ $pelanggan->id }}" {{ old('pelanggan_id') == $pelanggan->id ? 'selected' : '' }}>
                                        {{ $pelanggan->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pelanggan_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-6">
                            @foreach ($kriterias as $kriteria)
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <h3 class="text-lg font-medium mb-4">{{ $kriteria->nama }}</h3>
                                <input type="hidden" name="penilaian[{{ $loop->index }}][kriteria_id]" value="{{ $kriteria->id }}">
                                
                                <div class="grid gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Nilai:</label>
                                        <div class="space-y-2">
                                            @foreach ($kriteria->subKriteria as $sub)
                                                <div class="flex items-center">
                                                    <input type="radio" 
                                                           id="sub_{{ $kriteria->id }}_{{ $sub->id }}" 
                                                           name="penilaian[{{ $loop->parent->index }}][sub_kriteria_id]" 
                                                           value="{{ $sub->id }}"
                                                           {{ old("penilaian.{$loop->parent->index}.sub_kriteria_id") == $sub->id ? 'checked' : '' }}
                                                           class="mr-2"
                                                           required>
                                                    <label for="sub_{{ $kriteria->id }}_{{ $sub->id }}" class="text-sm text-gray-700">
                                                        {{ $sub->nama }} (Nilai: {{ $sub->nilai }} - {{ $sub->keterangan }})
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @error("penilaian.{$loop->index}.sub_kriteria_id")
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('penilaian.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
