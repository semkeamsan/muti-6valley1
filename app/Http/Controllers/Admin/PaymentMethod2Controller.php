<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Model\Translation;
use App\Model\PaymentMethod;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;

use function App\CPU\translate;

class PaymentMethod2Controller extends Controller
{
    public function index(Request $request)
    {
        $paymentMethod = new PaymentMethod;
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $paymentMethod = PaymentMethod::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }

        $paymentMethod = $paymentMethod->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.payment-method.index', compact('paymentMethod', 'search'));
    }

    public function create(Request $request)
    {
        return view('admin-views.payment-method.create');

    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
        ], [
            'name.required' => 'Name is required!',
            'account_name.required' => 'Account name is required!',
            'account_number.required' => 'Account number is required!',
        ]);

        $paymentMethod = new PaymentMethod;
        $paymentMethod->name = $request->name[array_search('en', $request->lang)];
        $paymentMethod->account_name = $request->account_name;
        $paymentMethod->account_number = $request->account_number;
        if ($request->image) {

            $image = collect(explode('/', $request->image))->last();
            $file = move_image('temp/' . $image, 'payment-methods/', $image);

            if ($file) {
                $paymentMethod->image = $file;
            }
        }
        $paymentMethod->save();

        $data = [];
        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                $data[] = array(
                    'translationable_type' => 'App\Model\PaymentMethod',
                    'translationable_id' => $paymentMethod->id,
                    'locale' => $key,
                    'key' => 'name',
                    'value' => $request->name[$index],
                );
            }
        }
        if (count($data)) {
            Translation::insert($data);
        }

        Toastr::success(translate('Payment method added successfully!'));
        return redirect()->route('admin.payment-method.index');
    }

    public function edit(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::withoutGlobalScopes()->find($id);
        return view('admin-views.payment-method.edit', compact('paymentMethod'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
        ], [
            'name.required' => 'Name is required!',
            'account_name.required' => 'Account name is required!',
            'account_number.required' => 'Account number is required!',
        ]);


        $paymentMethod = PaymentMethod::find($id);
        $oldimage = $paymentMethod->image;
        $paymentMethod->name = $request->name[array_search('en', $request->lang)];
        $paymentMethod->account_name = $request->account_name;
        $paymentMethod->account_number = $request->account_number;
        if ($request->image) {

            $image = collect(explode('/', $request->image))->last();
            $file = move_image('temp/' . $image, 'payment-methods/', $image);

            if ($file) {
                $paymentMethod->image = $file;

                if ($oldimage) {
                    delete_image('payment-methods/' . $oldimage);
                }
            }
        }
        $paymentMethod->save();

        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                Translation::updateOrInsert(
                    ['translationable_type' => 'App\Model\PaymentMethod',
                        'translationable_id' => $paymentMethod->id,
                        'locale' => $key,
                        'key' => 'name'],
                    ['value' => $request->name[$index]]
                );
            }
        }

        Toastr::success(translate('Payment method updated successfully!'));
        return redirect()->route('admin.payment-method.index');
    }

    public function destroy($id)
    {

        $translation = Translation::where('translationable_type', 'App\Model\PaymentMethod')
            ->where('translationable_id', $id);
        $translation->delete();

//        if ($old = PaymentMethod::find($id)) {
//            delete_image('payment-methods/' . $old->image);
//        }

        PaymentMethod::destroy($id);

        return response()->json();
    }

    public function fetch(Request $request)
    {
        if ($request->ajax()) {
            $data = PaymentMethod::where('position', 0)->orderBy('id', 'desc')->get();
            return response()->json($data);
        }
    }

}
