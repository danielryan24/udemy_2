<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Models\Customer;
use Faker\Core\File as CoreFile;
use Illuminate\Http\Request;
use File;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customers = Customer::when($request->has('search'), function($query) use ($request) {
            $query->where('first_name', 'LIKE', "%$request->search%")
            ->orWhere('last_name', 'LIKE', "%$request->search%")
            ->orWhere('phone', 'LIKE', "%$request->search%")
            ->orWhere('email', 'LIKE', "%$request->search%");
        })->orderBy('id', 'DESC')->get();
        return view('customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerStoreRequest $request)
    {
        $customer = new Customer();

        if($request->hasFile('image')){
            $image = $request->file('image');
            $fileName = $image->store('', 'public');
            $filePath = '/uploads/'.$fileName;
            $customer->image = $filePath;
        }

        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->bank_account_number = $request->bank_account_number;
        $customer->about = $request->about;
        $customer->save();

        // dd($customer);
        $customers = Customer::all();
        return view('customer.index', compact('customers'));
        

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('customer.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('customer.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerStoreRequest $request, string $id)
    {
        $customer = Customer::findOrFail($id);
        // dd($request->all());

        if($request->hasFile('image')){
            File::delete(public_path($customer->image));
            $image = $request->file('image');
            $fileName = $image->store('', 'public');
            $filePath = '/uploads/'.$fileName;
            $customer->image = $filePath;
        }

        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->bank_account_number = $request->bank_account_number;
        $customer->about = $request->about;
        $customer->save();

        // dd($customer);

        $customers = Customer::all();
            return view('customer.index', compact('customers'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        File::delete(public_path($customer->image));
        $customer->delete();
        return redirect()->route('customers.index');
    }
}
