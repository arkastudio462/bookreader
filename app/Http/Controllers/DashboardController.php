<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Book;
use App\Models\ReadingProgress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Render dashboard sesuai role user yang login (PRD: pisahkan dashboard per role).
     */
    public function index(): Response
    {
        $user = Auth::user();

        return match ($user->role) {
            UserRole::ADMIN => Inertia::render('Admin/Dashboard', [
                'stats' => $this->adminStats(),
            ]),
            UserRole::PUBLISHER => Inertia::render('Publisher/Dashboard', [
                'stats' => $this->publisherStats($user->id),
            ]),
            UserRole::AUTHOR => Inertia::render('Author/Dashboard', [
                'stats' => $this->authorStats($user->id),
            ]),
            default => Inertia::render('User/Dashboard', [
                'library' => $this->libraryFor($user),
            ]),
        };
    }

    /**
     * Buku yang bisa dibaca user (gratis, milik sendiri, atau sudah dibeli) — PRD 7.5.
     */
    protected function libraryFor(User $user): array
    {
        $purchased = collect();
        if (Schema::hasTable('orders') && Schema::hasTable('order_items')) {
            $purchased = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.user_id', $user->id)
                ->where('orders.status', 'paid')
                ->pluck('order_items.book_id');
        }

        $books = Book::published()
            ->where(function ($q) use ($user, $purchased) {
                $q->where('price', 0)->orWhere('publisher_id', $user->id);
                if ($purchased->isNotEmpty()) {
                    $q->orWhereIn('id', $purchased);
                }
            })
            ->limit(24)
            ->get();

        $progress = ReadingProgress::where('user_id', $user->id)
            ->whereIn('book_id', $books->pluck('id'))
            ->get()
            ->keyBy('book_id');

        return $books->map(fn ($b) => [
            'id' => $b->id,
            'title' => $b->title,
            'slug' => $b->slug,
            'author_name' => $b->author_name,
            'price' => (float) $b->price,
            'percent' => $progress[$b->id]->percent ?? 0,
            'location' => $progress[$b->id]->location ?? null,
            'last_read_at' => optional($progress[$b->id] ?? null)->last_read_at,
        ])->all();
    }

    protected function adminStats(): array
    {
        return [
            'total_books' => \App\Models\Book::count(),
            'pending_books' => \App\Models\Book::pending()->count(),
            'total_users' => User::query()->count(),
        ];
    }

    protected function publisherStats(int $userId): array
    {
        return [
            'total_books' => \App\Models\Book::where('publisher_id', $userId)->count(),
            'published_books' => \App\Models\Book::where('publisher_id', $userId)->published()->count(),
            'pending_books' => \App\Models\Book::where('publisher_id', $userId)->pending()->count(),
        ];
    }

    protected function authorStats(int $userId): array
    {
        return $this->publisherStats($userId);
    }
}
