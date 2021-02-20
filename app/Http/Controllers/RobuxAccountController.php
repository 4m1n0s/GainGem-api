<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRobuxAccountRequest;
use App\Http\Requests\StoreRobuxAccountRequest;
use App\Models\RobuxAccount;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Robux;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class RobuxAccountController extends Controller
{
    public function index(IndexRobuxAccountRequest $request): JsonResponse
    {
        $payload = $request->validated();

        if (! isset($payload['user_id'])) {
            /** @var Collection $robuxAccounts */
            $robuxAccounts = RobuxAccount::withTotalWithdrawn()->orderByDesc('id')->paginate(10);

            $robuxAccountsArr = $robuxAccounts->append(['formatted_total_withdrawn', 'formatted_disabled_at']);

            $robuxAccountsArr->map(static function (RobuxAccount $robuxAccount) {
                $robuxAccount->cookie = Str::limit($robuxAccount->cookie, 200);
            });

            $pagination = $robuxAccounts->toArray();
            unset($pagination['data']);

            return response()->json([
                'accounts' => $robuxAccountsArr,
                'pagination' => $pagination,
            ]);
        }

        /** @var User $supplier */
        $supplier = User::find($payload['user_id']);
        $this->authorize('update', $supplier);

        /** @var Collection $robuxAccounts */
        $robuxAccounts = $supplier->robuxAccounts()->select(['id', 'supplier_user_id', 'robux_account_id', 'robux_account_username', 'robux_amount', 'disabled_at'])
            ->orderByDesc('id')
            ->paginate(10);

        $monthlyRobuxEarnings = Transaction::whereSupplierWithTrashed($supplier)
            ->selectRaw('DATE(`created_at`) as date, SUM(`robux_amount`) as total_robux_amount')
            ->groupBy('date')
            ->orderBy('date')
            ->whereBetween('created_at', [now()->startOfMonth()->startOfDay(), now()->endOfMonth()->endOfDay()])
            ->get()
            ->each(static function (Transaction $transaction) { /** @phpstan-ignore-line */
                $date = Carbon::parse($transaction['date']);
                $transaction['formatted_date'] = $date->copy()->format('M d');
                $transaction['date'] = $date->day;
            });

        $totalRobuxEarnings = Transaction::whereSupplierWithTrashed($supplier)->sum('robux_amount');

        $robuxAccountsArr = $robuxAccounts->append('formatted_disabled_at');

        $pagination = $robuxAccounts->toArray();
        unset($pagination['data']);

        return response()->json([
            'accounts' => $robuxAccountsArr,
            'pagination' => $pagination,
            'monthly_robux_earnings' => $monthlyRobuxEarnings,
            'total_robux_earnings' => currency_format((float) $totalRobuxEarnings),
        ]);
    }

    public function store(StoreRobuxAccountRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $robuxUser = Robux::getUserById($payload['robux_account_id']);
        $robuxAccountCurrencyResponse = Robux::getCurrencyResponse([
            'robux_account_id' => $payload['robux_account_id'],
            'cookie' => $payload['cookie'],
        ]);

        abort_if($robuxAccountCurrencyResponse->failed(), 422, isset($robuxAccountCurrencyResponse['errors']) ? $robuxAccountCurrencyResponse['errors'][0]['message'] : 'Invalid cookie or wrong account id!');

        $robuxAccountCurrency = $robuxAccountCurrencyResponse['robux'];

        abort_if($robuxAccountCurrency < RobuxAccount::MIN_ROBUX_AMOUNT, 422, 'Account must have at least '.RobuxAccount::MIN_ROBUX_AMOUNT.' robux.');

        /** @var User $user */
        $user = auth()->user();

        $robuxAccount = $user->robuxAccounts()->create([
            'cookie' => $payload['cookie'],
            'robux_account_id' => $robuxUser['Id'],
            'robux_account_username' => $robuxUser['Username'],
            'robux_amount' => $robuxAccountCurrency,
        ]);

        return response()->json($robuxAccount->makeHidden('cookie'));
    }

    public function destroy(RobuxAccount $robuxAccount): void
    {
        $this->authorize('update', auth()->user());

        $robuxAccount->delete();
    }
}
