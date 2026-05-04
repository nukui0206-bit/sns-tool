<?php

namespace App\Services\AiCopywriter;

interface AiCopywriterInterface
{
    /**
     * プロンプトから投稿文案を生成する。
     *
     * @param string                $prompt   ユーザーが入力したお題（例：「新商品の告知」）
     * @param array<string, mixed>  $options  追加オプション（platform / tone / length など。Phase 7 以降で活用）
     */
    public function generate(string $prompt, array $options = []): string;
}
