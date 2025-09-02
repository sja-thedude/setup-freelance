<?php

namespace App\Excel\Import;

use App\Helpers\Helper;
use App\Models\Category;
use App\Models\Media;
use App\Models\OptionItem;
use App\Models\OptionItemReference;
use App\Models\Product;
use App\Models\ProductReference;
use App\Models\SettingConnector;
use App\Repositories\CategoryOptionRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\OpenTimeslotRepository;
use App\Repositories\OptionItemRepository;
use App\Repositories\OptionRepository;
use App\Repositories\ProductAllergenenRepository;
use App\Repositories\ProductLabelRepository;
use App\Repositories\ProductOptionRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductSuggestionRepository;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;

class CategoryImport
{
    use Importable;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var OptionRepository
     */
    private $optionRepository;

    /**
     * @var OptionItemRepository
     */
    private $optionItemRepository;

    /**
     * @var OpenTimeslotRepository
     */
    private $openTimeslotRepository;

    /**
     * @var ProductOptionRepository
     */
    private $productOptionRepository;

    /**
     * @var CategoryOptionRepository
     */
    private $categoryOptionRepository;

    /**
     * @var ProductSuggestionRepository
     */
    private $productSuggestionRepository;

    /**
     * @var ProductAllergenenRepository
     */
    private $productAllergenenRepository;

    /**
     * @var ProductLabelRepository
     */
    private $productLabelRepository;

    /**
     * @var int
     */
    private $workspaceId;

    /**
     * IMPORTANT: MOST BE EQUAL TO EXPORT..
     * @var int
     */
    private $numberOfOptions = 4;

    /**
     * @var string
     */
    const PATTERN = '/(,|\.)\s|(,|\.)/';

    /**
     * CategoryImport constructor.
     *
     * @param int                         $workspaceId
     * @param CategoryRepository          $categoryRepository
     * @param ProductRepository           $productRepository
     * @param OptionRepository            $optionRepository
     * @param OptionItemRepository        $optionItemRepository
     * @param OpenTimeslotRepository      $openTimeslotRepository
     * @param ProductOptionRepository     $productOptionRepository
     * @param CategoryOptionRepository    $categoryOptionRepository
     * @param ProductSuggestionRepository $productSuggestionRepository
     * @param ProductAllergenenRepository $productAllergenenRepository
     * @param ProductLabelRepository      $productLabelRepository
     */
    public function __construct(
        int $workspaceId,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        OptionRepository $optionRepository,
        OptionItemRepository $optionItemRepository,
        OpenTimeslotRepository $openTimeslotRepository,
        ProductOptionRepository $productOptionRepository,
        CategoryOptionRepository $categoryOptionRepository,
        ProductSuggestionRepository $productSuggestionRepository,
        ProductAllergenenRepository $productAllergenenRepository,
        ProductLabelRepository $productLabelRepository
    ) {
        $this->workspaceId = $workspaceId;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->optionRepository = $optionRepository;
        $this->optionItemRepository = $optionItemRepository;
        $this->openTimeslotRepository = $openTimeslotRepository;
        $this->productOptionRepository = $productOptionRepository;
        $this->categoryOptionRepository = $categoryOptionRepository;
        $this->productSuggestionRepository = $productSuggestionRepository;
        $this->productAllergenenRepository = $productAllergenenRepository;
        $this->productLabelRepository = $productLabelRepository;

        // Adjust based on connectors..
        $connectorCount = count(SettingConnector::getProviders());
        $this->numberOfOptions += $connectorCount;
    }

