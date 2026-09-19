<?php

namespace App\Services;

use App\Models\ListingType;
use App\Models\NavigationItem;
use Illuminate\Support\Collection;

class NavigationMenuBuilder
{
    private Collection $itemsByParent;
    private Collection $listingTypes;

    public function build(): array
    {
        $items = NavigationItem::active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $this->itemsByParent = $items->groupBy(fn (NavigationItem $item) => $item->parent_id ?? 0);
        $this->listingTypes = $items->contains('kind', NavigationItem::KIND_LISTING_TYPES)
            ? ListingType::active()->orderBy('name')->get()
            : collect();

        return $this->buildLevel(null);
    }

    private function buildLevel(?int $parentId): array
    {
        return $this->itemsByParent
            ->get($parentId ?? 0, collect())
            ->map(fn (NavigationItem $item) => $this->buildItem($item))
            ->values()
            ->all();
    }

    private function buildItem(NavigationItem $item): array
    {
        $children = $this->buildLevel($item->id);

        if ($item->kind === NavigationItem::KIND_LISTING_TYPES) {
            $children = array_merge($children, $this->buildListingTypeItems());
        }

        $url = $item->kind === NavigationItem::KIND_LINK
            ? $this->resolveUrl($item->url)
            : '#';
        $active = $this->isActiveUrl($item->url)
            || ($item->kind === NavigationItem::KIND_LISTING_TYPES && request()->routeIs('public.offers.*'))
            || collect($children)->contains(fn (array $child) => !empty($child['active']));

        return [
            'id' => 'navigation-item-' . $item->id,
            'label' => $item->localized_name,
            'translate' => false,
            'url' => $url,
            'target' => $item->target_blank ? '_blank' : null,
            'rel' => $item->target_blank ? 'noopener noreferrer' : null,
            'active' => $active,
            'expanded' => $active && !empty($children),
            'children' => $children,
        ];
    }

    private function buildListingTypeItems(): array
    {
        $routeListingType = request()->route('listingType');
        $routeListingTypeId = $routeListingType instanceof ListingType
            ? $routeListingType->id
            : (int) $routeListingType;

        return $this->listingTypes->map(fn (ListingType $type) => [
            'id' => 'listing-type-' . $type->id . '-menu',
            'label' => $type->name,
            'translate' => false,
            'url' => route('public.offers.type', $type->id),
            'active' => request()->routeIs('public.offers.type') && $routeListingTypeId === $type->id,
        ])->all();
    }

    private function resolveUrl(?string $url): string
    {
        if (!$url) {
            return '#';
        }

        if ($this->isSecureExternalUrl($url)) {
            return $url;
        }

        return url('/' . ltrim($url, '/'));
    }

    private function isActiveUrl(?string $url): bool
    {
        if (!$url || $this->isSecureExternalUrl($url)) {
            return false;
        }

        $itemPath = trim(parse_url($url, PHP_URL_PATH) ?: '', '/');
        $currentPath = trim(request()->path(), '/');

        if ($itemPath === '') {
            return $currentPath === '';
        }

        return $currentPath === $itemPath || str_starts_with($currentPath, $itemPath . '/');
    }

    private function isSecureExternalUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL)
            && strtolower((string) parse_url($url, PHP_URL_SCHEME)) === 'https';
    }
}
