<?php

namespace App\Http\Controllers;

use DB;
use Inertia\Inertia;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\InvoiceProduct;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{
    public function invoiceCreate(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();

        try {
            $user_id = $request->header('user_id');
            $data = [
                'user_id' => $user_id,
                'customer_id' => $request->input('customer_id'),
                'total' => $request->input('total'),
                'discount' => $request->input('discount'),
                'vat' => $request->input('vat'),
                'payable' => $request->input('payable')
            ];
            $invoice = Invoice::create($data);

            $products = $request->input('products');

            foreach ($products as $product) {
                $existUnit = Product::where('id', $product['id'])->select('unit')->first();
                if (!$existUnit) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => "Product with ID {$product['id']} not found"
                    ], 404);
                }
                if ($existUnit->unit < $product['unit']) {
                    // return response()->json([
                    //     'status' => 'failed',
                    //     'message' => "Only {$existUnit->unit} Units available for product with ID {$product['id']}"
                    // ]);
                    $data = [
                        'status' => false,
                        'message' => "Only {$existUnit->unit} Units available for product with ID {$product['id']}"
                    ];
                    return redirect('invoice_list_page')->with($data);
                }

                InvoiceProduct::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product['id'],
                    'user_id' => $user_id,
                    'qty' => $product['unit'],
                    'sale_price' => $product['price']
                ]);

                Product::where('id', $product['id'])->update([
                    'unit' => $existUnit->unit - $product['unit']
                ]);
            }
            DB::commit();

            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'Invoice created successfull'
            // ]);

            $data = [
                'status' => true,
                'message' => 'Invoice created successfull',
                'error' => ''
            ];
            return redirect('invoice_list_page')->with($data);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'Something went wrong please try again later'
            ]);
        }
    }

    public function invoiceList(Request $request)
    {
        $user_id = $request->header('id');
        return Invoice::where('user_id', $user_id)->with('customer')->get();
    }

    public function invoiceDetails(Request $request)
    {
        $user_id = $request->header('id');

        $customerDetails = Customer::where('user_id', $user_id)->where('id', $request->input('customer_id'))->first();

        $invoiceDetails = Invoice::where('user_id', $user_id)->where('id', $request->input('invoice_id'))->first();

        $invoiceProduct = InvoiceProduct::where('user_id', $user_id)->where('invoice_id', $request->input('invoice_id'))->with('product')->get();

        return [
            'invoice' => $invoiceDetails,
            'customer' => $customerDetails,
            'product' => $invoiceProduct
        ];
    }

    public function invoiceDelete(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user_id = $request->header('user_id');
            InvoiceProduct::where('user_id', $user_id)->where('invoice_id', $id)->delete();
            Invoice::where('user_id', $user_id)->where('id', $id)->delete();

            DB::commit();
            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'Invoice deleted successfull'
            // ]);
            $data = [
                'status' => true,
                'message' => 'Invoice deleted successfull'
            ];
            return redirect('invoice_list_page')->with($data);

        } catch (\Throwable $th) {
            DB::rollBack();
            // return response()->json([
            //     'status' => 'faield',
            //     'message' => 'Something went wrong.'
            // ]);
            $data = [
                'status' => false,
                'message' => 'Something went wrong.'
            ];
            return redirect('invoice_list_page')->with($data);
        }
    }

    public function InvoiceListPage(Request $request)
    {
        $user_id = $request->header('user_id');
        $list = Invoice::where('user_id', $user_id)->with(['customer', 'invoiceProduct.product'])->get();
        return Inertia::render('InvoiceListPage', [
            'list' => $list
        ]);

    }
}
