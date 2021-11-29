<?php

namespace App\Services;

use App\Enums\ResponseMessageEnum;
use App\Exceptions\MessageException;
use Illuminate\Contracts\Container\BindingResolutionException;
use App\Repositories\MessageRepository;
use App\Models\{Message, User};
use Exception;
use Illuminate\Http\Request;

class MessageService
{
    public function __construct(
        protected MessageRepository $messageRepository
    )
    {
    }

    /**
     * @throws MessageException
     * @throws BindingResolutionException
     */
    public function storeMessage(Request $request, User $user): Message
    {
        $this->validateRequest($request);
        $fileName = $this->storeImage($request, $user);
        return $this->saveMessage($user->id, $request->get('content'), $fileName);
    }

    /**
     * @throws MessageException
     * @throws Exception
     */
    public function deleteMessage(int $messageId, int $authenticatedUserId): void
    {
        $message = $this->checkMessageExists($messageId);
        if ((int)$message->user_id !== $authenticatedUserId) {
            throw new MessageException('This is not your message');
        }
        if ($message->image !== null) {
            unlink(public_path() . '/messageImages/' . auth('api')->user()->tag . '/' . $message->image);
        }
        $this->messageRepository->delete($message);
    }

    /**
     * @throws MessageException|BindingResolutionException
     */
    private function validateRequest(Request $request): void
    {
        $validator = validator()->make($request->all(), [
            'content' => 'required|string|max:500',
            'image' => 'sometimes|image|mimes:jpeg,jpg,png|max:2048',
        ]);
        if ($validator->fails()) {
            throw new MessageException($validator->getMessageBag()->first());
        }
    }

    private function storeImage(Request $request, User $user): ?string
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileOriginalName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path() . '/messageImages/' . $user->tag . '/', $fileOriginalName);
            return $fileOriginalName;
        }
        return null;
    }

    private function saveMessage(int $userId, string $messageContent, ?string $fileName = null): Message
    {
        return Message::create([
            'image' => $fileName,
            'user_id' => $userId,
            'content' => $messageContent,
        ]);
    }

    /**
     * @throws MessageException
     */
    private function checkMessageExists(int $messageId): Message
    {
        if (($message = $this->messageRepository->findById($messageId)) === null) {
            throw new MessageException(ResponseMessageEnum::NO_MESSAGE_FOUND);
        }
        return $message;
    }
}
