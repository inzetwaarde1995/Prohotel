<?php

namespace App\Models\Articles;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;

/**
 * @property int $id
 * @property string $name
 * @property string $background_color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, WebsiteArticle> $websiteArticles
 * @property-read int|null $website_articles_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereBackgroundColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Tag extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    protected $guarded = [];

    /** @return MorphToMany<WebsiteArticle, $this> */
    public function websiteArticles(): MorphToMany
    {
        return $this->morphedByMany(WebsiteArticle::class, 'taggable');
    }

    /** @return MorphToMany<User, $this> */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'taggable');
    }

    /**
     * The most used user tags, shuffled, with a 1-4 weight for colouring.
     *
     * @return SupportCollection<int, array{name: string, weight: int}>
     */
    public static function popularForUsers(int $limit = 24): SupportCollection
    {
        $tags = static::query()
            ->whereHas('users')
            ->withCount('users')
            ->orderByDesc('users_count')
            ->take($limit)
            ->get(['id', 'name']);

        $max = max(1, (int) $tags->max('users_count'));

        return $tags
            ->map(fn (self $tag): array => [
                'name' => $tag->name,
                'weight' => (int) ceil(($tag->users_count / $max) * 4),
            ])
            ->shuffle()
            ->values();
    }
}
