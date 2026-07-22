<?php

namespace Botble\Mailing\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Mailing\Services\UpdateService;

class UpdateController extends BaseController
{
    public function index(UpdateService $updateService)
    {
        $this->pageTitle(trans('plugins/mailing::mailing.update.title'));

        $info = $updateService->check();

        return view('plugins/mailing::update', compact('info'));
    }

    public function run(UpdateService $updateService)
    {
        $result = $updateService->update();

        if (! $result['success']) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextUrl(route('mailing.update'))
                ->setMessage($result['message']);
        }

        return $this
            ->httpResponse()
            ->setNextUrl(route('mailing.update'))
            ->setMessage($result['message']);
    }
}
