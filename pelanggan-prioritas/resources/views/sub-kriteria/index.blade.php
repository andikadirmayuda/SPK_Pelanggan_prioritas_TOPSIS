<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sub Kriteria') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('sub-kriteria.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tambah Sub Kriteria
                        </a>
                    </div>

                    @foreach($kriterias as $kriteria)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4 bg-gray-100 p-3 rounded">Kriteria: {{ $kriteria->nama }}</h3>
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 mb-4">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3">No</th>                                        <th class="px-6 py-3">Nama Sub Kriteria</th>
                                        <th class="px-6 py-3">Nilai</th>
                                        <th class="px-6 py-3">Keterangan</th>
                                        <th class="px-6 py-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kriteria->subKriteria as $index => $sub_kriteria)
                                        <tr class="bg-white border-b">
                                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4">{{ $sub_kriteria->nama }}</td>
                                            <td class="px-6 py-4">{{ $sub_kriteria->nilai }}</td>
                                            <td class="px-6 py-4">{{ $sub_kriteria->keterangan }}</td>                                            <td class="px-6 py-4">
                                                <a href="{{ route('sub-kriteria.edit', $sub_kriteria) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                                <form action="{{ route('sub-kriteria.destroy', $sub_kriteria) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus sub kriteria ini?')">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>