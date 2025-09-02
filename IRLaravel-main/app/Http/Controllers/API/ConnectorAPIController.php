<?php

namespace App\Http\Controllers\API;

use App\Repositories\SettingConnectorRepository;
use App\Repositories\OrderRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Http\Requests\API\ConnectorAuthAPIRequest;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ConnectorAPIController
 * @package App\Http\Controllers\API
 */
class ConnectorAPIController extends AppBaseController
{
    /** @var  SettingConnectorRepository */
    private $settingConnectorRepository;
    /**
     * @var OrderRepository $orderRepository
     */
    protected $orderRepository;
    protected $categoryRepository;
    protected $productRepository;

    /**
     * SettingConnectorController constructor.
     * @param SettingConnectorRepository $settingConnectorRepository
     */
    public function __construct(
        SettingConnectorRepository $settingConnectorRepository,
        OrderRepository $orderRepository,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository
    ) {
        $this->enableStateless = true;

        parent::__construct();

        $this->settingConnectorRepository = $settingConnectorRepository;
        $this->orderRepository = $orderRepository;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    public function auth(ConnectorAuthAPIRequest $request, int $connectorId)
    {
        $clientId = $request->get('client_id', null);
        $refreshToken = $request->get('refresh_token', null);
        $grantType = $request->get('grant_type', null);

        if(!empty($clientId) && !empty($refreshToken) && $grantType == 'refresh_token') {
            $key = InMemory::plainText(trim($clientId));
            $config = Configuration::forSymmetricSigner(new Sha256(), $key);
            assert($config instanceof Configuration);
            $refreshTokenParse = $config->parser()->parse($refreshToken);
            $refreshTokenClaims = $refreshTokenParse->claims();

            if($refreshTokenClaims->has('exp') && $refreshTokenClaims->get('exp')->getTimeStamp() < strtotime(now())) {
                return $this->sendError('Refresh token expired', 401);
            }

            if(!$refreshTokenClaims->has('secret')) {
                return $this->sendError('Refresh token invalid', 401);
            }

            $connector = $this->settingConnectorRepository->makeModel()
                ->where('id', $connectorId)
                ->where('provider', \App\Models\SettingConnector::PROVIDER_CUSTOM)
                ->where('key', trim($clientId))
                ->where('refresh_token', trim($refreshToken))
                ->first();

            if(!empty($connector)) {
                $secretKey = $connector->key;
                $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($secretKey));
                $now = new \DateTimeImmutable();
                $tokenObj = $config->builder()
                    ->expiresAt($now->modify('+'. config('jwt.connector.ttl') .' minutes'))
                    ->withClaim('secret', hash('sha512', $secretKey))
                    ->getToken($config->signer(), $config->signingKey());
                $refreshTokenObj = $config->builder()
                    ->expiresAt($now->modify('+'. config('jwt.connector.refresh_ttl') .' minutes'))
                    ->withClaim('secret', hash('sha512', $secretKey))
                    ->getToken($config->signer(), $config->signingKey());
                $accessToken = $tokenObj->toString();
                $refreshToken = $refreshTokenObj->toString();
                $expires = $tokenObj->claims()->get('exp')->getTimeStamp();
                $connector->token = $accessToken;
                $connector->refresh_token = $refreshToken;
                $connector->save();

                return $this->sendResponse([
                    'token' => [
                        'access_token' => $accessToken,
                        'expires' => $expires,
                        'refresh_token' => $refreshToken,
                    ]
                ], 'Token has been refreshed successfully.');
            }
        }

        return $this->sendError('Information is invalid. Please try again.');
    }

    public function orders(Request $request, int $connectorId) {
        try {
            $this->orderRepository->pushCriteria(new RequestCriteria($request));
            $this->orderRepository->pushCriteria(new LimitOffsetCriteria($request));
            $perPage = (int)$request->get('per_page', 10);
            $limit = min(250,(int)$request->get('limit', $perPage));
            $orders = $this->orderRepository->connectorOrderList(
                $limit,
                $request->get('workspaceId', null),
                $request->get('date_updated', $request->get('dateUpdated', null)),
                $request->get('inOrderList', null),
                $request->get('date_ordered', $request->get('dateOrdered', null)),
                $request->get('date_ordered_from', $request->get('dateOrderedFrom', null))
            );

            if($request->get('version',1) == 1) {
                $orders = $orders->items();
            }

            return $this->sendResponse($orders, 'Orders are retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function categories(Request $request, int $connectorId) {
        try {
            $this->categoryRepository->pushCriteria(new RequestCriteria($request));
            $this->categoryRepository->pushCriteria(new LimitOffsetCriteria($request));
            $perPage = (int)$request->get('per_page', 10);
            $limit = min(1000,(int)$request->get('limit', $perPage));
            $categories = $this->categoryRepository->connectorCategoryList(
                $limit,
                $request->get('workspaceId', null),
                $request->get('term', null),
                $request->get('updatedSince', null)
            );

            return $this->sendResponse($categories, trans('category.message_retrieved_list_successfully'));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function products(Request $request, int $connectorId) {
        // Quickfix: We still need to investigate why this is needed;
        // - Example restaurant: Boulevard Moelingen page 5 has an output if 24MB.
        ini_set('memory_limit', '512M');

        try {
            $this->productRepository->pushCriteria(new RequestCriteria($request));
            $this->productRepository->pushCriteria(new LimitOffsetCriteria($request));
            $perPage = (int)$request->get('per_page', 10);
            $limit = min(1000,(int)$request->get('limit', $perPage));
            $products = $this->productRepository->connectorProductList(
                $limit,
                $request->get('workspaceId', null),
                $request->get('term', null),
                $request->get('updatedSince', null)
            );

            return $this->sendResponse($products, trans('product.message_retrieved_list_successfully'));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
