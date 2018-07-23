<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Profile
 *
 * @property int $id
 * @property int $user_id
 * @property int $is_draft
 * @property string|null $title
 * @property string|null $slug
 * @property string|null $body
 * @property string|null $cover
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\User $user
 */
class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'body'
    ];

    /**
     * Get the user that owns the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get created at in a human readable format.
     *
     * @return string
     */
    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->diffForHumans();
    }

    /**
     * Get updated at in a human readable format.
     *
     * @return string
     */
    public function getUpdatedAtAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])->diffForHumans();
    }

    /**
     * Sets the title and the readable slug.
     *
     * @param string $value
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;

        $this->setUniqueSlug($value);
    }

    /**
     * Sets the unique slug.
     *
     * @param $value
     */
    public function setUniqueSlug($value)
    {
        $slug = str_slug($value);
        if (static::whereSlug($slug)->exists()) {
            $slug = str_slug($value . '-' . str_random(5));
            $this->attributes['slug'] = $slug;
            return;
        }

        $this->attributes['slug'] = $slug;
    }

    /**
     * Scope a query to only include drafts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $is_draft
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByIsDraft($query, $is_draft)
    {
        return $query->where('is_draft', '=', $is_draft);
    }

    /**
     * Scope a query to only include post with the given slug.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $slug
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterBySlug($query, $slug = null)
    {
        if (!$slug) {
            return $query;
        }

        return $query->where('slug', '=', $slug);
    }

    /**
     * Scope a query to only include post with the given id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|null $id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterById($query, $id = null)
    {
        if (!$id) {
            return $query;
        }

        return $query->where('id', '=', $id);
    }

    /**
     * Scope a query to only include user posts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|null $user_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByUserId($query, $user_id = null)
    {
        if (!$user_id) {
            return $query;
        }

        return $query->where('user_id', '=', $user_id);
    }
}