<?php

namespace App\View\Components;

use App\Models\Game\Furniture\ItemBase;
use App\Services\FurnitureDataService;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;

class NewestFurniture extends Component
{
    /**
     * @var Collection<int, ItemBase>
     */
    public Collection $items;

    public function __construct(FurnitureDataService $furnitureData, public int $amount = 6)
    {
        $this->items = rescue(fn (): Collection => ItemBase::query()
            ->select(['id', 'public_name', 'item_name'])
            ->latest('id')
            ->take($this->amount)
            ->get()
            ->each(function (ItemBase $item) use ($furnitureData): void {
                $item->display_name = $furnitureData->name($item->item_name)
                    ?: ($item->public_name ?: $item->item_name);
            }), collect(), report: false);
    }

    public function shouldRender(): bool
    {
        return $this->items->isNotEmpty();
    }

    public function render(): View
    {
        return view('components.newest-furniture');
    }
}
