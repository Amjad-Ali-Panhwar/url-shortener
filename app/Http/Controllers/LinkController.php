<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str; // For generating random strings
use App\Models\Link;        // Our Link model

class LinkController extends Controller
{
    /**
     * Show the form to create a new short link.
     */
    public function index()
    {
        return view('welcome'); // For now, we'll use the default welcome view
    }

    /**
     * Store a new short link.
     */
    public function store(Request $request)
    {
        // 1. Validate the incoming request
        $request->validate([
            'url' => 'required|url', // 'url' is the name of our input field
        ]);

        $originalUrl = $request->input('url');

        // 2. Check if the URL already exists to avoid duplicates
        $existingLink = Link::where('original_url', $originalUrl)->first();

        if ($existingLink) {
            return redirect('/')->with('short_code', $existingLink->short_code);
        }

        // 3. Generate a unique short code
        $shortCode = $this->generateUniqueShortCode();

        // 4. Save the new link
        $link = Link::create([
            'original_url' => $originalUrl,
            'short_code' => $shortCode,
        ]);

        // 5. Redirect back with the short code
        return redirect('/')->with('short_code', $link->short_code);
    }

    /**
     * Redirect to the original URL.
     */
    public function show($shortCode)
    {
        $link = Link::where('short_code', $shortCode)->firstOrFail(); // firstOrFail will throw a 404 if not found

        // Increment click count here if you wanted to add that later.

        return redirect($link->original_url);
    }

    /**
     * Generate a unique short code.
     * We'll make this private as it's an internal helper.
     */
    private function generateUniqueShortCode()
    {
        $length = 6; // You can adjust the length of the short code
        do {
            $code = Str::random($length);
        } while (Link::where('short_code', $code)->exists()); // Ensure uniqueness

        return $code;
    }
}