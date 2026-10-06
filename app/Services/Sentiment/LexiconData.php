<?php

namespace App\Services\Sentiment;

class LexiconData
{
    /**
     * Kamus kata positif bahasa Indonesia beserta bobotnya (1 - 3).
     */
    public static function positiveWords(): array
    {
        return [
            // Apresiasi & Kepuasan
            'bagus' => 2, 'mantap' => 3, 'keren' => 3, 'puas' => 2, 'hebat' => 2,
            'suka' => 2, 'rekomendasi' => 2, 'recommended' => 2, 'juara' => 3, 'top' => 2,
            'terbaik' => 3, 'senang' => 2, 'cinta' => 3, 'bahagia' => 2, 'ramah' => 2,
            'cepat' => 2, 'rapi' => 2, 'responsif' => 2, 'mantul' => 3, 'berkah' => 2,
            'sip' => 2, 'mantep' => 3, 'joss' => 3, 'jos' => 3, 'oke' => 1,
            'aman' => 2, 'amanah' => 3, 'sukses' => 2, 'memuaskan' => 3, 'istimewa' => 3,
            'berkualitas' => 3, 'asli' => 2, 'original' => 2, 'ori' => 2, 'murah' => 2,
            'terjangkau' => 2, 'terpercaya' => 3, 'bersih' => 2, 'sejuk' => 2, 'nyaman' => 3,
            'enak' => 2, 'lezat' => 3, 'gurih' => 2, 'segar' => 2, 'wangi' => 2,
            'presisi' => 2, 'fungsional' => 2, 'membantu' => 2, 'bermanfaat' => 3,
            'sempurna' => 3, 'terima kasih' => 2, 'makasih' => 2, 'alhamdulillah' => 2,
            'patut dicoba' => 2, 'worth' => 2, 'worth it' => 3, 'worthit' => 3, 'kece' => 2,
            'cakep' => 2, 'menarik' => 2, 'indah' => 2, 'luwes' => 2, 'ganteng' => 2,
            'cantik' => 2, 'apik' => 2, 'mudah' => 2, 'gampang' => 2, 'praktis' => 2,
            'solutif' => 3, 'inovatif' => 2, 'progresif' => 2, 'unggul' => 3, 'bintang 5' => 3,
            'bintang lima' => 3, 'profesional' => 3, 'kompeten' => 2, 'disiplin' => 2,
            'adil' => 2, 'transparan' => 2, 'positif' => 2, 'setuju' => 1, 'cocok' => 2,
            'pas' => 1, 'sesuai' => 2, 'terbantu' => 2, 'senang sekali' => 3, 'terharu' => 2,
            'salut' => 3, 'respect' => 2, 'respectful' => 2, 'keren abis' => 3, 'gokil' => 2,
            'solid' => 2, 'stabil' => 2, 'lancar' => 2, 'awet' => 2, 'tahan lama' => 2,
        ];
    }

