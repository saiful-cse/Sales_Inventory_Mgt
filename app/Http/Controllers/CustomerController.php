<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Request as GlobalRequest;

class CustomerController extends Controller
{
    public function customerCreate(Request $request)
    {
        try {
            $user_id = $request->header('user_id');
            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'mobile' => 'required'
            ]);

            Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'user_id' => $user_id
            ]);

            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'Customer created successfull'
            // ]);
            $data = ['message' => 'Customer created successfull', 'status' => true, 'error' => ''];
            return redirect('/customer_page')->with($data);

        } catch (\Throwable $th) {
            // return response()->json([
            //     'status' => 'failed',
            //     'message' => 'Customer created failed'
            // ]);
            $data = ['message' => 'Customer created failed', 'status' => false, 'error' => $th->getMessage()];
            return redirect('/customer_page')->with($data);
        }
    }

    public function customerList(Request $request)
    {
        $user_id = $request->header('id');
        return Customer::where('user_id', $user_id)->get();
    }

    public function customerById(Request $request)
    {
        $user_id = $request->header('id');
        return Customer::where('user_id', $user_id)->where('id', $request->input('id'))->first();
    }

    public function customerUpdate(Request $request)
    {
        $user_id = $request->header('user_id');
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required'
        ]);
        Customer::where('user_id', $user_id)->where('id', $request->input('id'))->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile
        ]);

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Customer updated successfull'
        // ]);
        $data = ['message' => 'Customer updated successfull', 'status' => true, 'error' => ''];
        return redirect('/customer_page')->with($data);
    }

    public function customerDelete(Request $request, $id)
    {
        $user_id = $request->header('user_id');
        Customer::where('user_id', $user_id)->where('id', $id)->delete();
        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Customer deleted successfull'
        // ]);
        $data = ['message' => 'Customer deleted successfull', 'status' => true, 'error' => ''];
        return redirect('/customer_page')->with($data);
    }

    public function CustomerPage(Request $request)
    {
        $user_id = $request->header('user_id');
        $customers = Customer::where('user_id', $user_id)->latest()->get();
        return Inertia::render('CustomerPage', ['customers' => $customers]);
    }

    public function CustomerSavePage(Request $request)
    {
        $user_id = $request->header('user_id');
        $customer = Customer::where('user_id', $user_id)->where('id', $request->query('id'))->first();
        return Inertia::render('CustomerSavePage', ['customer' => $customer]);
    }

}
