<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class PublicProfileController extends Controller
{
    public function author(User $user): Response
    {
        if (!$user->hasRole('author')) {
            abort(404);
        }

        $profile = $user->authorProfile;
        $books = $user->books()->published()->latest('published_at')->get();

        return Inertia::render('Public/AuthorProfile', [
            'author' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'bio' => $profile?->bio,
                'website' => $profile?->website,
                'social_links' => $profile?->social_links,
            ],
            'books' => $books->map(fn($b) => [
                'id' => $b->id,
                'slug' => $b->slug,
                'title' => $b->title,
                'author_name' => $b->author_name,
                'price' => $b->price,
                'cover_path' => $b->cover_path,
                'cover_url' => $b->cover_url,
                'average_rating' => round($b->averageRating(), 1),
                'rating_count' => $b->ratingCount(),
            ]),
        ]);
    }

    public function publisher(User $user): Response
    {
        if (!$user->hasRole('publisher')) {
            abort(404);
        }

        $profile = $user->publisherProfile;
        $books = $user->books()->published()->latest('published_at')->get();

        return Inertia::render('Public/PublisherProfile', [
            'publisher' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'imprint_name' => $profile?->imprint_name,
                'legal_name' => $profile?->legal_name,
                'website' => $profile?->website,
                'address' => $profile?->address,
            ],
            'books' => $books->map(fn($b) => [
                'id' => $b->id,
                'slug' => $b->slug,
                'title' => $b->title,
                'author_name' => $b->author_name,
                'price' => $b->price,
                'cover_path' => $b->cover_path,
                'cover_url' => $b->cover_url,
                'average_rating' => round($b->averageRating(), 1),
                'rating_count' => $b->ratingCount(),
            ]),
        ]);
    }
}