    /**
     * @param array $dataExcel
     * @return array
     */
    public function sheetOptions($dataExcel = [])
    {
        $optionIds = array();

        $fields = [
            'workspace_id',
            'order',
            'name',
            'min',
            'max',
            'is_ingredient_deletion',
            'type',
        ];

        foreach ($dataExcel as $row => $dataRow) {
            if ($row < 2) {
                continue;
            }

            $idExcel         = $dataRow[0];
            $dataRow[0]      = $this->workspaceId;
            $dataRow[5]      = (boolean) $dataRow[5];
            $dataRow[6]      = $dataRow[3] > 0;

            // Force array length to be the one we have from fields
            $dataRow = array_splice($dataRow, 0, count($fields));

            $option          = array_combine($fields, $dataRow);

            $option = $this->mutilLangData($option, ['name']);

            $result = $this->optionRepository->create($option);
            $optionIds[$idExcel] = $result->id;
        }

        // Update order of all options to make sure we still have follow up numbers
        $this->optionRepository->updateOrderToPreventDuplicates($this->workspaceId);

        return $optionIds;
    }

    /**
     * @param $dataExcel
     * @param $optionIds
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function sheetOptionItems($dataExcel, $optionIds)
    {
        unset($dataExcel[0]);

        // Length how many options we have
        $listOption = array_chunk($dataExcel, $this->numberOfOptions);



        foreach ($listOption as $option) {
            if (
                count($option) < 3
                || empty($option[0])
                || empty($option[0][0])
            ) {
                continue;
            }

            $position = $this->optionItemRepository->getMaxOrderPositionByWorkspaceId($this->workspaceId, $optionIds[$option[0][0]]);
            if($position == 0) {
                $position = 1;
            }

            foreach ($option[0] as $key => $field) {
                if ($key < 2) {
                    continue;
                }

                if (
                    $option[0][$key] !== NULL
                    && $option[1][$key] !== NULL
                ) {
                    $dataCreate = [
                        'opties_id' => $optionIds[$option[0][0]],
                        'name'      => $option[0][$key],
                        'price'     => $option[1][$key],
                        'available' => (boolean) $option[2][$key],
                        'master'    => (boolean) $option[3][$key],
                        'order'     => $position
                    ];

                    /** @var OptionItem $optionItem */
                    $optionItem = $this->optionItemRepository->create($dataCreate);

                    $position++;

