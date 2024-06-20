<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Inertia\Inertia;
use Inertia\Response;
use Automattic\WooCommerce\Client;
use Exception;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $data = file_get_contents('https://api.otech.co.il:21443/webservice/GetCatalogItems?UserName=gongapitest&Password=gongapitest2022&Date=2022-07-06T00:00:00');
        $json_data = json_decode($data, true);

        if(!$json_data['HasError'] && count($json_data['ParitList']) > 0) {

            $grouped_by_item_code = array_reduce($json_data['ParitList'], function ($obj, $item) {
                $obj[$item['ItemCode']][] = $item;
                return $obj;
            }, []);

            foreach ($grouped_by_item_code as $products) {

                $woocommerce = new Client(
                    'https://wordpress-1142719-4643873.cloudwaysapps.com',
                    'ck_365251ba9429b039788780c31a64a028ae33a461',
                    'cs_46362641da98310e04e6c535360bc64f31dd828e',
                    [
                        'version' => 'wc/v3',
                    ]
                );
                
                $product_data = [
                    "name" => $products[0]['ItemDescription'],
                    "type" => "variable",
                    "regular_price" => $products[0]['Price'],
                    "description" => $products[0]['ItemDescription'],
                    "sku" => $products[0]['ItemCode'],
                    "attributes" => [
                        [
                            "id"  => 4,
                            "name" => "Color",
                            "visible" => true,
                            "variation" => true,
                            "options" => ["בורדו", "כללי", "שחור","מלאנג'","רויאל", "לבן"]
                        ],
                        [
                            "id"  => 5,
                            "name" => "Size",
                            "visible" => true,
                            "variation" => true,
                            "options" => ["XL", "ללא", "S", "L"]
                        ]
                    ]
                ];

                $response = $woocommerce->post('products', $product_data);

                if (isset($response->id)) {

                    $product_id = $response->id;

                    foreach ($products as $product) {

                        $product_variation_data = [
                            "regular_price" => $product['Price'],
                            "attributes" => [
                                [
                                    "id" => 4,
                                    "option" => $product['ColorDescription']
                                ],
                                [
                                    "id" => 5,
                                    "option" => $product['SizeDescription']
                                ]
                            ]
                        ];

                        $woocommerce->post("products/$product_id/variations", $product_variation_data);
                    }
                }
            }
        }

        // $data = file_get_contents('https://api.otech.co.il:21443/webservice/GetCatalogItems?UserName=gongapitest&Password=gongapitest2022&Date=2022-07-06T00:00:00');
        // $json_data = json_decode($data, true);

        // if(!$json_data['HasError'] && count($json_data['ParitList']) > 0) {

        //     // store product setting
        //     $store_product = curl_init();
        //     curl_setopt($store_product, CURLOPT_URL, 'https://wordpress-1142719-4643873.cloudwaysapps.com/wp-json/wc/v3/products');
        //     curl_setopt($store_product, CURLOPT_RETURNTRANSFER, true);
        //     curl_setopt($store_product, CURLOPT_POST, true);
        //     curl_setopt($store_product, CURLOPT_HTTPHEADER, array(
        //         'Content-Type: application/json',
        //         'Authorization: Basic ' . base64_encode('ck_365251ba9429b039788780c31a64a028ae33a461:cs_46362641da98310e04e6c535360bc64f31dd828e'),
        //     ));

        //     // store product variation setting
        //     $store_product_variation = curl_init();
        //     curl_setopt($store_product_variation, CURLOPT_RETURNTRANSFER, true);
        //     curl_setopt($store_product_variation, CURLOPT_POST, true);
        //     curl_setopt($store_product_variation, CURLOPT_HTTPHEADER, array(
        //         'Content-Type: application/json',
        //         'Authorization: Basic ' . base64_encode('ck_365251ba9429b039788780c31a64a028ae33a461:cs_46362641da98310e04e6c535360bc64f31dd828e'),
        //     ));

        //     if (curl_errno($store_product)) {
        //         dd('Error:' . curl_error($store_product));
        //     } else {

        //         $grouped_by_item_code = array_reduce($json_data['ParitList'], function ($obj, $item) {
        //             $obj[$item['ItemCode']][] = $item;
        //             return $obj;
        //         }, []);

        //         foreach ($grouped_by_item_code as $products) {

        //             $product_data = [
        //                 "name" => $products[0]['ItemDescription'],
        //                 "type" => "variable",
        //                 "regular_price" => $products[0]['Price'],
        //                 "description" => $products[0]['ItemDescription'],
        //                 "sku" => $products[0]['ItemCode'],
        //                 "attributes" => [
        //                     [
        //                         "id"  => 4,
        //                         "name" => "Color",
        //                         "visible" => true,
        //                         "variation" => true,
        //                         "options" => ["בורדו", "כללי", "שחור","מלאנג'","רויאל", "לבן"]
        //                     ],
        //                     [
        //                         "id"  => 5,
        //                         "name" => "Size",
        //                         "visible" => true,
        //                         "variation" => true,
        //                         "options" => ["XL", "ללא", "S", "L"]
        //                     ]
        //                 ]
        //             ];

        //             curl_setopt($store_product, CURLOPT_POSTFIELDS, json_encode($product_data));
                    
        //             $response = curl_exec($store_product);
        //             $decoded_response = json_decode($response, true);

        //             if (isset($decoded_response['id'])) {

        //                 $product_id = $decoded_response['id'];
        //                 curl_setopt($store_product_variation, CURLOPT_URL, "https://wordpress-1142719-4643873.cloudwaysapps.com/wp-json/wc/v3/products/$product_id/variations");

        //                 foreach ($products as $product) {
        //                     $product_variation_data = [
        //                         "regular_price" => $product['Price'],
        //                         "attributes" => [
        //                             [
        //                                 "id" => 4,
        //                                 "option" => $product['ColorDescription']
        //                             ],
        //                             [
        //                                 "id" => 5,
        //                                 "option" => $product['SizeDescription']
        //                             ]
        //                         ]
        //                     ];

        //                     curl_setopt($store_product_variation, CURLOPT_POSTFIELDS, json_encode($product_variation_data));
        //                     curl_exec($store_product_variation);

        //                     curl_close($store_product_variation);
        //                 }
        //             }

        //             curl_close($store_product);
        //         }
        //     }

        // } else {
        //     dd("Somthing Wrong");
        // }

        dd(1);

        $this->middleware('auth');
    }

    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'total_members' => Member::all()->count(),
            'page_name' => 'Dashboard',
        ]);
    }


    // public function getPluginDetails()
    // {
    //     $client = new GuzzleHttpClient();

    //     try {
    //         $response = $client->post("$this->wpServerUrl/wp-json/jwt-auth/v1/token", [
    //             'form_params' => [
    //                 'username' => $this->username,
    //                 'password' => $this->password,
    //             ],
    //         ]);

    //         $body = $response->getBody();
    //         $data = json_decode($body, true);

    //         if (isset($data['token'])) {
    //             $response = Http::get("$this->wpServerUrl/wp-json/get-plugin-details/data");
    //             $body = $response->getBody();
    //             $data = json_decode($body, true);

    //             if($data) {
    //                 return $this->successResponse(message: "Plugin details fetch successfully.", data : $data);
    //             } else {
    //                 return $this->errorResponse(message: "Somthing went wrong please try again.");
    //             }
    //         }
            
    //     } catch (RequestException $e) {
    //         return $this->errorResponse(message: "Somthing went wrong please try again.");
    //     }
    // }

    // public function activeOrDeactive(Request $request)
    // {
    //     $client = new GuzzleHttpClient();

    //     try {
    //         $response = $client->post("$this->wpServerUrl/wp-json/jwt-auth/v1/token", [
    //             'form_params' => [
    //                 'username' => $this->username,
    //                 'password' => $this->password,
    //             ],
    //         ]);

    //         $body = $response->getBody();
    //         $data = json_decode($body, true);

    //         if (isset($data['token'])) {

    //             $response = Http::post("$this->wpServerUrl/wp-json/update-plugin-status/submit",[
    //                 'status' => $request->status
    //             ]);

    //             $body = $response->getBody();
    //             $data = json_decode($body, true);

    //             if($data['success']) {
    //                 $msg = $request->status == 1 ? 'Plugin active successfully.' : 'Plugin Deactivate successfully.';
    //                 return $this->successResponse(message: $msg);
    //             } else {
    //                 return $this->errorResponse(message: "Somthing went wrong please try again.");
    //             }
    //         }
            
    //     } catch (RequestException $e) {
    //         return $this->errorResponse(message: "Somthing went wrong please try again.");
    //     }
    // }

    // public function delete()
    // {
    //     $client = new GuzzleHttpClient();

    //     try {
    //         $response = $client->post("$this->wpServerUrl/wp-json/jwt-auth/v1/token", [
    //             'form_params' => [
    //                 'username' => $this->username,
    //                 'password' => $this->password,
    //             ],
    //         ]);

    //         $body = $response->getBody();
    //         $data = json_decode($body, true);

    //         if (isset($data['token'])) {
    //             $response = Http::get("$this->wpServerUrl/wp-json/delete/data");
    //             $body = $response->getBody();
    //             $data = json_decode($body, true);

    //             if($data) {
    //                 return $this->successResponse(message: "Plugin delete successfully.");
    //             } else {
    //                 return $this->errorResponse(message: "Somthing went wrong please try again.");
    //             }
    //         }
            
    //     } catch (RequestException $e) {
    //         return $this->errorResponse(message: "Somthing went wrong please try again.");
    //     }
    // }
}