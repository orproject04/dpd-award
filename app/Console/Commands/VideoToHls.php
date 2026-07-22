<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class VideoToHls extends Command
{
    protected $signature = 'video:hls
        {input : Absolute or relative path to the source video (e.g. public/videos/Awards2025.mp4)}
        {--name= : Output folder name under public/videos/ (defaults to source filename without extension)}
        {--segment=6 : Segment length in seconds}
        {--height=720 : Target output height in pixels (width auto-scales)}';

    protected $description = 'Transcode a video into HLS (m3u8 + ts segments) for progressive streaming.';

    public function handle(): int
    {
        $input = $this->argument('input');

        if (! file_exists($input)) {
            $this->error("Input file not found: {$input}");
            return self::FAILURE;
        }

        $name = $this->option('name') ?: pathinfo($input, PATHINFO_FILENAME);
        $name = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
        $outDir = public_path("videos/{$name}");

        if (! is_dir($outDir) && ! mkdir($outDir, 0755, true) && ! is_dir($outDir)) {
            $this->error("Could not create output directory: {$outDir}");
            return self::FAILURE;
        }

        $segment = (int) $this->option('segment');
        $height = (int) $this->option('height');
        $playlist = "{$outDir}/index.m3u8";
        $segmentPattern = "{$outDir}/seg_%03d.ts";

        $cmd = [
            'ffmpeg', '-y', '-i', $input,
            '-vf', "scale=-2:{$height}",
            '-c:v', 'libx264', '-preset', 'veryfast', '-crf', '23',
            '-c:a', 'aac', '-b:a', '128k',
            '-hls_time', (string) $segment,
            '-hls_list_size', '0',
            '-hls_segment_filename', $segmentPattern,
            '-f', 'hls',
            $playlist,
        ];

        $this->info('Running: ' . implode(' ', $cmd));

        $process = new Process($cmd);
        $process->setTimeout(null);
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (! $process->isSuccessful()) {
            $this->error('ffmpeg failed. See output above.');
            return self::FAILURE;
        }

        $this->newLine();
        $this->info("Done. Playlist: /videos/{$name}/index.m3u8");
        return self::SUCCESS;
    }
}
