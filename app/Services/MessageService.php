<?php

namespace App\Services;

use App\Enums\ResponseMessageEnum;
use App\Exceptions\MessageException;
use App\Repositories\{MessageRepository, RepositoryBase, UserRepository};
use App\Models\{Message, User};
use Exception;
use Illuminate\Http\Request;

class MessageService extends RepositoryBase
{
    /**
     * @var MessageRepository
     */
    protected $messageRepository;

    /**
     * @param MessageRepository $messageRepository
     */
    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Message
     * @throws MessageException
     */
    public function storeMessage(Request $request, User $user): Message
    {
        $this->validateRequest($request);
        $fileName = $this->storeImage($request, $user);
        return $this->saveMessage($user->getAttribute('id'), $request->get('content'), $fileName);
    }

    /**
     * @param int $messageId
     * @param int $authenticatedUserId
     * @throws MessageException
     * @throws Exception
     */
    public function deleteMessage(int $messageId, int $authenticatedUserId): void
    {
        $message = $this->checkMessageExists($messageId);
        if ($message->getAttribute('user_id') !== $authenticatedUserId) {
            throw new MessageException('This is not your message');
        }
        $authenticatedUser = (new UserRepository())->getUserById($authenticatedUserId);
        if ($authenticatedUser !== null && $message->getAttribute('image') !== null) {
            unlink(
                public_path() .
                '/messageImages/' .
                $authenticatedUser->getAttribute('tag') .
                '/' .
                $message->getAttribute('image')
            );
        }
        $this->messageRepository->delete($message);
    }

    /**
     * @param Request $request
     * @throws MessageException
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

    /**
     * @param Request $request
     * @param User $user
     * @return string|void
     */
    private function storeImage(Request $request, User $user)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileOriginalName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('messageImages') . '/' . $user->getAttribute('tag') . '/', $fileOriginalName);
            return $fileOriginalName;
        }
    }

    /**
     * @param int $userId
     * @param string $messageContent
     * @param null $fileName
     * @return Message
     */
    private function saveMessage(int $userId, string $messageContent, $fileName = null): Message
    {
        $message = new Message();
        if ($fileName !== null) {
            $message->setAttribute('image', $fileName);
        }
        $message->setAttribute('user_id', $userId);
        $message->setAttribute('content', $messageContent);
        $message->save();
        return $message;
    }

    /**
     * @param int $messageId
     * @return Message
     * @throws MessageException
     */
    private function checkMessageExists(int $messageId): Message
    {
        $message = $this->messageRepository->findById($messageId);
        if ($message === null) {
            throw new MessageException(ResponseMessageEnum::NO_MESSAGE_FOUND);
        }
        return $message;
    }
}
