<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core as MinepicCore;
use App\Misc\SplashMessage;
use App\Models\AccountStats;
use App\Resolvers\UsernameResolver;
use App\Transformers\Account\AccountBasicDataTransformer;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;
use Laravel\Lumen\Routing\Controller as BaseController;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class WebsiteController.
 */
class WebsiteController extends BaseController
{
    /**
     * Default title.
     *
     * @var string
     */
    private const DEFAULT_PAGE_TITLE = 'Minecraft avatar generator - Minepic';

    /**
     * Default description.
     *
     * @var string
     */
    private const DEFAULT_PAGE_DESCRIPTION = 'MinePic is a free Minecraft avatar and skin viewer '.
    'that allow users and developers to pick them for their projects';

    /**
     * Default keywords.
     *
     * @var string
     */
    private const DEFAULT_PAGE_KEYWORDS = 'Minecraft, Minecraft avatar viewer, pic, minepic avatar viewer, skin, '.
    'minecraft skin, avatar, minecraft avatar, generator, skin generator, skin viewer';

    /**
     * @var ResponseFactory
     */
    private ResponseFactory $responseFactory;
    /**
     * @var MinepicCore
     */
    private MinepicCore $minepicCore;
    /**
     * @var UsernameResolver
     */
    private UsernameResolver $usernameResolver;
    /**
     * @var Manager
     */
    private Manager $dataManager;

    /**
     * WebsiteController constructor.
     *
     * @param MinepicCore      $minepicCore
     * @param ResponseFactory  $responseFactory
     * @param UsernameResolver $usernameResolver
     * @param Manager          $dataManager
     */
    public function __construct(
        MinepicCore $minepicCore,
        ResponseFactory $responseFactory,
        UsernameResolver $usernameResolver,
        Manager $dataManager
    ) {
        $this->responseFactory = $responseFactory;
        $this->minepicCore = $minepicCore;
        $this->usernameResolver = $usernameResolver;
        $this->dataManager = $dataManager;
        $this->dataManager->setSerializer(new ArraySerializer());
    }

    /**
     * Compose view with header and footer.
     *
     * @param string $page
     * @param array  $bodyData
     * @param array  $headerData
     *
     * @return string
     */
    private function composeView(
        string $page = '',
        array $bodyData = [],
        array $headerData = []
    ): string {
        return view('public.template.header', $headerData).
            view('public.'.$page, $bodyData).
            view('public.template.footer');
    }

    /**
     * Render fullpage (headers, body, footer).
     *
     * @param string $page
     * @param array  $bodyData
     * @param array  $headerData
     *
     * @return Response
     */
    private function renderPage(
        string $page = '',
        array $bodyData = [],
        array $headerData = []
    ): Response {
        $realHeaderData = [];
        $realHeaderData['title'] = $headerData['title'] ?? self::DEFAULT_PAGE_TITLE;
        $realHeaderData['description'] = $headerData['description'] ?? self::DEFAULT_PAGE_DESCRIPTION;
        $realHeaderData['keywords'] = $headerData['keywords'] ?? self::DEFAULT_PAGE_KEYWORDS;
        $realHeaderData['randomMessage'] = SplashMessage::get();

        $view = $this->composeView($page, $bodyData, $realHeaderData);

        return $this->responseFactory->make(
            $view,
            Response::HTTP_OK
        );
    }

    /**
     * Index.
     */
    public function index(): Response
    {
        $bodyData = [
            'lastRequests' => AccountStats::getLastUsers(),
            'mostWanted' => AccountStats::getMostWanted(),
        ];

        return $this->renderPage('index', $bodyData);
    }

    /**
     * User stats page.
     *
     * @param string $uuid
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function user(string $uuid): Response
    {
        if ($this->minepicCore->initialize($uuid)) {
            $account = $this->minepicCore->getUserdata();

            $headerData = [
                'title' => $account->username.' usage statistics - Minepic',
                'description' => 'MinePic usage statistics for the user '.$account->username,
                'keywords' => 'Minecraft, Minecraft avatar viewer, pic, minepic avatar viewer, skin, '.
                    'minecraft skin, avatar, minecraft avatar, generator, skin generator, skin viewer',
            ];

            $accountResource = new Fractal\Resource\Item($account, new AccountBasicDataTransformer());
            $bodyData = [
                'user' => $this->dataManager->createData($accountResource)->toArray(),
            ];

            return $this->renderPage('user', $bodyData, $headerData);
        }

        throw new NotFoundHttpException();
    }

    /**
     * @param string $username
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function userWithUsername(string $username): Response
    {
        $uuid = $this->usernameResolver->resolve($username);
        if ($uuid === env('DEFAULT_UUID')) {
            throw new NotFoundHttpException();
        }

        return $this->user($uuid);
    }
}
