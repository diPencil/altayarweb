<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class NavigationItemController extends Controller
{
    public function index()
    {
        $pageTitle = __('Website Menu');
        $items = NavigationItem::orderBy('sort_order')->orderBy('id')->get();
        $orderedItems = $this->flattenItems($items);
        $parentOptions = $orderedItems->filter(fn (array $row) => $row['depth'] < 2);

        return view('admin.navigation.index', compact('pageTitle', 'orderedItems', 'parentOptions'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        NavigationItem::create($data);

        return back()->withNotify([['success', __('Menu item added successfully')]]);
    }

    public function update(Request $request, NavigationItem $navigationItem)
    {
        $data = $this->validatedData($request, $navigationItem);
        $navigationItem->update($data);

        return back()->withNotify([['success', __('Menu item updated successfully')]]);
    }

    public function status(NavigationItem $navigationItem)
    {
        $navigationItem->update(['status' => !$navigationItem->status]);

        return back()->withNotify([['success', __('Status changed successfully')]]);
    }

    public function destroy(NavigationItem $navigationItem)
    {
        $navigationItem->delete();

        return back()->withNotify([['success', __('Menu item deleted successfully')]]);
    }

    private function validatedData(Request $request, ?NavigationItem $navigationItem = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'name_ar' => 'nullable|string|max:120',
            'kind' => ['required', Rule::in([
                NavigationItem::KIND_LINK,
                NavigationItem::KIND_GROUP,
                NavigationItem::KIND_LISTING_TYPES,
            ])],
            'url' => ['nullable', 'string', 'max:500', function ($attribute, $value, $fail) use ($request) {
                if ($request->kind !== NavigationItem::KIND_LINK) {
                    return;
                }

                if (!$value || (!$this->isInternalPath($value) && !$this->isSecureExternalUrl($value))) {
                    $fail(__('Enter a valid internal path or an absolute URL.'));
                }
            }],
            'parent_id' => 'nullable|exists:navigation_items,id',
            'sort_order' => 'required|integer|min:0|max:9999',
            'target_blank' => 'nullable|boolean',
            'status' => 'nullable|boolean',
        ]);

        $parentId = isset($data['parent_id']) ? (int) $data['parent_id'] : null;
        if ($navigationItem && $parentId === $navigationItem->id) {
            throw ValidationException::withMessages(['parent_id' => __('An item cannot be its own parent.')]);
        }

        if ($parentId) {
            $parent = NavigationItem::findOrFail($parentId);
            $ancestorIds = $this->ancestorIds($parent);

            if ($navigationItem && in_array($navigationItem->id, $ancestorIds, true)) {
                throw ValidationException::withMessages(['parent_id' => __('An item cannot be placed inside one of its children.')]);
            }

            if (count($ancestorIds) >= 2) {
                throw ValidationException::withMessages(['parent_id' => __('The website menu supports a maximum of three levels.')]);
            }
        }

        $data['parent_id'] = $parentId;
        $data['url'] = $data['kind'] === NavigationItem::KIND_LINK ? trim((string) $data['url']) : null;
        $data['target_blank'] = $data['kind'] === NavigationItem::KIND_LINK && $request->boolean('target_blank');
        $data['status'] = $request->boolean('status');

        return $data;
    }

    private function isInternalPath(string $url): bool
    {
        return str_starts_with($url, '/') && !str_starts_with($url, '//');
    }

    private function isSecureExternalUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL)
            && strtolower((string) parse_url($url, PHP_URL_SCHEME)) === 'https';
    }

    private function ancestorIds(NavigationItem $item): array
    {
        $ids = [];
        while ($item->parent_id) {
            $ids[] = $item->parent_id;
            $item = NavigationItem::findOrFail($item->parent_id);
        }

        return $ids;
    }

    private function flattenItems($items, ?int $parentId = null, int $depth = 0)
    {
        return $items
            ->where('parent_id', $parentId)
            ->flatMap(function (NavigationItem $item) use ($items, $depth) {
                return collect([['item' => $item, 'depth' => $depth]])
                    ->concat($this->flattenItems($items, $item->id, $depth + 1));
            })
            ->values();
    }
}
