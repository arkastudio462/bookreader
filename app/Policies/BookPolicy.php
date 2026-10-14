<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Book $book): bool
    {
        return $book->status->isPublished() || $user->id === $book->publisher_id || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['author', 'publisher']);
    }

    public function update(User $user, Book $book): bool
    {
        return $user->id === $book->publisher_id && $user->hasRole(['author', 'publisher']);
    }

    public function delete(User $user, Book $book): bool
    {
        return $user->id === $book->publisher_id && $user->hasRole(['author', 'publisher']);
    }
}
