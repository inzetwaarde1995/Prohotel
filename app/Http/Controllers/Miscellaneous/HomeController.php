<?php

namespace App\Http\Controllers\Miscellaneous;

use App\Http\Controllers\Controller;
use App\Models\Articles\Tag;
use App\Models\Room;
use App\Services\Articles\ArticleService;
use App\Services\Community\CameraService;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('index', [
            'articles' => app(ArticleService::class)->latestWithAuthor(4),
            'photos' => app(CameraService::class)->latestPhotos(),
            'popularRooms' => $this->popularRooms(),
            'popularTags' => rescue(fn (): Collection => Tag::popularForUsers(20), collect(), report: false),
        ]);
    }

    /**
     * @return Collection<int, Room>
     */
    private function popularRooms(): Collection
    {
        return rescue(fn (): Collection => Room::query()->popular()->get(), collect(), report: false);
    }
}
