<?php

return [
    // Record not found
    'record_not_found' => 'Record with ID :id not found',

    // Find operations
    'find_failed' => 'Failed to find record: :error',

    // Filter operations
    'invalid_filter_format' => 'Invalid filter format',
    'filter_must_be_array' => 'Filter must be an array',
    'invalid_relation_field_format' => 'Invalid relation field format. Use: relation.column',

    // Sorting operations
    'invalid_order_direction' => 'Order direction must be asc or desc',
    'invalid_sort_column' => 'Invalid sort column: :column',

    // CRUD operations
    'create_failed' => 'Failed to create model: :error',
    'update_failed' => 'Failed to update model: :error',
    'delete_failed' => 'Failed to delete model: :error',

    // Batch operations
    'batch_create_failed' => 'Failed to create multiple records: :error',
    'batch_update_failed' => 'Failed to update multiple records: :error',
    'batch_delete_failed' => 'Failed to delete multiple records: :error',

    // Soft delete operations
    'force_delete_failed' => 'Failed to force delete model: :error',
    'restore_failed' => 'Failed to restore model: :error',

    // Utility operations
    'update_or_create_failed' => 'Failed to update or create model: :error',
    'exists_check_failed' => 'Failed to check existence: :error',
    'count_failed' => 'Failed to count records: :error',
];
