<?php

namespace App\Http\Controllers;

use App\Core as MinepicCore;
use App\Database\Accounts;
use App\Helpers\Date as DateHelper;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;

class Json extends BaseController
{
    /**
     * Send response to the user
     *
     * @param $response
     * @param $httpStatus
     * @return Response
     */
    private static function sendResponse($response, $httpStatus) : Response
    {
        return Response::create($response, $httpStatus, ['Content-Type' =>'application-json']);
    }

    /**
     * User info
     *
     * @param string $uuidOrName
     * @return Response
     */
    public function user($uuidOrName = '') : Response
    {
        $minepicCore = new MinepicCore();
        if ($minepicCore->initialize($uuidOrName)) {
            $httpStatus = 200;
            list($userdata, $userstats) = $minepicCore->getFullUserdata();

            $response = [
                'ok' => true,
                'userdata' => [
                    'uuid'          => $userdata->uuid,
                    'username'      => $userdata->username,
                    'count_request' => $userstats->count_request,
                    'count_search'  => $userstats->count_search,
                    'last_request'  => DateHelper::humanizeTimestamp($userstats->time_request),
                    'last_search'   => DateHelper::humanizeTimestamp($userstats->time_search),
                ]
            ];
        } else {
            $httpStatus = 404;
            $response = [
                'ok' => false,
                'message' => 'User not found'
            ];
        }

        return self::sendResponse($response, $httpStatus);
    }

    /**
     * Username Typeahead
     *
     * @param $term
     * @return Response
     */
    public function userTypeahead($term) : Response{
        $response = [];
        $accounts = Accounts::select('username')->where('username', 'LIKE', $term.'%')->take(15)->get();
        foreach ($accounts as $account) {
            $response[]['value'] = $account->username;
        }
        return self::sendResponse($response, 200);
    }

}