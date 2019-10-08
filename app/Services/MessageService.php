<?php

namespace App\Services;

use App\Enums\RedirectErrorMessageEnum;
use App\Exceptions\MessageException;
use App\Message;
use App\Repositories\MessageRepository;
use Exception;

/**
 * @package App\Services
 */
class MessageService
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
     */
    private function saveMessage(): void
    {
        $message = new Message();
        $message->setAttribute('user_id', auth()->user()->id);
        $message->setAttribute('content', request()->get('content'));
        $message->save();
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
        throw new MessageException(RedirectErrorMessageEnum::NO_MESSAGE_FOUND);
    }
}
