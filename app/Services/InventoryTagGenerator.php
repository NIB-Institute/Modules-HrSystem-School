<?php

namespace Modules\School\Services;

use App\Services\CodeGeneratorService;
use Modules\School\Models\Inventory;

/**
 * Generates unique asset tags for Inventory items.
 *
 * Thin domain wrapper around the generic CodeGeneratorService: applies the
 * inventory-specific uniqueness scope (asset_tag column, soft-deleted
 * rows included) and the project's standard 3-letter / 4-digit format.
 */
class InventoryTagGenerator
{
    public function __construct(private CodeGeneratorService $generator) {}

    public function generate(?string $source = null): string
    {
        return $this->generator->generate(
            $source,
            fn (string $tag) => Inventory::withTrashed()
                ->where('asset_tag', $tag)
                ->exists(),
        );
    }
}