                    if(!empty($optionItem)) {
                        $keyCounter = 3;
                        foreach (SettingConnector::getProviders() as $providerKey => $providerName) {
                            $keyCounter++;
                            $remoteId = !empty($option[$keyCounter][$key])
                                ? $option[$keyCounter][$key]
                                : '';

                            if (!empty($remoteId)) {
                                $optionItemReference = new OptionItemReference();
                                $optionItemReference->workspace_id = $this->workspaceId;
                                $optionItemReference->local_id = $optionItem->id;
                                $optionItemReference->provider = $providerKey;
                                $optionItemReference->remote_id = $remoteId;
                                $optionItemReference->save();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $dataExcel
     * @param $optionIds
     * @return array
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function sheetCategories($dataExcel, $optionIds)
    {
        $categoryIds = array();
        $idExcelCategories = array();
        $idExcelProducts = array();

        foreach ($dataExcel as $key => $row) {
            if ($key < 2) {
                continue;
            }

            $idExcelOptions             = Helper::explodeStr(self::PATTERN, $row[4]);
            $idExcelCategories[$row[0]] = Helper::explodeStr(self::PATTERN, $row[9]);
            $idExcelProducts[$row[0]]   = Helper::explodeStr(self::PATTERN, $row[10]);

            $category = [
                'workspace_id'       => $this->workspaceId,
                'name'               => $row[3],
                'active'             => (boolean) $row[5],
                'available_delivery' => (boolean) $row[6],
                'favoriet_friet'     => (boolean) $row[7],
                'kokette_kroket'     => (boolean) $row[8],
                'individual'         => (boolean) $row[20],
                'group'              => (boolean) $row[21],
            ];

            if ($row[11]) {
                $category['time_no_limit'] = 0;
            }
            if ($row[12]) {
                $category['time_no_limit'] = 1;
            }

            $category = $this->mutilLangData($category, ['name']);
            $obj = $this->categoryRepository->create($category);

            // Open timeslot
            $openTimeslots = array();
            for($i = 13; $i < 20; $i++) {
                $value = (boolean) $row[$i];
                $record = [
                    'workspace_id'  => $this->workspaceId,
                    'foreign_id'    => $obj->id,
                    'foreign_model' => Category::class,
                    'start_time'    => NULL,
                    'end_time'      => NULL,
                    'day_number'    => $i - 12,
                    'status'        => $value,
                ];
                $times = Helper::explodeStr('/(\(|\)|-)/', $row[$i]);
                if ($value) {
                    $record['status']     = TRUE;
                    $record['start_time'] = $times[1] ?? NULL ;
                    $record['end_time']   = $times[2] ?? NULL;
                }
                $openTimeslots[] = $record;
            }
            $this->openTimeslotRepository->saveMany($openTimeslots);

            // Category Options
            $categoryOptions = array();
            foreach ($idExcelOptions as $idOptionExcel) {
                if (!isset($optionIds[$idOptionExcel])) {
                    continue;
                }
                $categoryOptions[] = [
                    'category_id' => $obj->id,
                    'opties_id'   => $optionIds[$idOptionExcel],
                    'is_checked'  => TRUE,
                ];
            }
            $this->categoryOptionRepository->saveMany($categoryOptions);

            // Media
            if ($row[2]) {
                Media::create([
                    'foreign_id'    => $obj->id,
                    'foreign_type'  => Category::AVATAR,
                    'foreign_model' => Category::class,
                    'full_path'     => $row[2],
                ]);
            }

            $categoryIds[$row[0]] = $obj->id;
        }

        return [
            'ids_category_db'    => $categoryIds,
            'ids_category_excel' => $idExcelCategories,
            'ids_product_excel'  => $idExcelProducts,
        ];
    }

    /**
     * @param $dataExcel
     * @param $optionIds
     * @param $resultIds
     * @return bool
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function sheetProducts($dataExcel, $optionIds, $resultIds)
    {
        $categoryIds       = $resultIds['ids_category_db'];
        $idExcelCategories = $resultIds['ids_category_excel'];
        $idExcelProducts   = $resultIds['ids_product_excel'];

        $productIds = array();
        $productGroupByIdExcel = array();

        foreach ($dataExcel as $key => $row) {
            if ($key < 2) {
                continue;
            }

            $idExcelOptions = Helper::explodeStr(self::PATTERN, $row[9]);

            $product = [
                'workspace_id'        => $this->workspaceId,
                'name'                => $row[3],
                'description'         => $row[4],
                'price'               => $row[5],
                'vat_id'              => $row[6],
                'category_id'         => $categoryIds[$row[7]],
                'use_category_option' => (boolean) $row[8],
                'active'              => (boolean) $row[10],
                'order'               => 100000000,
            ];

            if ($row[11]) {
                $product['time_no_limit'] = 0;
            }
            if ($row[12]) {
                $product['time_no_limit'] = 1;
            }

            $product = $this->mutilLangData($product, ['name', 'description']);
            $obj = $this->productRepository->create($product);

            $productGroupByIdExcel[$row[7]][] = $obj->id;
            $productIds[$row[0]] = $obj->id;

            // Open timeslot
            $openTimeslots = array();
            for($i = 13; $i < 20; $i++) {
                $value = (boolean) $row[$i];
                $times = Helper::explodeStr('/(\(|\)|-)/', $row[$i]);
                $record = [
                    'workspace_id'  => $this->workspaceId,
                    'foreign_id'    => $obj->id,
                    'foreign_model' => Product::class,
                    'start_time'    => NULL,
                    'end_time'      => NULL,
                    'day_number'    => $i - 12,
                    'status'        => $value,
                ];
                if ($value) {
                    $record['status']     = TRUE;
                    $record['start_time'] = $times[1] ?? NULL ;
                    $record['end_time']   = $times[2] ?? NULL;
                }
                $openTimeslots[] = $record;
            }
            $this->openTimeslotRepository->saveMany($openTimeslots);

            // Product Options
            $productOptions = array();
            foreach ($idExcelOptions as $idOptionExcel) {
                if (!isset($optionIds[$idOptionExcel])) {
                    continue;
                }
                $productOptions[] = [
                    'product_id' => $obj->id,
                    'opties_id'  => $optionIds[$idOptionExcel],
                    'is_checked' => TRUE,
                ];
            }
            $this->productOptionRepository->saveMany($productOptions);

            // Product Label
            $labels = [1 => 20, 2 => 21, 3 => 22, 4 => 23, 5 => 24];
            $productLabels = array();
            foreach ($labels as $keyLb => $noRow) {
                $productLabels[] = [
                    'product_id' => $obj->id,
                    'type'       => $keyLb,
                    'active'     => (boolean) $row[$noRow],
                ];
            }
            $this->productLabelRepository->saveMany($productLabels);

            // Product Allergenens
            $allergens = [1, 2, 3, 4, 5, 7, 8, 11, 12, 13, 14, 6, 9, 10];
            $productAllergenens = array();
            for($i = 26; $i < 40; $i++) {
                if ((boolean) $row[$i]) {
                    $productAllergenens[] = [
                        'product_id'    => $obj->id,
                        'allergenen_id' => $allergens[$i - 26],
                    ];
                }
            }
            $this->productAllergenenRepository->saveMany($productAllergenens);

            // Product references
            $keyCounter = 40;
            foreach(SettingConnector::getProviders() as $providerKey => $providerName) {
                $remoteId =  !empty($row[$keyCounter])
                    ? $row[$keyCounter]
                    : '';

                if(!empty($remoteId)) {
                    $productReference = new ProductReference();
                    $productReference->workspace_id = $this->workspaceId;
                    $productReference->local_id = $obj->id;
                    $productReference->provider = $providerKey;
                    $productReference->remote_id = $remoteId;
                    $productReference->save();
                }

                $keyCounter++;
            }

            // Media
            if ($row[2]) {
                Media::create([
                    'foreign_id'    => $obj->id,
                    'foreign_type'  => Product::AVATAR,
                    'foreign_model' => Product::class,
                    'full_path'     => $row[2],
                ]);
            }
        }

        // Update product suggesstion into product_sugesstion table
        $dataUpdate = array();

        foreach ($idExcelProducts as $idExcel => $arrIdExcelProduct) {
            if (!$arrIdExcelProduct) {
                continue;
            }

            foreach ($arrIdExcelProduct as $idExcelProduct) {
                if (!isset($productIds[$idExcelProduct])) {
                    continue;
                }

                $productReal = $productIds[$idExcelProduct];
                $dataUpdate[$categoryIds[$idExcel] . $productReal] = [
                    'category_id' => $categoryIds[$idExcel],
                    'product_id'  => $productReal,
                ];
            }
        }

        foreach ($idExcelCategories as $idExcel => $idExcelOther) {
            if (!$idExcelOther) {
                continue;
            }

            foreach ($idExcelOther as $id) {
                if (!isset($productGroupByIdExcel[$id])) {
                    continue;
                }

                foreach ($productGroupByIdExcel[$id] as $idRealProduct) {
                    $dataUpdate[$categoryIds[$idExcel] . $idRealProduct] = [
                        'category_id' => $categoryIds[$idExcel],
                        'product_id'  => $idRealProduct,
                    ];
                }
            }
        }

        $this->productSuggestionRepository->saveMany($dataUpdate);
    }

    /**
     * @param $record
     * @param $fields
     * @return mixed
     */
    private function mutilLangData($record, $fields)
    {
        $languages = config('languages');

        $currentLang = app()->getLocale();

        foreach($languages as $langKey => $langLabel) {
            if($langKey == $currentLang) {
                continue;
            }
            foreach ($fields as $fl) {
                $record[$langKey][$fl] = $record[$fl];
            }
        }

        return $record;
    }
}
