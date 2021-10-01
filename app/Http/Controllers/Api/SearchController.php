<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ErrorMessageHelper;
use App\Http\Resources\SearchUserResource;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\{JsonResponse, Request, Resources\Json\AnonymousResourceCollection};

class SearchController
{
    public function __construct(
        public ErrorMessageHelper $errorMessageHelper
    )
    {
    }

    public function search(Request $request): JsonResponse|AnonymousResourceCollection
    {
        try {
            return SearchUserResource::collection(
                (new UserRepository())->searchForUsersInTagOrName(
                    $request->get('tag')
                )
            );
        } catch (Exception $exception) {
            return $this->errorMessageHelper->jsonErrorMessage($exception);
        }
    }
}
