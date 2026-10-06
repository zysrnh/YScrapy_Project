<?php

namespace App\Services\Trending;

use App\Services\Scrapers\NewsScraper;
use Illuminate\Support\Facades\Http;

class TrendingDiscoveryService
{
    /**
     * Mengambil daftar headline & topik yang sedang trending/viral di Indonesia hari ini.
     */
    public function getTrendingTopics(int $limit = 12): array
    {
        $rssUrl = 'https://news.google.com/rss?hl=id&gl=ID&ceid=ID:id';
        $topics = [];

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                    'Accept' => 'application/rss+xml, application/xml, text/xml',
                ])
                ->get($rssUrl);

            if ($response->successful()) {
                $xmlString = $response->body();
                libxml_use_internal_errors(true);
                $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
                libxml_clear_errors();

                if ($xml && isset($xml->channel->item)) {
                    foreach ($xml->channel->item as $item) {
                        if (count($topics) >= $limit) break;

                        $rawTitle = (string)$item->title;
                        $link = (string)$item->link;
                        $pubDate = (string)$item->pubDate;
                        $source = (string)($item->source ?? 'Media Nasional');

                        // Bersihkan judul dari nama sumber (misal: "Judul Berita - Kompas.com")
                        $cleanTitle = trim(preg_replace('/\s*-\s*[^-]+$/', '', $rawTitle));
                        $cleanTitle = NewsScraper::sanitizeContent($cleanTitle);

                        // Ekstrak keyword fokus
                        $words = explode(' ', $cleanTitle);
                        $shortKeyword = implode(' ', array_slice($words, 0, 5));

                        $topics[] = [
                            'title' => $cleanTitle,
                            'keyword' => $shortKeyword,
                            'source' => $source,
                            'url' => $link,
                            'published_at' => $pubDate ? date('Y-m-d H:i', strtotime($pubDate)) : now()->format('Y-m-d H:i'),
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::error('TrendingDiscoveryService error: ' . $e->getMessage());
        }

        if (empty($topics)) {
            $topics = $this->fallbackTrendingTopics($limit);
        }

        return array_slice($topics, 0, $limit);
    }

    protected function fallbackTrendingTopics(int $limit): array
    {
        $defaults = [
            [
                'title' => 'Timnas Indonesia Siap Berlaga di Kualifikasi Piala Dunia',
                'keyword' => 'Timnas Indonesia',
                'source' => 'PSSI & Media Olahraga',
                'url' => 'https://news.google.com',
                'published_at' => now()->format('Y-m-d H:i'),
            ],
            [
                'title' => 'Perkembangan Pembangunan IKN Nusantara Tahap Terbaru',
                'keyword' => 'IKN Nusantara',
                'source' => 'Kementerian PUPR',
                'url' => 'https://news.google.com',
                'published_at' => now()->subHours(2)->format('Y-m-d H:i'),
            ],
            [
                'title' => 'Penyesuaian Tarif Pajak PPN dan Dampak Daya Beli Masyarakat',
                'keyword' => 'Tarif PPN 12%',
                'source' => 'Kemenkeu RI',
                'url' => 'https://news.google.com',
                'published_at' => now()->subHours(4)->format('Y-m-d H:i'),
            ],
            [
                'title' => 'Peluncuran Smartphone Flagship Terbaru Berbasis AI Generatif',
                'keyword' => 'Smartphone AI Flagship',
                'source' => 'Teknologi Indonesia',
                'url' => 'https://news.google.com',
                'published_at' => now()->subHours(6)->format('Y-m-d H:i'),
            ],
            [
                'title' => 'Efisiensi Layanan Publik Digital Terintegrasi di Kementerian',
                'keyword' => 'Layanan Digital Publik',
                'source' => 'Kemenpan RB',
                'url' => 'https://news.google.com',
                'published_at' => now()->subHours(8)->format('Y-m-d H:i'),
            ],
        ];

        return array_slice($defaults, 0, $limit);
    }
}
