<?php

namespace App\Http\Controllers\API;

use App\Models\PrinterJob;
use App\Repositories\PrinterJobRepository;
use Illuminate\Http\Request;
use App\Facades\Helper;

/**
 * Class PrinterJobController
 * @package App\Http\Controllers\API
 */
class PrinterJobAPIController extends AppBaseController
{
    private $printerJobRepository;
    
    public function __construct(PrinterJobRepository $printerJobRepo)
    {
        // Disable sessions..
        $this->enableStateless = true;

        parent::__construct();
        
        $this->printerJobRepository = $printerJobRepo;
    }
    
    /**
     * @param  Request  $request
     * @param $workspaceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function printerStarAskJob(Request $request, $workspaceId)
    {
        // server call this function instead of printerStarConfirmJob because server don't support method "DELETE"
        $confirmation = $request->get('delete', null);
        
        if (!is_null($confirmation)) {
            $result = $this->printerStarConfirmProcess($request, $workspaceId);
            return response()->json($result);
        }
        
        $result = ['jobReady' => false];
        $printerMAC = $request->get('printerMAC', null);
        $statusCode = $request->get('statusCode', null);
        $printingInProgress = $request->get('printingInProgress');
        
        if (empty($printerMAC) || empty($statusCode) || $statusCode != '200%20OK' || !empty($printingInProgress)) {
            return response()->json($result);
        }
        
        $job = $this->printerJobRepository->needPrint($workspaceId, $printerMAC);
        
        if (empty($job)) {
            return response()->json($result);
        }
        
        $print = $this->printerJobRepository->processPrint($workspaceId, $printerMAC, $job);
        
        if (empty($print['content'])) {
            return response()->json($result);
        }
        
        // Check what content type we now need to print
        $mediaTypes = null;
        switch ($print['content']['type']) {
            case 'image':
                $mediaTypes = 'image/png';
                break;
            
            case 'bbcode':
                $mediaTypes = 'application/vnd.star.line';
                break;
        }
        
        if (empty($mediaTypes)) {
            return response()->json($result);
        }
        
        $result = [
            'jobReady' => true,
            'jobToken' => $job->id,
            'mediaTypes' => [$mediaTypes]
        ];
        
        return response()->json($result);
    }

    /**
     * @param  Request  $request
     * @param $workspaceId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function printerStarProcessJob(Request $request, $workspaceId)
    {
        $printerMAC = $request->get('mac', null);
        
        if (empty($printerMAC)) {
            return response('Mac not found!', 404);
        }
        
        $print = $this->printerJobRepository->processPrint($workspaceId, $printerMAC);
        
        if (empty($print)) {
            return response('Source not found!', 404);
        }
        
        switch ($print['content']['type']) {
            case 'image':
                $response = response(\Storage::disk('public')->get($print['content']['path']))
                    ->header('Content-Type', 'image/png');
                break;
            
            default:
            case 'bbcode':
                $text = isset($print['content']['text']) ? $print['content']['text'] : '';
                
                $starLine = new \App\Services\Star\LineJob();
                $starLine->fromBbCode($text);
                
                $response = response($starLine->getPrintJobData())
                    ->header('Content-Type', 'application/vnd.star.line');
                break;
        }
        
        if (empty($print['last'])) {
            $response = $response->header('X-Star-Cut', 'none; feed=false');
        }
        
        return $response;
    }
    
    /**
     * @param  Request  $request
     * @param $workspaceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function printerStarConfirmJob(Request $request, $workspaceId)
    {
        $result = $this->printerStarConfirmProcess($request, $workspaceId);
        return response()->json($result);
    }
    
    /**
     * @param $request
     * @param $workspaceId
     * @return mixed
     */
    private function printerStarConfirmProcess($request, $workspaceId)
    {
        $printerMAC = $request->get('mac', null);
        $statusCode = $request->get('code', null);
        $result = $request->all();
        
        if (!is_null($printerMAC) && !is_null($statusCode)) {
            $this->printerJobRepository->confirmPrinted($workspaceId, $printerMAC, $statusCode);
        }
        
        return $result;
    }
    
    /**
     * Epson printer uses POST for both asking for a job and for processing a job
     * So we will process the request here and split over the methods 'printerEpsonAskJob' and 'printerEpsonProcessJob'
     * @param  Request  $request
     * @param $workspaceId
     */
    public function printerEpson(Request $request, $workspaceId)
    {
        $httpRequest = $request->input('ConnectionType');
        if ($httpRequest == 'GetRequest') {
            // Return print data
            return $this->printerEpsonProcessJob($request, $workspaceId);
        } elseif ($httpRequest == 'SetResponse') {
            // Get print result
            return $this->printerEpsonConfirmJob($request, $workspaceId);
        }
    }
    
