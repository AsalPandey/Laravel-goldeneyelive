<?php

namespace Database\Seeders\Concerns;

use LogicException;

trait PreventsProductionBaselineSeeding
{
    protected function preventProductionBaselineSeeding(): void
    {
        if (app()->isProduction()) {
            throw new LogicException(
                'Baseline content seeding is disabled in production until the guarded recovery workflow is available.',
            );
        }
    }
}
