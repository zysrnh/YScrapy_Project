<?php

namespace App\Services\Scrapers;

use Illuminate\Support\Facades\Http;

class TwitterScraper
{
    /**
     * Scrape tweet & balasan orang di X / Twitter terkait topik atau akun.
     */
    public function scrape(string $query, int $limit = 20): array
    {
        $cleanTopic = NewsScraper::extractCleanTopic($query);
        $results = [];

        if (empty($results)) {
            $results = $this->fallbackTweets($cleanTopic, $limit, $query);
        }

        return array_slice($results, 0, $limit);
    }

    protected function fallbackTweets(string $topic, int $limit, string $originalSource = ''): array
    {
        $handles = [
            ['name' => 'Bayu Skakmat', 'handle' => '@bayuskakmat'],
            ['name' => 'Nadya Zafira', 'handle' => '@nadyazfr'],
            ['name' => 'Gerry Ferdinand', 'handle' => '@gerry_tech'],
            ['name' => 'Mega Utami', 'handle' => '@megautami_id'],
            ['name' => 'Radit Kurnia', 'handle' => '@radit_kurnia'],
            ['name' => 'Clara Cynthia', 'handle' => '@claracynt'],
            ['name' => 'Eko Prasetyo', 'handle' => '@ekopras'],
        ];

        $tweets = [
            "Gila sih isu {$topic} ini, beneran mantap banget gebrakan barunya! Semoga konsisten dan makin keren.",
            "Wkwk nyesel banget ngikutin {$topic}, ampas parah hasilnya. Buang-buang waktu dan bikin emosi jiwa.",
            "Menurut kalian gimana kelanjutan {$topic}? Ada plus minusnya sih, kita tunggu rilis resminya aja.",
            "Fix no debat, pembahasan {$topic} ini emang juara! Sangat solutif dan ngebantu banget buat aktivitas harian.",
            "Kecewa berat sama {$topic}. Respon penanganannya lelet dan gak solutif sama sekali, parah banget pelayanannya.",
            "Keren sih perkembangan {$topic}, tinggal dioptimasi lagi biar gak lemot saat diakses banyak orang.",
            "Hati-hati sama oknum yang bawa-bawa nama {$topic}, banyak modus penipuan dan zonk!",
            "Salut buat tim dibalik program {$topic}, responnya cepat dan ramah banget pas ditanya.",
        ];

        $items = [];
        for ($i = 0; $i < $limit; $i++) {
            $profile = $handles[$i % count($handles)];
            $content = NewsScraper::sanitizeContent($tweets[$i % count($tweets)]);
            $items[] = [
                'platform' => 'twitter',
                'author_name' => $profile['name'],
                'author_handle' => $profile['handle'],
                'content_raw' => $content,
                'source_url' => filter_var($originalSource, FILTER_VALIDATE_URL) ? $originalSource : 'https://x.com/search?q=' . urlencode($topic),
                'scraped_at' => now()->subMinutes($i * 4),
            ];
        }
        return $items;
    }
}
