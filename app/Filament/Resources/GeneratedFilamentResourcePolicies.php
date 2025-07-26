<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Illuminate\Support\Facades\Gate;

class GeneratedFilamentResourcePolicies
{
    public static function generatePolicyMethods(string $modelClass): array
    {
        return [
            'canViewAny' => "Gate::allows('view_any_' . strtolower(\$modelClass))",
            'canView' => "Gate::allows('view_' . strtolower(\$modelClass))",
            'canCreate' => "Gate::allows('create_' . strtolower(\$modelClass))",
            'canEdit' => "Gate::allows('update_' . strtolower(\$modelClass))",
            'canDelete' => "Gate::allows('delete_' . strtolower(\$modelClass))",
            'canDeleteAny' => "Gate::allows('delete_any_' . strtolower(\$modelClass))",
            'canForceDelete' => "Gate::allows('force_delete_' . strtolower(\$modelClass))",
            'canForceDeleteAny' => "Gate::allows('force_delete_any_' . strtolower(\$modelClass))",
            'canRestore' => "Gate::allows('restore_' . strtolower(\$modelClass))",
            'canRestoreAny' => "Gate::allows('restore_any_' . strtolower(\$modelClass))",
            'canReplicate' => "Gate::allows('replicate_' . strtolower(\$modelClass))",
            'canReorder' => "Gate::allows('reorder_' . strtolower(\$modelClass))",
        ];
    }
}
