<?php

namespace App\Http\Controllers;

use Illuminate\Console\OutputStyle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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

        set_time_limit(600);

        Artisan::call('scrape:myhome', ['--pages' => $pages]);
        $output = Artisan::output();

        return back()->with('result', $output ?: '✓ Done!');
    }
}
