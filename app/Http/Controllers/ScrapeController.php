<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class ScrapeController extends Controller
{
    private const PASSWORD = 'myhomemap2024';

    public function page()
    {
        return view('scrape');
    }

    public function auth(Request $request)
    {
        if ($request->input('password') !== self::PASSWORD) {
            return back()->with('error', 'Wrong password.');
        }
        session(['scrape_auth' => true]);
        return redirect()->route('scrape.page');
    }

    public function run(Request $request)
    {
        if (! session('scrape_auth')) {
            abort(403);
        }

        $pages = max(1, min(30, (int) $request->input('pages', 30)));

        return response()->stream(function () use ($pages) {
            $send = function (string $line) {
                echo 'data: ' . json_encode(['line' => $line]) . "\n\n";
                ob_flush(); flush();
            };

            $send("Starting scrape — {$pages} pages...");

            $process = new Process([
                PHP_BINARY, base_path('artisan'), 'scrape:myhome', "--pages={$pages}",
            ]);
            $process->setTimeout(600);
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
