<?php

namespace App\Services\Integration;

use Dompdf\Dompdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChessComApiErrorPreviewService
{
    public function jsonPathToHtmlPath(string $jsonRelativePath): string
    {
        return preg_replace('/\.json$/', '.html', $jsonRelativePath);
    }

    public function jsonPathToPngPath(string $jsonRelativePath): string
    {
        return preg_replace('/\.json$/', '.png', $jsonRelativePath);
    }

    public function previewHtmlExists(string $jsonRelativePath): bool
    {
        return Storage::disk("chesscom_api_errors")->exists($this->jsonPathToHtmlPath($jsonRelativePath));
    }

    public function previewImageExists(string $jsonRelativePath): bool
    {
        return Storage::disk("chesscom_api_errors")->exists($this->jsonPathToPngPath($jsonRelativePath));
    }

    /**
     * @param array<string, mixed> $error
     */
    public function ensurePreviewFiles(string $jsonRelativePath, array $error): void
    {
        $responseBody = isset($error["response_body"]) ? (string) $error["response_body"] : "";
        if (!$this->looksLikeHtml($responseBody)) {
            return;
        }

        $htmlPath = $this->jsonPathToHtmlPath($jsonRelativePath);
        $disk = Storage::disk("chesscom_api_errors");

        if (!$disk->exists($htmlPath)) {
            $disk->put($htmlPath, $responseBody);
        }

        $pngPath = $this->jsonPathToPngPath($jsonRelativePath);
        if (!$disk->exists($pngPath)) {
            $this->tryGeneratePngFromHtml($htmlPath, $responseBody, $pngPath);
        }
    }

    public function deletePreviewFiles(string $jsonRelativePath): void
    {
        $disk = Storage::disk("chesscom_api_errors");
        foreach ([$jsonRelativePath, $this->jsonPathToHtmlPath($jsonRelativePath), $this->jsonPathToPngPath($jsonRelativePath)] as $path) {
            if ($disk->exists($path)) {
                $disk->delete($path);
            }
        }
    }

    private function looksLikeHtml(string $body): bool
    {
        $trimmed = ltrim($body);
        if ($trimmed === "") {
            return false;
        }

        return stripos($trimmed, "<html") !== false || stripos($trimmed, "<!DOCTYPE") !== false;
    }

    private function tryGeneratePngFromHtml(string $relativeHtmlPath, string $html, string $relativePngPath): bool
    {
        $disk = Storage::disk("chesscom_api_errors");
        $htmlFullPath = $disk->path($relativeHtmlPath);
        $pngFullPath = $disk->path($relativePngPath);

        if ($this->generatePngWithWkhtmltoimage($htmlFullPath, $pngFullPath)) {
            return true;
        }

        if ($this->generatePngWithHeadlessBrowser($htmlFullPath, $pngFullPath)) {
            return true;
        }

        if ($this->generatePngWithDompdfImagick($html, $relativePngPath)) {
            return true;
        }

        Log::debug("ChessComApiErrorPreviewService: nao foi possivel gerar PNG do HTML da resposta.");

        return false;
    }

    private function generatePngWithWkhtmltoimage(string $htmlFullPath, string $pngFullPath): bool
    {
        if (!$this->commandExists("wkhtmltoimage")) {
            return false;
        }

        $command = sprintf(
            "wkhtmltoimage --width 1280 --quality 85 %s %s 2>/dev/null",
            escapeshellarg($htmlFullPath),
            escapeshellarg($pngFullPath)
        );
        exec($command, $output, $exitCode);

        return $exitCode === 0 && is_file($pngFullPath);
    }

    private function generatePngWithHeadlessBrowser(string $htmlFullPath, string $pngFullPath): bool
    {
        foreach (["google-chrome", "chromium-browser", "chromium"] as $browser) {
            if (!$this->commandExists($browser)) {
                continue;
            }

            $command = sprintf(
                "%s --headless --disable-gpu --no-sandbox --window-size=1280,800 --screenshot=%s file://%s 2>/dev/null",
                escapeshellarg($browser),
                escapeshellarg($pngFullPath),
                $htmlFullPath
            );
            exec($command, $output, $exitCode);
            if ($exitCode === 0 && is_file($pngFullPath)) {
                return true;
            }
        }

        return false;
    }

    private function generatePngWithDompdfImagick(string $html, string $relativePngPath): bool
    {
        if (!class_exists(Dompdf::class) || !extension_loaded("imagick")) {
            return false;
        }

        $tmpPdf = tempnam(sys_get_temp_dir(), "chesscom_err_");
        if ($tmpPdf === false) {
            return false;
        }

        $tmpPdfPath = $tmpPdf . ".pdf";

        try {
            $dompdf = new Dompdf(["isRemoteEnabled" => false]);
            $dompdf->loadHtml($this->sanitizeHtmlForDompdf($html));
            $dompdf->setPaper("A4", "portrait");
            $dompdf->render();
            file_put_contents($tmpPdfPath, $dompdf->output());

            $imagick = new \Imagick();
            $imagick->setResolution(120, 120);
            $imagick->readImage($tmpPdfPath . "[0]");
            $imagick->setImageFormat("png");
            Storage::disk("chesscom_api_errors")->put($relativePngPath, $imagick->getImageBlob());
            $imagick->clear();
            $imagick->destroy();

            return Storage::disk("chesscom_api_errors")->exists($relativePngPath);
        } catch (\Throwable $exception) {
            Log::debug("ChessComApiErrorPreviewService Dompdf/Imagick: " . $exception->getMessage());

            return false;
        } finally {
            @unlink($tmpPdf);
            @unlink($tmpPdfPath);
        }
    }

    private function sanitizeHtmlForDompdf(string $html): string
    {
        $withoutScripts = preg_replace("/<script\b[^>]*>[\s\S]*?<\/script>/mi", "", $html);

        return is_string($withoutScripts) ? $withoutScripts : $html;
    }

    private function commandExists(string $command): bool
    {
        $path = trim((string) shell_exec(sprintf("command -v %s 2>/dev/null", escapeshellarg($command))));

        return $path !== "";
    }
}
