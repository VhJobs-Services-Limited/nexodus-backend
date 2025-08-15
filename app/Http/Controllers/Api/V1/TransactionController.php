<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\TransactionCollection;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

final class TransactionController
{
    /**
     * Display a listing of the user's transactions.
     */
    public function index(Request $request): JsonResponse
    {
        $transactions = QueryBuilder::for(Transaction::class)
            ->allowedFilters([
                AllowedFilter::exact('transaction_type'),
                AllowedFilter::exact('status'),
                AllowedFilter::scope('amount_range', 'amountRange'),
            ])
            ->allowedIncludes(['billTransaction', 'walletTransaction'])
            ->defaultSort('-created_at')
            ->whereBelongsTo($request->user())
            ->withLimit($request)
            ->withPagination($request);

        return $request->has('paginate') ? (new TransactionCollection($transactions))->additional(['message' => 'Transactions retrieved successfully'])->response() : TransactionResource::collection($transactions)->additional(['message' => 'Transactions retrieved successfully'])->response();
    }

    /**
     * Display the specified transaction.
     */
    public function show(Request $request, Transaction $transaction): JsonResponse
    {
        $user = $request->user();

        // Ensure the transaction belongs to the authenticated user
        if ($transaction->user_id !== $user->id) {
            abort(Response::HTTP_FORBIDDEN, 'You can only view your own transactions.');
        }

        $transaction->load('billTransaction');

        return response()->json([
            'message' => 'Transaction retrieved successfully',
            'data' => TransactionResource::make($transaction),
        ]);
    }
}
