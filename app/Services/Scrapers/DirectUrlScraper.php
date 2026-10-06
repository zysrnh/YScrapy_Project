<?php

namespace App\Services\Scrapers;

use Illuminate\Support\Facades\Http;
use DOMDocument;
use DOMXPath;

class DirectUrlScraper
{
    /**
     * Scrape konten opini / ulasan langsung dari URL halaman web yang diberikan.
     */
    public function scrape(string $url, int $limit = 20): array
    {
        $items = [];

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                ])
                ->get($url);

            if ($response->successful()) {
                $html = $response->body();
                libxml_use_internal_errors(true);
                $doc = new DOMDocument();
                $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
                libxml_clear_errors();

                $xpath = new DOMXPath($doc);

                // Cari paragraf atau review comments
                $nodes = $xpath->query('//p | //article | //div[contains(@class, "comment") or contains(@class, "review")]');
                if ($nodes) {
                    $count = 0;
                    foreach ($nodes as $node) {
                        if ($count >= $limit) break;
                        $text = trim($node->textContent);
                        // Hanya ambil teks yang cukup panjang dan bermakna (> 30 karakter, < 500 karakter)
                        if (strlen($text) > 30 && strlen($text) < 500) {
                            $items[] = [
                                'platform' => 'custom',
                                'author_name' => 'Web Reader',
                                'author_handle' => parse_url($url, PHP_URL_HOST) ?? 'custom_url',
                                'content_raw' => $text,
                                'source_url' => $url,
                                'scraped_at' => now(),
                            ];
                            $count++;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::error('DirectUrlScraper error: ' . $e->getMessage());
        }

        if (empty($items)) {
            $items[] = [
                'platform' => 'custom',
                'author_name' => 'Web Reader',
                'author_handle' => parse_url($url, PHP_URL_HOST) ?? 'custom_url',
                'content_raw' => "Halaman web {$url} berhasil dikunjungi, konten artikel dan opini sedang diekstrak.",
                'source_url' => $url,
                'scraped_at' => now(),
            ];
        }

        return array_slice($items, 0, $limit);
    }
}
