<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class ScrapeController extends Controller
{
    public function page()
    {
        return view('scrape');
    }

    public function run(Request $request)
    {
        $pages       = max(1,   min(30,   (int) $request->input('pages', 30)));
        $delay       = max(200, min(5000, (int) $request->input('delay', 400)));
        $detailDelay = max(300, min(5000, (int) $request->input('detail_delay', 700)));

        return response()->stream(function () use ($pages, $delay, $detailDelay) {
            set_time_limit(0);
            ini_set('max_execution_time', 0);

            $send = function (string $line) {
                echo 'data: ' . json_encode(['line' => $line]) . "\n\n";
                ob_flush(); flush();
            };

            $send("Starting scrape — {$pages} pages (page delay: {$delay}ms, detail delay: {$detailDelay}ms)...");

            $process = new Process([
                PHP_BINARY, base_path('artisan'), 'scrape:myhome',
                "--pages={$pages}", "--delay={$delay}", "--detail-delay={$detailDelay}",
            ]);
            $process->setTimeout(0);
            $process->start();

            foreach ($process as $type => $data) {
                foreach (explode("\n", trim($data)) as $line) {
                    if ($line !== '') $send(strip_tags($line));
                }
                if (connection_aborted()) { $process->stop(); break; }
            }

            $send($process->isSuccessful() ? '✓ Done!' : '✗ Failed: ' . $process->getExitCodeText());
            echo 'data: ' . json_encode(['done' => true]) . "\n\n";
            ob_flush(); flush();
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