    /**
     * @param  Request  $request
     * @param $workspaceId
     */
    private function printerEpsonProcessJob(Request $request, $workspaceId)
    {
        $id = $request->input('ID');
        $serialNumber = $request->input('Name');
        
        if (empty($serialNumber)) {
            return response('SERIAL NUMBER NOT FOUND', 404, [
                'Content-Type' => 'application/xml; charset=UTF-8'
            ]);
        }
        
        /** @var PrinterJob $job */
        $job = $this->printerJobRepository->needPrint($workspaceId, $serialNumber);
        
        if (empty($job)) {
            return response('JOB NOT FOUND', 404, [
                'Content-Type' => 'application/xml; charset=UTF-8'
            ]);
        }
        
        $contents = $this->printerJobRepository->processPrint($workspaceId, $serialNumber, $job, false);
        
        if (empty($contents)) {
            return response('EMPTY CONTENT', 404, [
                'Content-Type' => 'application/xml; charset=UTF-8'
            ]);
        }
        
        $contentXML = '';
        foreach ($contents as $content) {
            if ($content['type'] == 'image') {
                $imagePath = \Storage::disk('public')->path($content['path']);
                if (file_exists($imagePath)) {
                    if (env('USE_OPTIMIZED_PRINT_ORDER_EPSON') == 1) {
                        $imgdata = Helper::processImage($imagePath, 1.0, 1);
//                        $imageResult = Helper::toMonoImage($imgdata,1.0,1);
                        $contentXML .= '<image width="'.$imgdata['width'].'" height="'.$imgdata['height'].'" color="color_1" mode="mono">'.base64_encode($imgdata['data']).'</image>';
                    } else {
                        $imgdata = Helper::getArrayOfPixelsFromFile($imagePath, true);
                        $imageResult = Helper::toMonoImage($imgdata, 1.0, 1);
                        $contentXML .= '<image width="'.$imgdata['width'].'" height="'.$imgdata['height'].'" color="color_1" mode="mono">'.base64_encode($imageResult).'</image>';
                    }
                    
                }
            } elseif ($content['type'] == 'bbcode') {
                // @todo currently not supported, it is a format of our own to make it easier to layout things
            }
        }
        
        if (empty($contentXML)) {
            return response('EMPTY CONTENT', 404, [
                'Content-Type' => 'application/xml; charset=UTF-8'
            ]);
        }
        
        return response(
            '<?xml version="1.0" encoding="utf-8"?>'
            .'<PrintRequestInfo>'
            .'<ePOSPrint>'
            .'<Parameter>'
            .'<devid>'.$serialNumber.'</devid>'
            .'<timeout>10000</timeout>'
            .'</Parameter>'
            .'<PrintData>'
            .'<epos-print xmlns="http://www.epson-pos.com/schemas/2011/03/epos-print">'
            .$contentXML
            .'<cut type="feed"/>'
            .'</epos-print>'
            .'</PrintData>'
            .'</ePOSPrint>'
            .'</PrintRequestInfo>',
            200, [
            'Content-Type' => 'application/xml; charset=UTF-8'
        ]);
    }
    
    /**
     * @param  Request  $request
     * @param $workspaceId
     */
    private function printerEpsonConfirmJob(Request $request, $workspaceId)
    {
        $id = $request->input('ID');
        $serialNumber = $request->input('Name');
        $responseFile = $request->input('ResponseFile');
        
        if (empty($responseFile)) {
            return response('response not found!', 404);
        }
        
        $xml = simplexml_load_string($responseFile);
        $version = (isset($xml['Version'])) ? reset($xml['Version']) : null;
        
        if (empty($version)) {
            return response('version not found!', 404);
        }
        
        switch ($version) {
            // Currently not supported
            case '1.00':
                foreach ($xml->response as $response) {
                    if ($response['success'][0] == 'true') {
                        $this->printerJobRepository->confirmPrinted($workspaceId, $serialNumber, '200 OK', false);
                    }
                }
                break;
            
            // Currently not supported
            case '2.00':
                foreach ($xml->ePOSPrint as $eposprint) {
                    $devid = $eposprint->Parameter->devid;
                    $printjobid = $eposprint->Parameter->printjobid;
                    $response = $eposprint->PrintResponse->response;
                    
                    if ($response['success'][0] == 'true') {
                        $this->printerJobRepository->confirmPrinted($workspaceId, $serialNumber, '200 OK', false);
                    }
                }
                break;
            
            case '3.00':
                $success = $xml->ServerDirectPrint->Response['Success'];
                
                if ($success == 'true') {
                    # display ePOSDisplay result
                    foreach ($xml->ePOSDisplay as $eposdisplay) {
                        $devid = $eposdisplay->Parameter->devid;
                        $printjobid = $eposdisplay->Parameter->printjobid;
                        $response = $eposdisplay->DisplayResponse->response;
                    }
                    
                    if (!is_null($serialNumber)) {
                        $this->printerJobRepository->confirmPrinted($workspaceId, $serialNumber, '200 OK', false);
                    }
                } else {
                    # display error summary and detail
                    $summary = $xml->ServerDirectPrint->Response->ErrorSummary;
                    $detail = $xml->ServerDirectPrint->Response->ErrorDetail;
                }
                break;
        }
        
        return response('', 200, [
            'Content-Type' => 'application/xml; charset=UTF-8'
        ]);
    }
}