<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'points_balance', 'avatar', 'bank_name', 'bank_account_no', 'bank_holder'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'points_balance' => 'integer',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isPublisher(): bool
    {
        return $this->role === UserRole::PUBLISHER;
    }

    public function isAuthor(): bool
    {
        return in_array($this->role, [UserRole::AUTHOR, UserRole::PUBLISHER]);
    }

    public function canManageBooks(): bool
    {
        return in_array($this->role, [UserRole::AUTHOR, UserRole::PUBLISHER, UserRole::ADMIN]);
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        $current = $this->role->value;

        foreach ($roles as $role) {
            if ($current === $role) {
                return true;
            }
        }

        return false;
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'publisher_id');
    }

    public function authorProfile(): HasOne
    {
        return $this->hasOne(AuthorProfile::class);
    }

    public function publisherProfile(): HasOne
    {
        return $this->hasOne(PublisherProfile::class);
    }

    public function readingProgress(): HasMany
    {
        return $this->hasMany(ReadingProgress::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(Highlight::class);
    }

    public function readingPreference(): HasOne
    {
        return $this->hasOne(ReadingPreference::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    /**
     * Buku yang sudah dimiliki (order paid) — Pustaka (PRD 4.6.6).
     */
    public function libraryBooks()
    {
        return Book::query()
            ->select('books.*')
            ->join('order_items', 'order_items.book_id', '=', 'books.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $this->id)
            ->where('orders.status', 'paid')
            ->with('category')
            ->orderByDesc('order_items.id')
            ->distinct();
    }

    public function ownsBook(int $bookId): bool
    {
        return \DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $this->id)
            ->where('orders.status', 'paid')
            ->where('order_items.book_id', $bookId)
            ->exists();
    }
}
