<?php

namespace App\Models\Help;

use App\Models\Concerns\TranslatesSeededContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $category_id
 * @property string $paragraph
 * @property string $rule
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read WebsiteRuleCategory|null $category
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule whereParagraph($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule whereRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WebsiteRule whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class WebsiteRule extends Model
{
    use TranslatesSeededContent;

    protected $guarded = [];

    /** @return BelongsTo<WebsiteRuleCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(WebsiteRuleCategory::class, 'category_id');
    }

    protected function seededTranslationGroup(): string
    {
        return 'rules';
    }

    /**
     * Rules are keyed by their seeded English text.
     *
     * @param  array<string, mixed>  $translations
     */
    protected function seededTranslation(array $translations, string $attribute): mixed
    {
        return $attribute === 'rule' ? ($translations[trim($this->rule)] ?? null) : null;
    }
}
