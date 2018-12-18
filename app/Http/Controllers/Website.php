<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core as MinepicCore;
use App\Database\AccountsStats;
use App\Helpers\Date as DateHelper;
use App\Misc\SplashMessage;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Website extends BaseController
{
    /**
     * Default title.
     *
     * @var string
     */
    private static $pageTitle = 'Minecraft avatar generator - Minepic';

    /**
     * Default description.
     *
     * @var string
     */
    private static $pageDescription = 'MinePic is a free Minecraft avatar and skin viewer '.
    'that allow users and developers to pick them for their projects';

    /**
     * Default keywords.
     *
     * @var string
     */
    private static $pageKeywords = 'Minecraft, Minecraft avatar viewer, pic, minepic avatar viewer, skin, '.
    'minecraft skin, avatar, minecraft avatar, generator, skin generator, skin viewer';

    /**
     * HTTP Response code.
     *
     * @var int
     */
    private static $httpCode = 200;

    /**
     * Render fullpage (headers, body, footer).
     *
     * @param string $page
     * @param array  $bodyData
     * @param array  $headerData
     *
     * @return Response
     */
    private static function renderPage(
        string $page = '',
        array $bodyData = [],
        array $headerData = []
    ): Response {
        $realHeaderData = [];
        $realHeaderData['title'] = (
            isset($headerData['title']) ? $headerData['title'] : self::$pageTitle
        );
        $realHeaderData['description'] = (
            isset($headerData['description']) ? $headerData['description'] : self::$pageDescription
        );
        $realHeaderData['keywords'] = (
            isset($headerData['keywords']) ? $headerData['keywords'] : self::$pageKeywords
        );
        $realHeaderData['randomMessage'] = SplashMessage::get();

        return Response::create(
            view('public.template.header', $realHeaderData).
            view('public.'.$page, $bodyData).
            view('public.template.footer'),
            self::$httpCode
        );
    }

    /**
     * Index.
     *
     * @return Response
     */
    public function index(): Response
    {
        $bodyData = [
            'lastRequests' => AccountsStats::getLastUsers(),
            'mostWanted' => AccountsStats::getMostWanted(),
        ];

        return self::renderPage('index', $bodyData);
    }

    /**
     * User stats page.
     *
     * @param string $uuidOrName
     *
     * @return Response
     */
    public function user(string $uuidOrName): Response
    {
        $minepicCore = new MinepicCore();

        if ($minepicCore->initialize($uuidOrName)) {
            list($userdata, $userstats) = $minepicCore->getFullUserdata();

            $headerData = [
                'title' => $userdata->username.' usage statistics - Minepic',
                'description' => 'MinePic usage statistics for the user '.$userdata->username,
                'keywords' => 'Minecraft, Minecraft avatar viewer, pic, minepic avatar viewer, skin, '.
                    'minecraft skin, avatar, minecraft avatar, generator, skin generator, skin viewer',
            ];

            $bodyData = [
                'user' => [
                    'uuid' => $userdata->uuid,
                    'username' => $userdata->username,
                    'count_request' => $userstats->count_request,
                    'count_search' => $userstats->count_search,
                    'last_request' => DateHelper::humanizeTimestamp($userstats->time_request),
                    'last_search' => DateHelper::humanizeTimestamp($userstats->time_search),
                ],
            ];

            return self::renderPage('user', $bodyData, $headerData);
        } else {
            throw new NotFoundHttpException();
        }
    }
}
