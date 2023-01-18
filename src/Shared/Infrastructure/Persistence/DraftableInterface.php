<?php

namespace Bejao\Shared\Infrastructure\Persistence;

use Illuminate\Database\Query\Builder;

/**
 * @method static Builder query()
 */
interface DraftableInterface
{

    /**
     * @param array<string,mixed> $data
     * @param int|null $draftOwner
     * @return void
     */
    public function storeDraft(array $data, ?int $draftOwner = null);

    /**
     * @return array<string,mixed>
     */
    public function getDraft(): array;

    /**
     * @return void
     */
    public function closeDraft(): void;

    /**
     * @return bool
     */
    public function hasDraft(): bool;

    public function getOwnerId(): int;
}
