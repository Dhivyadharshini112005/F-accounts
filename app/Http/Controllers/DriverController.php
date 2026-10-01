<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Income;
use Illuminate\Http\Request;
use App\Services\FTaxiService;

class DriverController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Driver List
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $drivers = Driver::orderByRaw(
            "CASE
                WHEN id_number REGEXP '^[0-9]+$'
                THEN CAST(id_number AS UNSIGNED)
                ELSE 999999999
             END ASC"
        )
        ->orderBy('id_number', 'asc')
        ->get();

        return view(
            'drivers.index',
            compact('drivers')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Sync F-Taxi Drivers
    |--------------------------------------------------------------------------
    */

    public function sync(FTaxiService $ftaxiService)
    {
        $result = $ftaxiService->getDrivers();

        /*
        |--------------------------------------------------------------------------
        | Check F-Taxi response
        |--------------------------------------------------------------------------
        */

        if (
            !isset($result['success']) ||
            $result['success'] !== true
        ) {
            return redirect()
                ->route('drivers.index')
                ->with(
                    'error',
                    'F-Taxi Sync Error: ' .
                    ($result['message'] ?? 'Unable to fetch F-Taxi drivers.')
                );
        }


        $count = 0;


        /*
        |--------------------------------------------------------------------------
        | Process Drivers
        |--------------------------------------------------------------------------
        */

        foreach ($result['data'] as $driverData) {

            /*
            |--------------------------------------------------------------------------
            | F-Taxi Driver ID
            |--------------------------------------------------------------------------
            */

            $ftaxiDriverId =
                $driverData['ftaxi_driver_id']
                ?? null;

            if (empty($ftaxiDriverId)) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Driver ID / ID Number
            |--------------------------------------------------------------------------
            */

            $idNumber =
                $driverData['id_number']
                ?? null;

            if ($idNumber !== null) {

                $idNumber =
                    trim(
                        strip_tags(
                            html_entity_decode(
                                (string) $idNumber
                            )
                        )
                    );

            }


            /*
            |--------------------------------------------------------------------------
            | Driver Name
            |--------------------------------------------------------------------------
            */

            $name =
                $driverData['name']
                ?? '';

            $name =
                trim(
                    strip_tags(
                        html_entity_decode(
                            (string) $name
                        )
                    )
                );

            if ($name === '') {
                $name = 'Unknown Driver';
            }


            /*
            |--------------------------------------------------------------------------
            | Phone
            |--------------------------------------------------------------------------
            */

            $phone =
                $driverData['phone']
                ?? null;

            if ($phone !== null) {

                $phone =
                    preg_replace(
                        '/[^0-9]/',
                        '',
                        (string) $phone
                    );

                if ($phone === '') {
                    $phone = null;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Vehicle Number
            |--------------------------------------------------------------------------
            */

            $vehicleNumber =
                $driverData['vehicle_number']
                ?? null;

            if (
                empty($vehicleNumber) ||
                $vehicleNumber === '+' ||
                $vehicleNumber === '-'
            ) {
                $vehicleNumber = null;
            }


            /*
            |--------------------------------------------------------------------------
            | Vehicle Name
            |--------------------------------------------------------------------------
            */

            $vehicleName =
                $driverData['vehicle_name']
                ?? null;

            if (
                empty($vehicleName) ||
                $vehicleName === '+' ||
                $vehicleName === '-'
            ) {
                $vehicleName = null;
            }


            /*
            |--------------------------------------------------------------------------
            | Pending Amount
            |--------------------------------------------------------------------------
            */

            $pendingAmount =
                $driverData['pending_amount']
                ?? 0;

            $pendingAmount =
                is_numeric($pendingAmount)
                    ? (float) $pendingAmount
                    : 0;


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $status =
                $driverData['status']
                ?? 'active';

            $status =
                strtolower(
                    trim(
                        (string) $status
                    )
                );

            if ($status === '') {
                $status = 'active';
            }


            /*
            |--------------------------------------------------------------------------
            | Save Driver
            |--------------------------------------------------------------------------
            */

            $existingDriver =
                Driver::where(
                    'ftaxi_driver_id',
                    $ftaxiDriverId
                )->first();


            Driver::updateOrCreate(

                [
                    'ftaxi_driver_id' =>
                        $ftaxiDriverId,
                ],

                [
                    'id_number' =>
                        $idNumber !== null && $idNumber !== ''
                            ? $idNumber
                            : ($existingDriver->id_number ?? null),

                    'name' =>
                        $name !== ''
                            ? $name
                            : ($existingDriver->name ?? 'Unknown Driver'),

                    'phone' =>
                        !empty($phone)
                            ? $phone
                            : ($existingDriver->phone ?? null),

                    'vehicle_number' =>
                        !empty($vehicleNumber)
                            ? $vehicleNumber
                            : ($existingDriver->vehicle_number ?? null),

                    'vehicle_name' =>
                        !empty($vehicleName)
                            ? $vehicleName
                            : ($existingDriver->vehicle_name ?? null),

                    'pending_amount' =>
                        $pendingAmount,

                    'status' =>
                        $status !== ''
                            ? $status
                            : ($existingDriver->status ?? 'active'),
                ]

            );


            $count++;
        }


        /*
        |--------------------------------------------------------------------------
        | Sync Complete
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('drivers.index')
            ->with(
                'success',
                $count .
                ' F-Taxi drivers synced successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Create Driver
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view(
            'drivers.create'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store Driver
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'name' =>
                'required|string|max:255',

            'phone' =>
                'nullable|string|max:20',

            'vehicle_number' =>
                'nullable|string|max:50',

            'vehicle_name' =>
                'nullable|string|max:100',

            'pending_amount' =>
                'nullable|numeric|min:0',

            'status' =>
                'required|string|max:50',

        ]);


        Driver::create([

            'id_number' =>
                $request->id_number,

            'name' =>
                $request->name,

            'phone' =>
                $request->phone,

            'vehicle_number' =>
                $request->vehicle_number,

            'vehicle_name' =>
                $request->vehicle_name,

            'pending_amount' =>
                $request->pending_amount ?? 0,

            'status' =>
                $request->status,

        ]);


        return redirect()
            ->route('drivers.index')
            ->with(
                'success',
                'Driver added successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Driver
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $driver =
            Driver::findOrFail($id);

        return view(
            'drivers.edit',
            compact('driver')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Driver
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {

        $driver =
            Driver::findOrFail($id);


        $request->validate([

            'name' =>
                'required|string|max:255',

            'phone' =>
                'nullable|string|max:20',

            'vehicle_number' =>
                'nullable|string|max:50',

            'vehicle_name' =>
                'nullable|string|max:100',

            'pending_amount' =>
                'nullable|numeric|min:0',

            'status' =>
                'required|string|max:50',

        ]);


        $driver->update([

            'id_number' =>
                $request->id_number,

            'name' =>
                $request->name,

            'phone' =>
                $request->phone,

            'vehicle_number' =>
                $request->vehicle_number,

            'vehicle_name' =>
                $request->vehicle_name,

            'pending_amount' =>
                $request->pending_amount ?? 0,

            'status' =>
                $request->status,

        ]);


        return redirect()
            ->route('drivers.index')
            ->with(
                'success',
                'Driver updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Driver
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $driver =
            Driver::findOrFail($id);

        $driver->delete();

        return redirect()
            ->route('drivers.index')
            ->with(
                'success',
                'Driver deleted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Driver Ledger
    |--------------------------------------------------------------------------
    */

    public function ledger($id)
    {
        $driver =
            Driver::findOrFail($id);


        $incomes =
            Income::where(
                'driver_id',
                $driver->id
            )
            ->orderBy(
                'id',
                'desc'
            )
            ->get();


        $totalPaid =
            $incomes->sum('amount');


        return view(
            'drivers.ledger',
            compact(
                'driver',
                'incomes',
                'totalPaid'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Record Driver Payment
    |--------------------------------------------------------------------------
    */

    public function recordPayment(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'nullable|date',
            'description' => 'nullable|string|max:255',
        ]);

        $pendingAmount = (float) ($driver->pending_amount ?? 0);
        $paymentAmount = (float) $validated['amount'];

        if ($pendingAmount <= 0) {
            return redirect()
                ->route('drivers.ledger', $driver->id)
                ->with('error', 'There is no pending amount for this driver.');
        }

        if ($paymentAmount > $pendingAmount) {
            return redirect()
                ->route('drivers.ledger', $driver->id)
                ->with(
                    'error',
                    'Payment cannot be greater than the current pending amount of ₹' .
                    number_format($pendingAmount, 2)
                );
        }

        $paymentDate = $validated['payment_date']
            ?? now()->format('Y-m-d');

        Income::create([
            'driver_id' => $driver->id,
            'amount' => $paymentAmount,
            'description' => $validated['description'] ?? 'Driver payment',
            'income_date' => $paymentDate,
        ]);

        $driver->pending_amount = round(
            max(0, $pendingAmount - $paymentAmount),
            2
        );

        $driver->save();

        return redirect()
            ->route('drivers.ledger', $driver->id)
            ->with('success', 'Payment recorded successfully.');
    }

}
