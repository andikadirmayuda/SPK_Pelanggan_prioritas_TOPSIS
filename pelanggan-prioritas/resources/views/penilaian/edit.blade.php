<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Penilaian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('penilaian.update', $penilaian->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun Penilaian</label>
                            <select name="tahun" id="tahun" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                                    <option value="{{ $year }}" {{ old('tahun', $penilaian->tahun) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                            @error('tahun')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="pelanggan_id" class="block text-sm font-medium text-gray-700">Pelanggan</label>
                            <select name="pelanggan_id" id="pelanggan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Pilih Pelanggan</option>
                                @foreach($pelanggans as $pelanggan)
                                    <option value="{{ $pelanggan->id }}" {{ (old('pelanggan_id', $penilaian->pelanggan_id) == $pelanggan->id) ? 'selected' : '' }}>
                                        {{ $pelanggan->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pelanggan_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="kriteria_id" class="block text-sm font-medium text-gray-700">Kriteria</label>
                            <select name="kriteria_id" id="kriteria_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Pilih Kriteria</option>
                                @foreach($kriterias as $kriteria)
                                    <option value="{{ $kriteria->id }}" {{ (old('kriteria_id', $penilaian->kriteria_id) == $kriteria->id) ? 'selected' : '' }}>
                                        {{ $kriteria->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kriteria_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="sub_kriteria_id" class="block text-sm font-medium text-gray-700">Sub Kriteria</label>
                            <select name="sub_kriteria_id" id="sub_kriteria_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Pilih Sub Kriteria</option>
                                @foreach($kriterias->find($penilaian->kriteria_id)->subKriteria as $subKriteria)
                                    <option value="{{ $subKriteria->id }}" {{ (old('sub_kriteria_id', $penilaian->sub_kriteria_id) == $subKriteria->id) ? 'selected' : '' }}>
                                        {{ $subKriteria->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sub_kriteria_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('penilaian.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kriteriaSelect = document.getElementById('kriteria_id');
            const subKriteriaSelect = document.getElementById('sub_kriteria_id');
            const currentSubKriteriaId = '{{ $penilaian->sub_kriteria_id }}';

            kriteriaSelect.addEventListener('change', function() {
                const kriteriaId = this.value;
                subKriteriaSelect.innerHTML = '<option value="">Pilih Sub Kriteria</option>';

                if (kriteriaId) {
                    fetch(`/get-sub-kriteria/${kriteriaId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(subKriteria => {
                                const option = document.createElement('option');
                                option.value = subKriteria.id;
                                option.textContent = subKriteria.nama;
                                if (subKriteria.id == currentSubKriteriaId) {
                                    option.selected = true;
                                }
                                subKriteriaSelect.appendChild(option);
                            });
                        });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
