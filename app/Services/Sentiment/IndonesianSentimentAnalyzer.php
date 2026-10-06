<?php

namespace App\Services\Sentiment;

class IndonesianSentimentAnalyzer
{
    protected array $positiveWords;
    protected array $negativeWords;
    protected array $intensifiers;
    protected array $negations;
    protected array $slangMap;

    public function __construct()
    {
        $this->positiveWords = LexiconData::positiveWords();
        $this->negativeWords = LexiconData::negativeWords();
        $this->intensifiers = LexiconData::intensifiers();
        $this->negations = LexiconData::negations();
        $this->slangMap = LexiconData::slangDictionary();
    }

    /**
     * Bersihkan teks: lowercasing, hapus link, username @, tanda baca berlebih.
     */
    public function cleanText(string $text): string
    {
        // 1. Lowercase
        $text = mb_strtolower($text, 'UTF-8');

        // 2. Hapus URL http/https
        $text = preg_replace('/https?:\/\/\S+/i', ' ', $text);

        // 3. Hapus mention (@username)
        $text = preg_replace('/@[\w.-]+/i', ' ', $text);

        // 4. Hapus hashtag symbol tapi simpan teksnya
        $text = preg_replace('/#([\w]+)/i', '$1', $text);

        // 5. Hapus karakter non-alfanumerik selain spasi & strip
        $text = preg_replace('/[^a-z0-9\s-]/i', ' ', $text);

        // 6. Normalisasi karakter berulang (misal: "baguuus" -> "bagus", "paraaaah" -> "parah")
        $text = preg_replace('/(.)\1{2,}/u', '$1$1', $text);

        // 7. Bersihkan spasi berlebih
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * Analisis teks dan hasilkan skor (-1 s/d 1), label, dan kata pemicu sentimen.
     */
    public function analyze(string $rawText): array
    {
        $cleanText = $this->cleanText($rawText);

        if (empty($cleanText)) {
            return [
                'clean_text' => '',
                'score' => 0.0,
                'label' => 'neutral',
                'tokens' => [],
            ];
        }

        $rawTokens = explode(' ', $cleanText);
        $normalizedTokens = [];

        // Terjemahkan kata slang/singkatan
        foreach ($rawTokens as $token) {
            $token = trim($token);
            if ($token === '') continue;
            $normalizedTokens[] = $this->slangMap[$token] ?? $token;
        }

        $score = 0.0;
        $matchedTokens = [];
        $tokenCount = count($normalizedTokens);

        for ($i = 0; $i < $tokenCount; $i++) {
            $word = $normalizedTokens[$i];

            // Cek frasa 2 kata (bigram) misal: "worth it", "terima kasih"
            $bigram = ($i < $tokenCount - 1) ? $word . ' ' . $normalizedTokens[$i + 1] : null;

            $currentWordWeight = 0;
            $matchedWord = null;
            $isBigram = false;

            if ($bigram && isset($this->positiveWords[$bigram])) {
                $currentWordWeight = $this->positiveWords[$bigram];
                $matchedWord = $bigram;
                $isBigram = true;
            } elseif ($bigram && isset($this->negativeWords[$bigram])) {
                $currentWordWeight = $this->negativeWords[$bigram];
                $matchedWord = $bigram;
                $isBigram = true;
            } elseif (isset($this->positiveWords[$word])) {
                $currentWordWeight = $this->positiveWords[$word];
                $matchedWord = $word;
            } elseif (isset($this->negativeWords[$word])) {
                $currentWordWeight = $this->negativeWords[$word];
                $matchedWord = $word;
            }

            if ($currentWordWeight !== 0 && $matchedWord) {
                // Periksa apakah ada kata negasi 1-2 kata sebelumnya
                $isNegated = false;
                for ($k = max(0, $i - 2); $k < $i; $k++) {
                    if (in_array($normalizedTokens[$k], $this->negations, true)) {
                        $isNegated = true;
                        break;
                    }
                }

                // Periksa intensifier (penguat makna)
                $multiplier = 1.0;
                for ($m = max(0, $i - 2); $m < $i; $m++) {
                    if (isset($this->intensifiers[$normalizedTokens[$m]])) {
                        $multiplier = $this->intensifiers[$normalizedTokens[$m]];
                        break;
                    }
                }
                // Cek juga 1 kata sesudahnya (misal: "bagus banget")
                $nextIndex = $isBigram ? $i + 2 : $i + 1;
                if ($nextIndex < $tokenCount && isset($this->intensifiers[$normalizedTokens[$nextIndex]])) {
                    $multiplier = $this->intensifiers[$normalizedTokens[$nextIndex]];
                }

                $wordScore = $currentWordWeight * $multiplier;

                // Jika dinegasi: balikkan polaritas skor
                if ($isNegated) {
                    $wordScore = -$wordScore * 0.9;
                }

                $score += $wordScore;
                $matchedTokens[] = [
                    'word' => $matchedWord,
                    'weight' => round($wordScore, 2),
                    'type' => $wordScore > 0 ? 'positive' : ($wordScore < 0 ? 'negative' : 'neutral'),
                    'negated' => $isNegated,
                ];

                if ($isBigram) {
                    $i++; // Lewati kata kedua dari bigram
                }
            }
        }

        // Normalisasi skor ke rentang -1.0 s/d 1.0
        $normalizedScore = 0.0;
        if (!empty($matchedTokens)) {
            $totalWeight = count($matchedTokens) * 2.5;
            $normalizedScore = round(max(-1.0, min(1.0, $score / max(1.0, $totalWeight))), 2);
        }

        // Tentukan klasifikasi label sentimen
        $label = 'neutral';
        if ($normalizedScore >= 0.08) {
            $label = 'positive';
        } elseif ($normalizedScore <= -0.08) {
            $label = 'negative';
        }

        return [
            'clean_text' => implode(' ', $normalizedTokens),
            'score' => $normalizedScore,
            'label' => $label,
            'tokens' => $matchedTokens,
        ];
    }
}
