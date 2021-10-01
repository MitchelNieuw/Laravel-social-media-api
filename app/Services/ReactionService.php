<?php

namespace App\Services;

use App\Enums\ResponseMessageEnum;
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
        $fileName = $this->storeImage($request, $user);
        return $this->saveReaction($user->id, $messageId, $request->get('content'), $fileName);
    }

    /**
     * @param User $user
     * @param int $messageId
     * @param int $reactionId
     * @return string
     * @throws MessageException
     * @throws ReactionException
     */
    public function deleteReaction(User $user, int $messageId, int $reactionId): string
    {
        $reaction = $this->checkReactionExists($reactionId);
        $this->checkMessageExists($messageId);
        $this->checkUserIsOwnerOfReaction($reaction, $user);
        if ($reaction->getAttribute('image') !== null) {
            unlink(
                public_path('') . '/reactions/' . $user->getAttribute('tag') . '/' . $reaction->getAttribute('image')
            );
        }
        $reaction->delete();
        return ResponseMessageEnum::REACTION_DELETED_SUCCESSFUL;
    }

    /**
     * @param int $reactionId
     * @return Reaction
     * @throws ReactionException
     */
    private function checkReactionExists(int $reactionId): Reaction
    {
        if (($reaction = (new ReactionRepository())->getReactionById($reactionId)) === null) {
            throw new ReactionException('Reaction with this id doesnt exist');
        }
        return $reaction;
    }

    /**
     * @param Reaction $reaction
     * @param User $user
     * @throws ReactionException
     */
    private function checkUserIsOwnerOfReaction(Reaction $reaction, User $user): void
    {
        if ($reaction->getAttribute('user_id') !== $user->getAttribute('id')) {
            throw new ReactionException('You are not the owner of this reaction');
        }
    }


    /**
     * @param Request $request
     * @throws ReactionException
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
     * @param int $messageId
     * @throws MessageException
     */
    private function checkMessageExists(int $messageId): void
    {
        if ((new MessageRepository())->findById($messageId) === null) {
            throw new MessageException('Message with this id doesnt exist');
        }
    }

    /**
     * @param Request $request
     * @param User $user
     * @return string|null
     */
    private function storeImage(Request $request, User $user): ?string
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileOriginalName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('reactions') . '/' . $user->getAttribute('tag') . '/', $fileOriginalName);
            return $fileOriginalName;
        }
        return null;
    }

    /**
     * @param int $userId
     * @param int $messageId
     * @param string $reactionContent
     * @param string|null $fileName
     * @return Reaction
     */
    private function saveReaction(
        int $userId,
        int $messageId,
        string $reactionContent,
        ?string $fileName = null
    ): Reaction {
        return Reaction::create([
            'user_id' => $userId,
            'message_id' => $messageId,
            'content' => $reactionContent,
            'image' => $fileName,
        ]);
    }
}
