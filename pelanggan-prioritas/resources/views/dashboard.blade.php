<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- {{ __("You're logged in!") }} --}}
                    <h3 class="text-2xl font-bold mb-4">Selamat Datang di Sistem Pendukung Keputusan Penilaian Pelanggan Prioritas <br>
                    Menggunakan Metode TOPSIS</h3>
                    <h4>VISI <br>
                        Menjadi perusahaan transportir minyak BBM terkemuka yang handal dan terpercaya, dengan standar keselamatan dan efisiensi tertinggi, serta berkontribusi pada keberlanjutan energi nasional.
                        <br>
                        MISI <br>
                        a. Memberikan Layanan Berkualitas Tinggi:<br>
                        Menyediakan layanan transportasi minyak BBM yang aman, tepat waktu, dan efisien untuk memenuhi kebutuhan pelanggan dengan standar kualitas tertinggi.<br>
                        b. Mengembangkan Sumber Daya Manusia: <br>
                        Membangun tim yang profesional dan kompeten melalui pelatihan, pengembangan karir, dan lingkungan kerja yang mendukung.<br>
                        c. Inovasi dan Efisiensi Operasional:<br>
                        Mengadopsi inovasi teknologi terbaru untuk meningkatkan efisiensi operasional dan mengurangi dampak lingkungan.<br>
                        d. Peningkatan Berkelanjutan:<br>
                        Melakukan evaluasi dan peningkatan berkelanjutan pada proses dan layanan untuk tetap kompetitif dan memenuhi ekspektasi pasar yang berkembang.</h4>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
