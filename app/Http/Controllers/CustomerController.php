<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();

        return view('customers.index', [
            'customers' => $customers
        ]);
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->all());

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

            $file->storeAs('customers/', $filename, 'public');
            $customer->update([
                'photo' => $filename
            ]);
        }

        return redirect()
            ->route('customers.index')
            ->with('success', 'New customer has been created!');
    }

    public function show(Customer $customer)
    {
        $customer->loadMissing(['quotations', 'orders'])->get();

        return view('customers.show', [
            'customer' => $customer
        ]);
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', [
            'customer' => $customer
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer = auth()->user(); 
        // Update without photo first
        $customer->update($request->except('photo'));

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($customer->photo) {
                $oldPhotoPath = public_path('storage/customers/') . $customer->photo;

                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                } else {
                    \Log::warning("Old customer photo not found for deletion: {$oldPhotoPath}");
                }
            }

            // Save new photo
            $file = $request->file('photo');
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

            $file->storeAs('customers', $fileName, 'public');

            $customer->update([
                'photo' => $fileName
            ]);
        }

        return redirect()
            ->route('customer.profile')
            ->with('success', 'Customer has been updated!');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->photo) {
            $photoPath = public_path('storage/customers/') . $customer->photo;

            if (file_exists($photoPath)) {
                unlink($photoPath);
            } else {
                \Log::warning("Customer photo not found for deletion: {$photoPath}");
            }
        }

        $customer->delete();

        return redirect()
            ->back()
            ->with('success', 'Customer has been deleted!');
    }

    /**
     * Show customer profile (for authenticated customers)
     */
    public function profile()
    {
        $customer = auth()->user();

        return view('customer.profile', [
            'customer' => $customer
        ]);
    }
}
