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
use Illuminate\Support\Facades\Notification;

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
     * @throws MessageException
     */
    public function storeMessage(): void
    {
        $this->validateRequest();
        $this->saveMessage();
        $this->sendNotifications(auth()->user()->getAuthIdentifier());
    }

    /**
     * @param int $messageId
     * @throws MessageException|Exception
     */
    public function deleteMessage(int $messageId): void
    {
        $message = $this->checkMessageExists($messageId);
        $this->messageRepository->delete($message);
    }

    /**
     * @throws MessageException
     */
    private function validateRequest(): void
    {
        $validator = validator(request()->all(), [
            'content' => 'required|string|max:500',
        ]);
        if ($validator->fails()) {
            throw new MessageException($validator->getMessageBag()->first());
        }
    }

    /**
     * @return Message
     */
    private function saveMessage(): Message
    {
        $message = new Message();
        $message->setAttribute('user_id', auth()->user()->id);
        $message->setAttribute('content', request()->get('content'));
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
