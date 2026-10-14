<?php

namespace App\Http\Controllers;

use App\Enums\BookStatus;
use App\Jobs\NotifyWishlistDiscount;
use App\Models\Book;
use App\Models\Category;
use App\Models\Genre;
use App\Services\BookFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class BookUploadController extends Controller
{
    public function __construct(protected BookFileService $fileService) {}

    public function index(): Response
    {
        $books = Book::where('publisher_id', Auth::id())
            ->with('category', 'genre')
            ->latest('created_at')
            ->paginate(20);

        return Inertia::render('Author/Books/Index', [
            'books' => $books,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Author/Books/Create', [
            'categories' => Category::where('is_active', true)->get(),
            'genres' => Genre::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'author_name' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn',
            'category_id' => 'required|exists:categories,id',
            'genre_id' => 'nullable|exists:genres,id',
            'language' => 'required|in:id,en',
            'price' => 'required|numeric|min:0|max:999999.99',
            'sample_price' => 'nullable|numeric|min:0|max:999999.99',
            'pages' => 'nullable|integer|min:1',
            'cover' => 'required|image|max:5120',
            'file' => 'required|mimes:epub,pdf|max:102400',
        ]);

        try {
            // Upload file
            $coverPath = $this->fileService->uploadCover($request->file('cover'));
            $filePath = $this->fileService->uploadBookFile($request->file('file'));

            $book = Book::create([
                'publisher_id' => Auth::id(),
                'title' => $validated['title'],
                'slug' => str($validated['title'])->slug(),
                'description' => $validated['description'],
                'author_name' => $validated['author_name'],
                'isbn' => $validated['isbn'] ?? null,
                'category_id' => $validated['category_id'],
                'genre_id' => $validated['genre_id'] ?? null,
                'language' => $validated['language'],
                'price' => $validated['price'],
                'sample_price' => $validated['sample_price'] ?? 0,
                'pages' => $validated['pages'] ?? null,
                'cover_path' => $coverPath,
                's3_key' => $filePath,
                'status' => BookStatus::PENDING_REVIEW,
            ]);

            return redirect()->route('author.books.show', $book)->with('success', 'Buku berhasil diunggah. Menunggu moderasi...');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Gagal mengunggah: ' . $e->getMessage()]);
        }
    }

    public function show(Book $book): Response
    {
        $this->authorize('view', $book);

        return Inertia::render('Author/Books/Show', [
            'book' => $book->load('category', 'genre'),
        ]);
    }

    public function edit(Book $book): Response
    {
        $this->authorize('update', $book);

        return Inertia::render('Author/Books/Edit', [
            'book' => $book->load('category', 'genre'),
            'categories' => Category::where('is_active', true)->get(),
            'genres' => Genre::where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request, Book $book)
    {
        $this->authorize('update', $book);

        if (!in_array($book->status->value, BookStatus::editable())) {
            return back()->withErrors(['status' => 'Buku tidak bisa diedit (status tidak editable)']);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'author_name' => 'required|string|max:255',
            'isbn' => ['nullable', 'string', Rule::unique('books')->ignore($book->id)],
            'category_id' => 'required|exists:categories,id',
            'genre_id' => 'nullable|exists:genres,id',
            'language' => 'required|in:id,en',
            'price' => 'required|numeric|min:0|max:999999.99',
            'sample_price' => 'nullable|numeric|min:0|max:999999.99',
            'pages' => 'nullable|integer|min:1',
        ]);

        $book->update($validated + [
            'slug' => str($validated['title'])->slug(),
        ]);

        return redirect()->route('author.books.show', $book)->with('success', 'Buku berhasil diperbarui.');
    }

    /**
     * Atur diskon terjadwal per buku (FR-4.4.4 / FR-4.7.1).
     *
     * Terpisah dari update() karena diskon tetap boleh diubah walau buku
     * sudah published.
     */
    public function updateDiscount(Request $request, Book $book)
    {
        $this->authorize('update', $book);

        $validated = $request->validate([
            'discount_price' => 'nullable|numeric|min:0',
            'discount_starts_at' => 'nullable|date',
            'discount_ends_at' => 'nullable|date|after:discount_starts_at',
        ]);

        $price = (float) $book->price;
        $discountPrice = $validated['discount_price'] ?? null;
        $maxPercent = (float) config('perpuskita.promo.max_discount_percent', 80);

        if ($discountPrice !== null && $discountPrice !== '') {
            $discountPrice = (float) $discountPrice;

            if ($price > 0 && $discountPrice >= $price) {
                throw ValidationException::withMessages([
                    'discount_price' => 'Harga diskon harus lebih rendah dari harga jual.',
                ]);
            }

            if ($price > 0) {
                $percent = (1 - $discountPrice / $price) * 100;
                if ($percent > $maxPercent) {
                    throw ValidationException::withMessages([
                        'discount_price' => 'Diskon maksimal ' . $maxPercent . '%.',
                    ]);
                }
            }
        } else {
            $discountPrice = null;
        }

        $book->update([
            'discount_price' => $discountPrice,
            'discount_starts_at' => $discountPrice === null ? null : ($validated['discount_starts_at'] ?? null),
            'discount_ends_at' => $discountPrice === null ? null : ($validated['discount_ends_at'] ?? null),
        ]);

        // Beri tahu pemilik wishlist bila diskon sedang aktif (FR-4.8.2).
        if ($book->fresh()->hasActiveDiscount()) {
            NotifyWishlistDiscount::dispatch($book);
        }

        return back()->with('success', 'Diskon buku diperbarui.');
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        if ($book->status->value === BookStatus::PUBLISHED->value) {
            return back()->withErrors(['status' => 'Buku terbit tidak boleh dihapus']);
        }

        $this->fileService->delete($book->cover_path);
        $this->fileService->delete($book->s3_key);
        $book->delete();

        return redirect()->route('author.books.index')->with('success', 'Buku dihapus.');
    }
}
