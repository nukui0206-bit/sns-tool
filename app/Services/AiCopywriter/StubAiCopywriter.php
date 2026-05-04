<?php

namespace App\Services\AiCopywriter;

use Illuminate\Support\Facades\Log;

/**
 * 開発・動作確認用のスタブ実装。実 AI API を叩かず、プロンプトを埋め込んだサンプル文を返す。
 * Phase 7 で OpenAiCopywriter / ClaudeCopywriter などに差し替え予定。
 */
class StubAiCopywriter implements AiCopywriterInterface
{
    /**
     * @var array<int, string>
     */
    private const TEMPLATES = [
        "【AI下書き サンプル①】\n%s\n\n本日も素敵な一日になりますように✨\nぜひチェックしてみてください👀\n\n#brand #lifestyle #日常",
        "%s について新しい投稿です📣\n\n詳細はプロフィールリンクから🔗\nお気に入りに追加してくださいね！\n\n#おすすめ #PR #トレンド",
        "今日のテーマ：%s\n\n皆さんはどう思いますか？\nコメントで教えてください💭\n\n#engagement #コミュニティ #みんなの声",
        "✨ %s ✨\n\nお待たせしました！\n\n気になる方はぜひ DM またはコメントで📩\n限定情報も配信中です！\n\n#お知らせ #限定 #フォロー歓迎",
    ];

    public function generate(string $prompt, array $options = []): string
    {
        $template = self::TEMPLATES[array_rand(self::TEMPLATES)];
        $content = sprintf($template, $prompt);

        Log::info('[StubAiCopywriter] generate', [
            'prompt' => $prompt,
            'options' => $options,
            'length' => mb_strlen($content),
        ]);

        return $content;
    }
}
