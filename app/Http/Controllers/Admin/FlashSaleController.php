<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FlashSaleStatus;
use App\Http\Controllers\Controller;
use App\Jobs\NotifyWishlistDiscount;
use App\Models\Book;
use App\Models\FlashSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Kelola kampanye Flash Sale (FR-4.7.8 / FR-4.13.5).
 */
class FlashSaleController extends Controller
{
    public function index(): Response
    {
        $campaigns = FlashSale::with(['items.book'])
            ->orderByDesc('starts_at')
            ->get()
            ->map(function (FlashSale $campaign) {
                $campaign->items->each(function ($item) {
                    $item->sold_percent = $item->soldPercent();
                    $item->remaining_stock = $item->remainingStock();
                });

                return $campaign;
            });

        return Inertia::render('Admin/FlashSale/Index', [
            'campaigns' => $campaigns,
            'books' => Book::published()
                ->select('id', 'title', 'price')
                ->orderBy('title')
                ->get(),
            'maxDiscountPercent' => (float) config('perpuskita.promo.max_discount_percent', 80),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateCampaign($request);

        $campaign = DB::transaction(function () use ($validated) {
            $campaign = FlashSale::create([
                'name' => $validated['name'],
                'banner_text' => $validated['banner_text'] ?? null,
                'starts_at' => $validated['starts_at'],
                'ends_at' => $validated['ends_at'],
                'status' => $this->initialStatus($validated['starts_at'], $validated['ends_at']),
            ]);

            $this->syncItems($campaign, $validated['items']);

            return $campaign;
        });

        $this->notifyIfRunning($campaign);

        return back()->with('success', 'Kampanye flash sale dibuat.');
    }

    public function update(Request $request, FlashSale $flashSale)
    {
        $validated = $this->validateCampaign($request);

        DB::transaction(function () use ($validated, $flashSale) {
            $flashSale->update([
                'name' => $validated['name'],
                'banner_text' => $validated['banner_text'] ?? null,
                'starts_at' => $validated['starts_at'],
                'ends_at' => $validated['ends_at'],
                'status' => $this->initialStatus($validated['starts_at'], $validated['ends_at']),
            ]);

            $this->syncItems($flashSale, $validated['items']);
        });

        $this->notifyIfRunning($flashSale->fresh());

        return back()->with('success', 'Kampanye flash sale diperbarui.');
    }

    public function activate(FlashSale $flashSale)
    {
        $flashSale->update(['status' => FlashSaleStatus::ACTIVE]);

        $this->notifyIfRunning($flashSale->fresh());

        return back()->with('success', 'Kampanye diaktifkan.');
    }

    public function end(FlashSale $flashSale)
    {
        $flashSale->update(['status' => FlashSaleStatus::ENDED]);

        return back()->with('success', 'Kampanye diakhiri. Harga kembali normal.');
    }

    public function destroy(FlashSale $flashSale)
    {
        $flashSale->delete();

        return back()->with('success', 'Kampanye dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateCampaign(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'banner_text' => 'nullable|string|max:500',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'items' => 'required|array|min:1',
            'items.*.book_id' => 'required|exists:books,id',
            'items.*.sale_price' => 'required|numeric|min:0',
            'items.*.stock_limit' => 'nullable|integer|min:1',
        ]);

        $maxPercent = (float) config('perpuskita.promo.max_discount_percent', 80);
        $books = Book::whereIn('id', collect($validated['items'])->pluck('book_id'))->get()->keyBy('id');

        foreach ($validated['items'] as $index => $item) {
            $book = $books->get($item['book_id']);
            $price = (float) $book->price;
            $sale = (float) $item['sale_price'];

            if ($price > 0 && $sale >= $price) {
                throw ValidationException::withMessages([
                    "items.{$index}.sale_price" => "Harga sale harus lebih rendah dari harga buku \"{$book->title}\".",
                ]);
            }

            if ($price > 0) {
                $percent = (1 - $sale / $price) * 100;
                if ($percent > $maxPercent) {
                    throw ValidationException::withMessages([
                        "items.{$index}.sale_price" => 'Diskon maksimal ' . $maxPercent . '%.',
                    ]);
                }
            }
        }

        return $validated;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function syncItems(FlashSale $campaign, array $items): void
    {
        $campaign->items()->delete();

        foreach ($items as $item) {
            $campaign->items()->create([
                'book_id' => $item['book_id'],
                'sale_price' => $item['sale_price'],
                'stock_limit' => $item['stock_limit'] ?? null,
            ]);
        }
    }

    private function initialStatus(string $startsAt, string $endsAt): FlashSaleStatus
    {
        $start = \Illuminate\Support\Carbon::parse($startsAt);
        $end = \Illuminate\Support\Carbon::parse($endsAt);

        if ($end->isPast()) {
            return FlashSaleStatus::ENDED;
        }

        return $start->isFuture() ? FlashSaleStatus::DRAFT : FlashSaleStatus::ACTIVE;
    }

    private function notifyIfRunning(?FlashSale $campaign): void
    {
        if ($campaign === null || ! $campaign->isRunning()) {
            return;
        }

        $campaign->loadMissing('items.book');

        foreach ($campaign->items as $item) {
            if ($item->book !== null) {
                NotifyWishlistDiscount::dispatch($item->book);
            }
        }
    }
}
