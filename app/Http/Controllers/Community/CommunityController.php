<?php

namespace App\Http\Controllers\Community;

use App\Emulator\Contracts\CurrencyRepository;
use App\Emulator\Contracts\PlayerStatsRepository;
use App\Emulator\Data\LeaderboardEntry;
use App\Emulator\Data\Stat;
use App\Enums\CurrencyTypes;
use App\Http\Controllers\Controller;
use App\Models\Articles\WebsiteArticle;
use App\Models\Room;
use App\Services\Community\StaffService;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CommunityController extends Controller
{
    private const LEADERBOARD_SIZE = 5;

    public function __construct(
        private readonly StaffService $staffService,
        private readonly CurrencyRepository $currencies,
        private readonly PlayerStatsRepository $stats,
    ) {}

    public function __invoke(): View
    {
        abort_unless(view()->exists('community.index'), 404);

        $staffIds = $this->staffService->fetchEmployeeIds();

        return view('community.index', [
            'popularRooms' => rescue(fn (): Collection => Room::query()->popular()->get(), collect(), report: false),
            'featuredArticle' => WebsiteArticle::query()
                ->whereHas('user')
                ->with('user:id,username,look')
                ->latest('id')
                ->first(),
            'leaderboards' => [
                'credits' => [
                    'label' => __('Credits'),
                    'entries' => $this->entries(fn () => $this->currencies->topBy(CurrencyTypes::Credits, self::LEADERBOARD_SIZE, $staffIds)),
                ],
                'diamonds' => [
                    'label' => __('Diamonds'),
                    'entries' => $this->entries(fn () => $this->currencies->topBy(CurrencyTypes::Diamonds, self::LEADERBOARD_SIZE, $staffIds)),
                ],
                'duckets' => [
                    'label' => __('Duckets'),
                    'entries' => $this->entries(fn () => $this->currencies->topBy(CurrencyTypes::Duckets, self::LEADERBOARD_SIZE, $staffIds)),
                ],
                'achievements' => [
                    'label' => __('Achievement points'),
                    'entries' => $this->entries(fn () => $this->stats->topBy(Stat::AchievementScore, self::LEADERBOARD_SIZE, $staffIds)),
                ],
            ],
        ]);
    }

    /**
     * @param  callable(): Collection<int, LeaderboardEntry>  $query
     * @return Collection<int, array{user: \App\Models\User, amount: int}>
     */
    private function entries(callable $query): Collection
    {
        return rescue(fn (): Collection => collect($query())
            ->map(fn (LeaderboardEntry $entry): array => ['user' => $entry->user, 'amount' => $entry->value])
            ->values(), collect(), report: false);
    }
}
