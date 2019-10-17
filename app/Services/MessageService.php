<?php

namespace App\Services;

use App\Enums\RedirectMessageEnum;
use App\Exceptions\MessageException;
use App\Message;
use App\Notifications\UserNewMessageNotification;
use App\Repositories\MessageRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\RepositoryBase;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @package App\Services
 */
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
     * @param int $userId
     * @param string $messageContent
     * @return Message
     * @throws MessageException
     */
    public function storeMessage(Request $request, int $userId, string $messageContent): Message
    {
        $this->validateRequest($request);
        $message = $this->saveMessage($userId, $messageContent);
        $this->sendNotifications($userId);
        return $message;
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
        if ((int)$message->user_id !== $authenticatedUserId) {
            throw new MessageException('This is not your message');
        }
        $this->messageRepository->delete($message);
    }

    /**
     * @param Request $request
     * @throws MessageException
     */
    private function validateRequest(Request $request): void
    {
        $validator = validator($request->all(), [
            'content' => 'required|string|max:500',
        ]);
        if ($validator->fails()) {
            throw new MessageException($validator->getMessageBag()->first());
        }
    }

    /**
     * @param int $userId
     * @param string $messageContent
     * @return Message
     */
    private function saveMessage(int $userId, string $messageContent): Message
    {
        $message = new Message();
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
        if (($message = $this->messageRepository->findById($messageId)) !== null) {
            return $message;
        }
        throw new MessageException(RedirectMessageEnum::NO_MESSAGE_FOUND);
    }

    /**
     * @param int $authenticatedUserId
     */
    private function sendNotifications(int $authenticatedUserId): void
    {
        (new UserRepository())->getUsersByIds(
            $this->getUserIdsForNotifications($authenticatedUserId)
        );
    }

    /**
     * @param int $authenticatedUserId
     * @return array
     */
    private function getUserIdsForNotifications(int $authenticatedUserId): array
    {
        return $this->removeStatusAndAuthenticatedUserIdFromArray(
            (new NotificationRepository())->getUserIdsWhereNotificationsArePossible($authenticatedUserId),
            $authenticatedUserId
        );
    }
}
