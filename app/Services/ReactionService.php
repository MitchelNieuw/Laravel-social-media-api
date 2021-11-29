<?php

namespace App\Services;

use App\Enums\ResponseMessageEnum;
use Illuminate\Contracts\Container\BindingResolutionException;
use App\Exceptions\{MessageException, ReactionException};
use App\Repositories\{MessageRepository, ReactionRepository};
use App\Models\{Reaction, User};
use Illuminate\Http\Request;

class ReactionService
{
    /**
     * @throws MessageException
     * @throws ReactionException
     */
    public function storeReaction(Request $request, User $user, int $messageId): Reaction
    {
        $this->validateRequest($request);
        $this->checkMessageExists($messageId);
        return $this->saveReaction(
            $user->id,
            $messageId,
            $request->get('content'),
            $this->storeImage($request, $user)
        );
    }

    /**
     * @throws MessageException
     * @throws ReactionException
     */
    public function deleteReaction(User $user, int $messageId, int $reactionId): string
    {
        $reaction = $this->checkReactionExists($reactionId);
        $this->checkMessageExists($messageId);
        $this->checkUserIsOwnerOfReaction($reaction, $user);
        if ($reaction->image !== null) {
            $publicPath = public_path();
            unlink("$publicPath/reactions/$user->tag/$reaction->image");
        }
        $reaction->delete();
        return ResponseMessageEnum::REACTION_DELETED_SUCCESSFUL;
    }

    /**
     * @throws ReactionException
     */
    private function checkReactionExists(int $reactionId): Reaction
    {
        if (($reaction = (new ReactionRepository)->getReactionById($reactionId)) === null) {
            throw new ReactionException('Reaction not found!');
        }
        return $reaction;
    }

    /**
     * @throws ReactionException
     */
    private function checkUserIsOwnerOfReaction(Reaction $reaction, User $user): void
    {
        if ((int)$reaction->user_id !== (int)$user->id) {
            throw new ReactionException('You are not the owner of this reaction!');
        }
    }


    /**
     * @param Request $request
     * @throws ReactionException
     * @throws BindingResolutionException
     */
    private function validateRequest(Request $request): void
    {
        $validator = validator()->make($request->all(), [
            'content' => 'required|string|max:500',
            'image' => 'sometimes|image|mimes:jpeg,jpg,png|max:2048',
        ]);
        if ($validator->fails()) {
            throw new ReactionException($validator->getMessageBag()->first());
        }
    }

    /**
     * @throws MessageException
     */
    private function checkMessageExists(int $messageId): void
    {
        if ((new MessageRepository)->findById($messageId) === null) {
            throw new MessageException('Message not found!');
        }
    }

    private function storeImage(Request $request, User $user): ?string
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $time = time();
            $fileOriginalName = "{$time}_{$file->getClientOriginalName()}";
            $publicPath = public_path();
            $file->move("$publicPath/reactions/$user->tag/$fileOriginalName");
            return $fileOriginalName;
        }
        return null;
    }

    private function saveReaction(
        int $userId,
        int $messageId,
        string $reactionContent,
        ?string $fileName = null
    ): Reaction
    {
        return Reaction::create([
            'user_id' => $userId,
            'message_id' => $messageId,
            'content' => $reactionContent,
            'image' => $fileName,
        ]);
    }
}
