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
     * Generate a unique short code with a custom alphabet and collision handling.
     */
    private function generateUniqueShortCode(int $initialLength = 6, int $maxAttempts = 5)
    {
        // Define a custom alphabet, excluding easily confused characters (l, I, 1, o, O, 0)
        // and making it URL-safe. Base62 without '0', 'O', 'I', 'l' etc.
        $alphabet = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $base = strlen($alphabet);
        $currentLength = $initialLength;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $code = '';
            for ($i = 0; $i < $currentLength; $i++) {
                $code .= $alphabet[random_int(0, $base - 1)];
            }

            // Check if the generated code already exists
            if (!Link::where('short_code', $code)->exists()) {
                return $code; // Found a unique code!
            }

            // If collision, increment length for next attempt after a few tries
            if ($attempt === $maxAttempts - 1) { // Only increment on the last collision attempt
                $currentLength++; // Increase length to reduce collision probability
            }
            // Fallback: If after max attempts and length increment, still no unique code (highly unlikely)
            // Consider throwing an exception or having a more robust fallback
            // For now, let's try one last time with an increased length
            $code = '';
            for ($i = 0; $i < ($initialLength + $maxAttempts); $i++) { // Try with significantly increased length
                $code .= $alphabet[random_int(0, $base - 1)];
            }
            if (!Link::where('short_code', $code)->exists()) {
                return $code;
            }

            // If it somehow still fails, this is an extreme edge case
            throw new \Exception('Could not generate a unique short code after multiple attempts.');
        }
    }
}