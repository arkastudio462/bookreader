<?php

namespace App\Services;

use App\Enums\PointTransactionType;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Poin reward non-tunai (PRD 4.7.d). Semua mutasi tercatat di point_transactions.
 */
class PointService
{
    public function earnPercent(): float
    {
        return (float) config('perpuskita.points.earn_percent', 1.0);
    }

    public function redeemRate(): float
    {
        return max(0.0001, (float) config('perpuskita.points.redeem_rate', 1.0));
    }

    public function maxRedeemPercent(): float
    {
        return (float) config('perpuskita.points.max_redeem_percent', 50.0);
    }

    public function pointsToValue(int $points): float
    {
        return round($points * $this->redeemRate(), 2);
    }

    public function valueToPoints(float $idr): int
    {
        return (int) floor($idr / $this->redeemRate());
    }

    public function pointsEarnedFor(float $total): int
    {
        return (int) floor($total * $this->earnPercent() / 100);
    }

    /**
     * Poin maksimum yang boleh dipotongkan untuk sebuah nilai belanja.
     */
    public function maxRedeemable(User $user, float $base): int
    {
        $capPoints = $this->valueToPoints($base * $this->maxRedeemPercent() / 100);

        return max(0, min((int) $user->points_balance, $capPoints));
    }

    public function earn(User $user, int $points, ?string $source = null, ?string $ref = null, ?string $note = null): void
    {
        if ($points <= 0) {
            return;
        }
        $this->apply($user, $points, PointTransactionType::EARN, $source, $ref, $note);
    }

    public function spend(User $user, int $points, ?string $source = null, ?string $ref = null, ?string $note = null): void
    {
        if ($points <= 0) {
            return;
        }
        if ((int) $user->points_balance < $points) {
            throw new RuntimeException('Saldo poin tidak mencukupi.');
        }
        $this->apply($user, -$points, PointTransactionType::SPEND, $source, $ref, $note);
    }

    public function revert(User $user, int $points, ?string $source = null, ?string $ref = null, ?string $note = null): void
    {
        if ($points <= 0) {
            return;
        }
        $this->apply($user, $points, PointTransactionType::REVERT, $source, $ref, $note);
    }

    protected function apply(User $user, int $delta, PointTransactionType $type, ?string $source, ?string $ref, ?string $note): void
    {
        DB::transaction(function () use ($user, $delta, $type, $source, $ref, $note) {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $newBalance = max(0, (int) $locked->points_balance + $delta);
            $locked->points_balance = $newBalance;
            $locked->save();

            $user->points_balance = $newBalance;

            PointTransaction::create([
                'user_id' => $locked->id,
                'type' => $type,
                'points' => $delta,
                'balance_after' => $newBalance,
                'source' => $source,
                'reference_id' => $ref,
                'note' => $note,
            ]);
        });
    }
}
