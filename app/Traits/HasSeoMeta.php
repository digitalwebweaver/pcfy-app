<?php

namespace App\Traits;

use App\Models\SeoMeta;

trait HasSeoMeta
{
    public function seoMeta()
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    public function getSeoTitle(string $fallback = ''): string
    {
        return $this->seoMeta?->meta_title ?: ($this->title ?? $this->name ?? $fallback);
    }

    public function getSeoDescription(string $fallback = ''): string
    {
        return $this->seoMeta?->meta_description ?: ($this->excerpt ?? $this->description ?? $fallback);
    }

    public function getOgTitle(string $fallback = ''): string
    {
        return $this->seoMeta?->og_title ?: $this->getSeoTitle($fallback);
    }

    public function getOgImage(): string
    {
        if ($this->seoMeta?->og_image) return $this->seoMeta->og_image;
        if (method_exists($this, 'getFirstMediaUrl')) return $this->getFirstMediaUrl() ?: '';
        return '';
    }

    public function isNoindex(): bool
    {
        return (bool) ($this->seoMeta?->noindex ?? false);
    }

    public function saveSeoMeta(array $data): SeoMeta
    {
        return $this->seoMeta()->updateOrCreate(
            ['seoable_type' => get_class($this), 'seoable_id' => $this->id],
            array_filter($data, fn($v) => $v !== null)
        );
    }
}
