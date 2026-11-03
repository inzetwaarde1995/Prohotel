<?php

namespace App\Http\Controllers\Community\Staff;

use App\Http\Controllers\Controller;
use App\Models\Community\Staff\WebsiteOpenPosition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VacancyController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless(view()->exists('community.vacancies'), 404);

        $user = $request->user();

        $vacancies = WebsiteOpenPosition::query()
            ->where(function ($query): void {
                $query->where(fn ($query) => $query->where('position_kind', 'rank')->whereHas('permission'))
                    ->orWhere(fn ($query) => $query->where('position_kind', 'team')->whereHas('team'));
            })
            ->canApply()
            ->with(['permission', 'team'])
            ->latest()
            ->get()
            ->map(fn (WebsiteOpenPosition $position): array => $this->present($position, $user));

        return view('community.vacancies', [
            'vacancies' => $vacancies,
        ]);
    }

    /**
     * @return array{id: int, kind: string, title: string, subtitle: ?string, badge: ?string, description: ?string, deadline: ?string, applied: bool, action: string}
     */
    private function present(WebsiteOpenPosition $position, ?User $user): array
    {
        $isTeam = $position->position_kind === 'team';
        $group = $isTeam ? $position->team : $position->permission;

        return [
            'id' => $position->id,
            'kind' => $isTeam ? 'team' : 'rank',
            'title' => $group->rank_name,
            'subtitle' => $group->job_description,
            'badge' => $group->badge ? sprintf('%s/%s.gif', rtrim((string) setting('badges_path'), '/'), $group->badge) : null,
            'description' => $position->description,
            'deadline' => $position->apply_to?->translatedFormat('j F Y, H:i'),
            'applied' => match (true) {
                $user === null => false,
                $isTeam => $user->hasAppliedForTeam((int) $position->team_id),
                default => $user->hasAppliedForPosition((int) $position->permission_id),
            },
            'action' => $isTeam ? route('team-applications.store', $position) : route('staff-applications.store', $position),
        ];
    }
}
