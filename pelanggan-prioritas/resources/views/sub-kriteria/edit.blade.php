<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Sub Kriteria') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">                    <form method="POST" action="{{ route('sub-kriteria.update', $sub_kriteria) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="kriteria_id" value="Kriteria" />
                            <select id="kriteria_id" name="kriteria_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Pilih Kriteria</option>
                                @foreach ($kriterias as $kriteria)                                    <option value="{{ $kriteria->id }}" {{ (old('kriteria_id', $sub_kriteria->kriteria_id) == $kriteria->id) ? 'selected' : '' }}>
                                        {{ $kriteria->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('kriteria_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="nama" value="Nama Sub Kriteria" />
                            <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $sub_kriteria->nama)" required />
                            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                        </div>                        <div>
                            <x-input-label for="nilai" value="Nilai" />
                            <select id="nilai" name="nilai" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required onchange="updateKeterangan(this.value)">
                                <option value="">Pilih Nilai</option>
                                @foreach ($nilaiOptions as $nilai => $keterangan)                                    <option value="{{ $nilai }}" {{ old('nilai', $sub_kriteria->nilai) == $nilai ? 'selected' : '' }}>
                                        {{ $nilai }} - {{ $keterangan }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('nilai')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="keterangan" value="Keterangan" />
                            <x-text-input id="keterangan" name="keterangan" type="text" class="mt-1 block w-full bg-gray-100" readonly :value="old('keterangan', $sub_kriteria->keterangan)" required />
                            <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                        </div>

                        <script>
                            function updateKeterangan(nilai) {
                                const keteranganMap = @json($nilaiOptions);
                                document.getElementById('keterangan').value = keteranganMap[nilai] || '';
                            }
                            // Set initial keterangan if nilai is pre-selected
                            document.addEventListener('DOMContentLoaded', function() {
                                const nilai = document.getElementById('nilai').value;
                                if (nilai) {
                                    updateKeterangan(nilai);
                                }
                            });
                        </script>

                        <div class="flex items-center gap-4">
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