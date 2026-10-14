<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LibraryController extends Controller
{
    public function index()
    {
        $books = Auth::user()->libraryBooks()->get()->map(function (Book $book) {
            $book->average_rating = round($book->averageRating(), 1);

            return $book;
        });

        return Inertia::render('Library/Index', [
            'books' => $books,
        ]);
    }
}
