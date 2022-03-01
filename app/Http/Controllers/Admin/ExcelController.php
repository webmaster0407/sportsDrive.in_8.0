<?php

namespace App\Http\Controllers\Admin;

use App\Product;
use App\ProductCategoryMap;
use App\ProductConfiguration;
use App\ProductFor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

class ExcelController extends Controller
{
    public function uploadExcelView(){
        return view("admin.excel-upload");

    }
    public function importExcel(Request $request){
        $data = $request->all();
        $message = null;
        if($request->hasFile("excel")){
            $reader = ReaderFactory::create(Type::XLSX); // for XLSX files
            $reader->open($request->excel);
            $successCount = 0;
            $errorCount = 0;
            foreach ($reader->getSheetIterator() as $sheetKey=>$sheet) {
                if($sheetKey=="1"){//upload main products
                    foreach ($sheet->getRowIterator() as $key=>$row) {
                        $productCount = Product::where("sku",$row[1])->count();
                        if($productCount>0 && $key!=1){
                            $errorCount++;
                        }else{
                            $date = Carbon::now();
                            $categories = null;
                            $subCategories = null;
                            $subSubCategories = null;
                            $productUsedFor = null;
                            if($key!=1){
                                if($row[2]!=null){
                                    $categories = explode(",",$row[2]);
                                }
                                if($row[3]!=null) {
                                    $subCategories = explode(",", $row[3]);
                                }
                                if($row[4]!=null) {
                                    $subSubCategories = explode(",", $row[4]);
                                }
                                $productData["sku"] = $row[1];
                                $productData["name"] = $row[5];
                                $slug =   $this->seo_friendly_url($row[5]);
                                $productData["slug"] = $this->checkduplicate_URLkey($slug);
                                if($row[6]!=null){
                                    $productUsedFor = explode(",",$row[6]);
                                }
                                $productData["meta_title"] = $row[7];
                                $productData["meta_keyword"] = $row[8];
                                $productData["meta_description"] = $row[9];
                                $productData["short_description"] = $row[10];
                                $productData["description"] = $row[11];
                                $productData["product_specifications"] = $row[12];
                                $productData["price"] = $row[13];
                                $productData["quantity"] = $row[14];
                                $productData["discount_price"] = $row[15];
                                $productData["brand_id"] = $row[16];
                                $productData["video_url"] = $row[17];
                                $productData['created_at'] = $date;
                                $productData['updated_at']  = $date;
                                $productId = Product::insertGetId($productData);
                                //create product category map array
                                if(count($categories)>0){
                                    foreach($categories as $category){
                                        $data = [
                                            'product_id' => $productId,
                                            'category_id' => $category,
                                            'created_at' => $date,
                                            'updated_at' => $date,
                                        ];
                                        ProductCategoryMap::insert($data);
                                    }
                                }
                                if(count($subCategories)>0){
                                    foreach($subCategories as $subCategory){
                                        $data = [
                                            'product_id' => $productId,
                                            'category_id' => $subCategory,
                                            'created_at' => $date,
                                            'updated_at' => $date,
                                        ];
                                        ProductCategoryMap::insert($data);
                                    }
                                }
                                if(count($subSubCategories)>0){
                                    foreach($subSubCategories as $subSubCategory){
                                        $data = [
                                            'product_id' => $productId,
                                            'category_id' => $subSubCategory,
                                            'created_at' => $date,
                                            'updated_at' => $date,
                                        ];
                                        ProductCategoryMap::insert($data);
                                    }
                                }
                                if(count($productUsedFor)>0){
                                    foreach($productUsedFor as $productFor){
                                        $data = [
                                            'product_id' => $productId,
                                            'for' => $productFor,
                                            'created_at' => $date,
                                            'updated_at' => $date,
                                        ];
                                        ProductFor::insert($data);
                                    }
                                }
                                $successCount++;
                            }
                        }
                    }
                    $success = $successCount." product(s) has been successfully inserted.";
                    $error = $errorCount." product(s) not inserted successfully as there SKU repeats.";
                }else{//upload product attributes
                    foreach ($sheet->getRowIterator() as $key=>$row) {
                        $date = Carbon::now();
                        if($key!=1){
                            $product = Product::where("sku",$row[0])->first();
                            if($product){
                                $checkIfExists = ProductConfiguration::where("product_id",$product->id)->where("AttributeColor",$row[1])->where("AttributeSize",$row[2])->count();
                                if($checkIfExists == 0){
                                    $productConfiguration =
                                        [
                                            "product_id" => $product->id,
                                            "AttributeColor" => $row[1],
                                            "AttributeSize" => $row[2],
                                            "quantity" => $row[3],
                                            "price" => $row[4],
                                            "created_at" => $date,
                                            "updated_at" => $date,
                                        ];
                                    ProductConfiguration::insert($productConfiguration);
                                }
                            }
                        }
                    }
                }
            }
            $reader->close();
        }else{
            abort(500);
        }
    return redirect()->back()->with(compact("success","error"));
    }

    function seo_friendly_url($string) {
        $string = str_replace(array('[\', \']'), '', $string);
        $string = preg_replace('/\[.*\]/U', '', $string);
        $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
        $string = htmlentities($string, ENT_COMPAT, 'utf-8');
        $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string);
        $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/'), '-', $string);
        return strtolower(trim($string, '-'));
    }

    public function checkduplicate_URLkey($urlKey) {

        $checkExist = Product::where('slug', $urlKey)
            ->select("id")->count();
        if ($checkExist == 0) {
            return $urlKey;
        }
        $i = 1;
        while ($checkExist > 0) {
            $urlKeyNw = $urlKey . '_' . $i;
            $checkExist = Product::where('slug', $urlKeyNw)
                ->select("id")->count();

            if ($checkExist == 0) {
                return $urlKeyNw;
            }
            $i++;
        }
    }
}