    /**
     * Kamus kata negatif bahasa Indonesia beserta bobotnya (-1 s/d -3).
     */
    public static function negativeWords(): array
    {
        return [
            // Kekecewaan & Kerusakan
            'jelek' => -2, 'buruk' => -3, 'kecewa' => -3, 'parah' => -3, 'nyesel' => -3,
            'rugi' => -3, 'lambat' => -2, 'lelet' => -3, 'rusak' => -3, 'pecah' => -3,
            'cacat' => -3, 'bohong' => -3, 'penipu' => -3, 'tipu' => -3, 'payah' => -2,
            'bau' => -2, 'kotor' => -2, 'hancur' => -3, 'ampas' => -3, 'kapok' => -3,
            'buang' => -2, 'busuk' => -3, 'sampah' => -3, 'lemot' => -3, 'eror' => -2,
            'error' => -2, 'zonk' => -3, 'ribet' => -2, 'mahal' => -2, 'sombong' => -2,
            'kasar' => -3, 'jutek' => -2, 'benci' => -3, 'marah' => -3, 'kesal' => -2,
            'muak' => -3, 'batal' => -2, 'bocor' => -3, 'palsu' => -3, 'kw' => -2,
            'tertunda' => -2, 'pending' => -1, 'lama' => -1, 'pusing' => -2, 'mumet' => -2,
            'stress' => -2, 'stres' => -2, 'menyesal' => -3, 'rugi bandar' => -3, 'males' => -2,
            'malas' => -2, 'susah' => -2, 'sulit' => -1, 'gak jelas' => -3, 'ga jelas' => -3,
            'mengecewakan' => -3, 'merugikan' => -3, 'bodong' => -3, 'bikin emosi' => -3,
            'emosi' => -2, 'ilfeel' => -3, 'gagal' => -2, 'sia-sia' => -3, 'basi' => -2,
            'gadungan' => -3, 'jahat' => -3, 'curang' => -3, 'korupsi' => -3, 'licik' => -3,
            'lamban' => -2, 'ngelag' => -2, 'lag' => -2, 'crash' => -2, 'kemahalan' => -2,
            // Kata Kasar / Slang Negatif Ekstrem
            'kontol' => -3, 'bangsat' => -3, 'anjing' => -3, 'babi' => -3, 'tahi' => -3,
            'tai' => -3, 'brengsek' => -3, 'bajingan' => -3, 'tolol' => -3, 'goblok' => -3,
            'idiot' => -3, 'kampret' => -2, 'asu' => -3, 'pantek' => -3, 'fak' => -3,
            'fuck' => -3, 'shit' => -3,
        ];
    }

    /**
     * Kata-kata penguat bobot sentimen (Intensifier).
     */
    public static function intensifiers(): array
    {
        return [
            'sangat' => 1.5,
            'banget' => 1.5,
            'bgt' => 1.5,
            'amat' => 1.4,
            'sekali' => 1.4,
            'luar biasa' => 1.6,
            'parah' => 1.5,
            'bener-bener' => 1.5,
            'benar-benar' => 1.5,
            'super' => 1.4,
            'ekstrem' => 1.5,
            'beneran' => 1.3,
            'terlalu' => 1.3,
            'banyak' => 1.2,
        ];
    }

    /**
     * Kata negasi pembalik makna.
     */
    public static function negations(): array
    {
        return [
            'tidak', 'bukan', 'jangan', 'tak', 'kurang', 'tiada', 'gak', 'ga', 'nggak', 'ngga',
            'kaga', 'kagak', 'nda', 'ndak', 'belum', 'blm', 'tanpa',
        ];
    }

    /**
     * Kamus normalisasi slang dan singkatan umum bahasa Indonesia.
     */
    public static function slangDictionary(): array
    {
        return [
            'bgt' => 'banget',
            'bgtt' => 'banget',
            'bener2' => 'benar-benar',
            'bener' => 'benar',
            'recsel' => 'rekomendasi',
            'recomended' => 'rekomendasi',
            'recommended' => 'rekomendasi',
            'mantul' => 'mantap',
            'mantep' => 'mantap',
            'joss' => 'bagus',
            'jos' => 'bagus',
            'gk' => 'tidak',
            'ga' => 'tidak',
            'gak' => 'tidak',
            'ngga' => 'tidak',
            'nggak' => 'tidak',
            'kaga' => 'tidak',
            'kagak' => 'tidak',
            'ndak' => 'tidak',
            'blm' => 'belum',
            'sdh' => 'sudah',
            'udah' => 'sudah',
            'udh' => 'sudah',
            'tp' => 'tapi',
            'tpi' => 'tapi',
            'dgn' => 'dengan',
            'dr' => 'dari',
            'krn' => 'karena',
            'karna' => 'karena',
            'sy' => 'saya',
            'km' => 'kamu',
            'bgs' => 'bagus',
            'bgus' => 'bagus',
            'jlek' => 'jelek',
            'ancur' => 'hancur',
            'rusakk' => 'rusak',
            'kecewaa' => 'kecewa',
            'nyesell' => 'nyesel',
            'goblokk' => 'goblok',
            'tololl' => 'tolol',
            'bgst' => 'bangsat',
            'kntl' => 'kontol',
            'asw' => 'asu',
        ];
    }
}
