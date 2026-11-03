<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserTagFormRequest;
use App\Models\Articles\Tag;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(view()->exists('community.tags'), 404);

        $search = $this->normalize(is_string($request->query('tag')) ? $request->query('tag') : '');

        return view('community.tags', [
            'popularTags' => Tag::popularForUsers(),
            'search' => $search,
            'users' => $search === '' ? null : User::query()
                ->select(['id', 'username', 'look', 'motto', 'online'])
                ->whereHas('tags', fn ($query) => $query->where('name', $search))
                ->with('tags:id,name')
                ->orderByDesc('online')
                ->orderBy('username')
                ->paginate(15)
                ->withQueryString(),
            'myTags' => $request->user()?->tags()->orderBy('name')->get(['tags.id', 'name']) ?? collect(),
        ]);
    }

    public function store(UserTagFormRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->tags()->count() >= UserTagFormRequest::MAX_TAGS) {
            return back()->withErrors(['tag' => __('You can have a maximum of :max tags.', ['max' => UserTagFormRequest::MAX_TAGS])], 'tags');
        }

        $tag = Tag::query()->firstOrCreate(
            ['name' => $request->validated('tag')],
            ['background_color' => '#bfdcf5'],
        );

        $user->tags()->syncWithoutDetaching([$tag->id]);

        return back();
    }

    public function destroy(Request $request, Tag $tag): RedirectResponse
    {
        $request->user()->tags()->detach($tag->id);

        return back();
    }

    private function normalize(string $value): string
    {
        return mb_substr(mb_strtolower(trim($value)), 0, 20);
    }
}
