<?php

namespace App\Http\Controllers\Articles;

use App\Http\Controllers\Controller;
use App\Http\Requests\ToggleReactionFormRequest;
use App\Models\Articles\WebsiteArticle;
use App\Models\User;
use App\Services\Articles\ArticleService;
use App\Services\Articles\ReactionService;
use App\Support\AuthenticatedUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articlesService,
        private readonly ReactionService $reactionService,
    ) {}

    public function index(Request $request): View
    {
        $search = is_string($request->query('search')) ? $request->query('search') : '';
        $search = str($search)->squish()->limit(50, '')->toString();

        return view('community.articles', [
            'articles' => $this->articlesService->getArticles(true, search: $search),
            'search' => $search,
        ]);
    }

    public function show(WebsiteArticle $article): View
    {
        $user = Auth::user();

        return view('community.article', [
            'article' => $this->articlesService->loadForDisplay($article),
            'otherArticles' => $this->articlesService->otherArticles($article),
            'myReactions' => $user instanceof User ? $this->reactionService->reactionsFor($article, $user) : collect(),
            'articleReactions' => $this->reactionService->countsFor($article),
        ]);
    }

    public function toggleReaction(WebsiteArticle $article, ToggleReactionFormRequest $request): JsonResponse
    {
        $response = $this->reactionService->toggleReaction(
            $article,
            AuthenticatedUser::from($request),
            $request->validated('reaction'),
        );

        return response()->json($response);
    }
}
