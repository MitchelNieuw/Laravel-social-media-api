<?php

namespace App\Http\Controllers;

use App\Exceptions\MessageException;
use App\Helpers\ErrorMessageHelper;
use App\Services\MessageService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $this->messageService->storeMessage($request, auth()->user()->getAuthIdentifier());
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
            $this->messageService->deleteMessage($id, auth()->user()->getAuthIdentifier());
            return back();
        } catch (MessageException $exception) {
            return back()->withErrors($exception->getMessage());
        } catch (Exception $exception) {
            return $this->errorMessageHelper->redirectErrorMessage($exception);
        }
    }
}
