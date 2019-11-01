<?php

namespace App\Repositories;

use App\Reaction;

/**
 * @package App\Repositories
 */
class ReactionRepository extends RepositoryBase
{
    /**
     * @param int $reactionId
     * @return Reaction|null
     */
    public function getReactionById(int $reactionId): ?Reaction
    {
        return Reaction::find($reactionId);
    }
}