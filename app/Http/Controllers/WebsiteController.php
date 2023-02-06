<?php

declare(strict_types=1);

namespace Minepic\Http\Controllers;

use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;
use Laravel\Lumen\Routing\Controller as BaseController;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use Minepic\Misc\SplashMessage;
use Minepic\Models\AccountStats;
use Minepic\Resolvers\UsernameResolver;
use Minepic\Resolvers\UuidResolver;
use Minepic\Transformers\Account\AccountBasicDataTransformer;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WebsiteController extends BaseController
{
    /**
     * Default title.
     *
     * @var string
     */
    private const DEFAULT_PAGE_TITLE = 'Minecraft avatar generator - MinePic';

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
    'minecraft skin, avatar, minecraft avatar, generator, skin generator, skin viewer, minecraft pfp generator';

    public function __construct(
        private readonly UuidResolver $uuidResolver,
        private readonly ResponseFactory $responseFactory,
        private readonly UsernameResolver $usernameResolver,
        private readonly Manager $dataManager
    ) {
        $this->dataManager->setSerializer(new ArraySerializer());
    }

    public function index(): Response
    {
        return $this->renderPage(
            'index',
            [
                'lastRequests' => AccountStats::getLastUsers(),
                'mostWanted' => AccountStats::getMostWanted(),
            ]
        );
    }

    /**
     * User stats page.
     */
    public function user(string $uuid): Response
    {
        if ($this->uuidResolver->resolve($uuid)) {
            $account = $this->uuidResolver->getAccount();

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
     * @throws \Throwable
     */
    public function userWithUsername(string $username): Response
    {
        $uuid = $this->usernameResolver->resolve($username);
        if ($uuid === null) {
            throw new NotFoundHttpException();
        }

        return $this->user($uuid);
    }

    /**
     * Compose view with header and footer.
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
     * Render full page (headers, body, footer).
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
            SymfonyResponse::HTTP_OK
        );
    }
}
