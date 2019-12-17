<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ErrorMessageHelper;
use App\Http\Resources\SearchUserResource;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @package App\Http\Controllers\Api
 */
class SearchController
{
    /**
     * @var ErrorMessageHelper
     */
    protected $errorMessageHelper;

    /**
     * @param ErrorMessageHelper $errorMessageHelper
     */
    public function __construct(ErrorMessageHelper $errorMessageHelper)
    {
        $this->errorMessageHelper = $errorMessageHelper;
    }

    /**
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function search()
    {
        try {
            $users = (new UserRepository())->searchForUsersInTagOrName(request()->get('tag'));
            return SearchUserResource::collection($users);
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}