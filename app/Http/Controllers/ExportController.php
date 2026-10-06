<?php

namespace App\Http\Controllers;

use App\Models\ScrapedFeedback;
use App\Models\ScrapeJob;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function exportCsv(Request $request, $jobId = null)
    {
        $fileName = 'yscrapy_sentiment_' . date('Y-m-d_His') . '.csv';

        $query = ScrapedFeedback::query();
        if ($jobId) {
            $query->where('scrape_job_id', $jobId);
        }

        if ($request->filled('sentiment') && $request->sentiment !== 'all') {
            $query->where('sentiment_label', $request->sentiment);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM agar rapi saat dibuka di Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID',
                'Platform',
                'Author',
                'Handle',
                'Konten Teks',
                'Label Sentimen',
                'Skor Sentimen',
                'Kata Kunci Pemicu',
                'URL Sumber',
                'Tanggal Diambil'
            ]);

            $query->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    $tokens = is_array($row->sentiment_tokens) 
                        ? implode(', ', array_column($row->sentiment_tokens, 'word'))
                        : '';

                    fputcsv($handle, [
                        $row->id,
                        ucfirst($row->platform),
                        $row->author_name,
                        $row->author_handle,
                        $row->content_raw,
                        strtoupper($row->sentiment_label),
                        $row->sentiment_score,
                        $tokens,
                        $row->source_url,
                        $row->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
