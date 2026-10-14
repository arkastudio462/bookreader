<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Bookmark;
use App\Models\Highlight;
use App\Models\ReadingPreference;
use App\Models\ReadingProgress;
use App\Services\BookFileService;
use App\Services\ReaderAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ReaderController extends Controller
{
    public function __construct(
        protected ReaderAccessService $access,
        protected BookFileService $files,
    ) {}

    /**
     * Halaman reader (PRD 7.5).
     */
    public function show(Book $book): Response|\Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $resolution = $this->access->resolve($user, $book);

        if ($resolution['mode'] === null) {
            return redirect()
                ->route('catalog.show', $book->slug)
                ->with('error', 'Anda belum berhak membaca buku ini. Silakan beli terlebih dahulu.');
        }

        $fileUrl = $book->s3_key ? $this->files->getTemporaryUrl($book->s3_key, 30) : null;

        if (!$fileUrl) {
            return redirect()
                ->route('catalog.show', $book->slug)
                ->with('error', 'File buku belum tersedia.');
        }

        $progress = ReadingProgress::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        return Inertia::render('Reader/Show', [
            'book' => [
                'id' => $book->id,
                'title' => $book->title,
                'author_name' => $book->author_name,
                'slug' => $book->slug,
                'type' => $this->files->fileType($book->s3_key),
                'file_url' => $fileUrl,
                'expires_in' => 1800,
            ],
            'mode' => $resolution['mode'],
            'progress' => $progress ? [
                'location' => $progress->location,
                'percent' => $progress->percent,
            ] : null,
            'bookmarks' => Bookmark::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->latest()
                ->get(['id', 'location', 'label', 'note']),
            'highlights' => Highlight::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->latest()
                ->get(['id', 'location', 'text', 'color', 'note']),
            'preferences' => ReadingPreference::forUser($user->id)
                ->only(['theme', 'font_size', 'font_family', 'line_height', 'margin']),
        ]);
    }

    /**
     * Simpan progres baca (dipanggil berkala dari reader).
     */
    public function updateProgress(Request $request, Book $book)
    {
        $user = Auth::user();

        if ($this->access->resolve($user, $book)['mode'] === null) {
            abort(403);
        }

        $data = $request->validate([
            'location' => 'nullable|string|max:500',
            'percent' => 'required|integer|min:0|max:100',
            'device' => 'nullable|string|max:100',
        ]);

        ReadingProgress::updateOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            [
                'location' => $data['location'] ?? null,
                'percent' => $data['percent'],
                'device' => $data['device'] ?? null,
                'last_read_at' => now(),
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function storeBookmark(Request $request, Book $book)
    {
        $user = Auth::user();

        if ($this->access->resolve($user, $book)['mode'] === null) {
            abort(403);
        }

        $data = $request->validate([
            'location' => 'required|string|max:500',
            'label' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:2000',
        ]);

        $bookmark = Bookmark::create($data + [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        return response()->json(['ok' => true, 'bookmark' => $bookmark]);
    }

    public function destroyBookmark(Book $book, Bookmark $bookmark)
    {
        abort_unless($bookmark->user_id === Auth::id() && $bookmark->book_id === $book->id, 403);

        $bookmark->delete();

        return response()->json(['ok' => true]);
    }

    public function storeHighlight(Request $request, Book $book)
    {
        $user = Auth::user();

        if ($this->access->resolve($user, $book)['mode'] === null) {
            abort(403);
        }

        $data = $request->validate([
            'location' => 'required|string|max:500',
            'text' => 'required|string|max:5000',
            'color' => 'nullable|string|max:20',
            'note' => 'nullable|string|max:2000',
        ]);

        $highlight = Highlight::create($data + [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'color' => $data['color'] ?? 'yellow',
        ]);

        return response()->json(['ok' => true, 'highlight' => $highlight]);
    }

    public function destroyHighlight(Book $book, Highlight $highlight)
    {
        abort_unless($highlight->user_id === Auth::id() && $highlight->book_id === $book->id, 403);

        $highlight->delete();

        return response()->json(['ok' => true]);
    }

    public function updatePreferences(Request $request)
    {
        $data = $request->validate([
            'theme' => 'required|in:light,sepia,dark',
            'font_size' => 'required|integer|min:12|max:28',
            'font_family' => 'required|in:serif,sans-serif',
            'line_height' => 'required|numeric|min:1.2|max:2.5',
            'margin' => 'required|in:narrow,normal,wide',
        ]);

        $pref = ReadingPreference::forUser(Auth::id());
        $pref->update($data);

        return response()->json(['ok' => true, 'preferences' => $pref]);
    }
}
