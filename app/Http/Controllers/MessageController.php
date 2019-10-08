<?php

namespace App\Http\Controllers;

use App\Exceptions\MessageException;
use App\Helpers\ErrorMessageHelper;
use App\Services\MessageService;
use Exception;
use Illuminate\Http\RedirectResponse;

/**
 * @package App\Http\Controllers
 */
class MessageController extends Controller
{
    /**
     * @var MessageService
     */
    protected $messageService;

    /**
     * @var ErrorMessageHelper
     */
    protected $errorMessageHelper;

    /**
     * @param MessageService $messageService
     * @param ErrorMessageHelper $errorMessageHelper
     */
    public function __construct(MessageService $messageService, ErrorMessageHelper $errorMessageHelper)
    {
        $this->messageService = $messageService;
        $this->errorMessageHelper = $errorMessageHelper;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(): RedirectResponse
    {
        try {
            $this->messageService->storeMessage();
            return back();
        } catch (MessageException $exception) {
            return back()->withErrors($exception->getMessage());
        } catch (Exception $exception) {
            return $this->errorMessageHelper->redirectErrorMessage($exception);
        }
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function delete(int $id): RedirectResponse
    {
        try {
            $this->messageService->deleteMessage($id);
            return back();
        } catch (MessageException $exception) {
            return back()->withErrors($exception->getMessage());
        } catch (Exception $exception) {
            return $this->errorMessageHelper->redirectErrorMessage($exception);
        }
    }
}
