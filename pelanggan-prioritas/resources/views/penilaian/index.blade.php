<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Penilaian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <a href="{{ route('penilaian.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Tambah Penilaian
                            </a>
                        </div>
                        <div class="flex items-center space-x-4">                            <form action="{{ route('penilaian.index') }}" method="GET" class="flex items-center space-x-2">
                                <label for="filter_tahun" class="text-sm font-medium text-gray-700">Filter Tahun:</label>
                                <select name="tahun" id="filter_tahun" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @php
                                        $years = App\Models\Penilaian::select('tahun')
                                            ->distinct()
                                            ->orderBy('tahun', 'desc')
                                            ->pluck('tahun');
                                        $selectedYear = request('tahun', date('Y'));
                                    @endphp
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div><div class="space-y-6">
                        @foreach($pelanggans as $pelanggan)
                            <div class="border rounded-lg overflow-hidden">
                                <div class="bg-gray-100 px-4 py-3 border-b">
                                    <h3 class="text-lg font-medium">{{ $pelanggan->nama }}</h3>
                                </div>
                                
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kriteria</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sub Kriteria</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">                                            @foreach($pelanggan->penilaian->where('tahun', $selectedYear) as $index => $penilaian)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">{{ $penilaian->kriteria->nama }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">{{ $penilaian->subKriteria->nama }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">{{ $penilaian->subKriteria->nilai }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">{{ $penilaian->subKriteria->keterangan }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                        <a href="{{ route('penilaian.edit', $penilaian->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                                        <form action="{{ route('penilaian.destroy', $penilaian->id) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus penilaian ini?')">
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
    </div>
</x-app-layout>
