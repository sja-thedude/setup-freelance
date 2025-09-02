<?php

namespace App\Http\Controllers\Manager\Excel;

use App\Excel\Export\CategoryExport;
use App\Excel\Import\CategoryImport;
use App\Http\Controllers\Manager\BaseController;
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
use Excel;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends BaseController
{
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
     * CategoryController constructor.
     *
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
        parent::__construct();

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
    }

    /**
     * @param Request $request
     * @return \Maatwebsite\Excel\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export(Request $request)
    {
        $fileName = "sample_file.xlsx";

        $categories = $this->categoryRepository->getAll($request, $this->tmpUser->workspace_id);

        $request->request->add(['workspace_id' => $this->tmpUser->workspace_id]);

        $products = $this->productRepository->paginate(10000000);

        $options = $this->optionRepository->getAll($request, $this->tmpUser->workspace_id);

        $categoryExport = new CategoryExport($categories, $products, $options);

        return Excel::download($categoryExport, $fileName);
    }

    /**
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function import()
    {
        try {
            $fileExcel = request()->file('file');

            if ($fileExcel) {

                if (!in_array($fileExcel->getClientOriginalExtension(), ["xlsx"])) {

                    Flash::error('Error format!');

                    return redirect()->back()->withInput();
                }

                $objImport = new CategoryImport(
                    $this->tmpUser->workspace_id,
                    $this->categoryRepository,
                    $this->productRepository,
                    $this->optionRepository,
                    $this->optionItemRepository,
                    $this->openTimeslotRepository,
                    $this->productOptionRepository,
                    $this->categoryOptionRepository,
                    $this->productSuggestionRepository,
                    $this->productAllergenenRepository,
                    $this->productLabelRepository
                );

                $dataExcel = $objImport->toArray($fileExcel);

                $optionIds = $objImport->sheetOptions($dataExcel[2]);

                $objImport->sheetOptionItems($dataExcel[3], $optionIds);

                $resultIds = $objImport->sheetCategories($dataExcel[0], $optionIds);

                $objImport->sheetProducts($dataExcel[1], $optionIds, $resultIds);

                Flash::success(trans('category.message_import_success'));
            }

        } catch (\Exception $exc) {
            Log::error($exc->getMessage(), [
                'file' => $exc->getFile(),
                'line' => $exc->getLine(),
            ]);

            Flash::error($exc->getMessage());
        }

        return redirect()->back()->withInput();
    }
}

