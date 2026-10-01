<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Driver - F-Taxi Accounts</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f6fa;
            color: #111827;
        }

        .page {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header {
            background: linear-gradient(
                135deg,
                #111827,
                #1e293b
            );

            color: white;
            border-radius: 22px;
            padding: 30px 32px;
            margin-bottom: 25px;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            font-size: 30px;
        }

        .header p {
            margin: 8px 0 0;
            color: #cbd5e1;
            font-size: 14px;
        }

        .back-btn {
            text-decoration: none;
            color: white;
            background: #334155;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
        }

        .back-btn:hover {
            background: #475569;
        }

        .card {
            background: white;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.08);
        }

        .card-title {
            font-size: 21px;
            font-weight: 700;
            margin-bottom: 25px;
            color: #111827;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 22px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
        }

        input,
        select {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 15px;
            outline: none;
            background: white;
        }

        input:focus,
        select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
        }

        input[readonly] {
            background: #f8fafc;
            color: #64748b;
        }

        .error {
            margin-top: 6px;
            color: #dc2626;
            font-size: 13px;
        }

        .buttons {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn {
            border: none;
            padding: 13px 22px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-cancel {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-cancel:hover {
            background: #d1d5db;
        }

        .btn-save {
            background: #2563eb;
            color: white;
        }

        .btn-save:hover {
            background: #1d4ed8;
        }

        .info-box {
            margin-bottom: 25px;
            padding: 15px 18px;
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            border-radius: 8px;
            color: #1e3a8a;
            font-size: 14px;
        }

        @media (max-width: 700px) {

            .page {
                margin: 20px auto;
            }

            .header {
                padding: 22px;
                flex-direction: column;
                align-items: flex-start;
                gap: 18px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>

<div class="page">

    <!-- Header -->
    <div class="header">

        <div>
            <h1>Edit Driver</h1>
            <p>Update driver information</p>
        </div>

        <a
            href="{{ route('drivers.index') }}"
            class="back-btn"
        >
            ← Back to Drivers
        </a>

    </div>


    <!-- Form Card -->
    <div class="card">

        <div class="card-title">
            Driver Information
        </div>


        <div class="info-box">
            Update the driver's details below and click
            <strong>Save Changes</strong>.
        </div>


        <form
            action="{{ route('drivers.update', $driver->id) }}"
            method="POST"
        >

            @csrf

            @method('PUT')


            <div class="form-grid">

                <!-- F-Taxi Driver ID -->
                <div class="form-group">

                    <label>
                        F-Taxi Driver ID
                    </label>

                    <input
                        type="text"
                        value="{{ $driver->ftaxi_driver_id ?? '-' }}"
                        readonly
                    >

                </div>


                <!-- Driver ID -->
                <div class="form-group">

                    <label for="id_number">
                        Driver ID
                    </label>

                    <input
                        type="text"
                        name="id_number"
                        id="id_number"
                        value="{{ old('id_number', $driver->id_number) }}"
                    >

                    @error('id_number')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <!-- Driver Name -->
                <div class="form-group">

                    <label for="name">
                        Driver Name *
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $driver->name) }}"
                        required
                    >

                    @error('name')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <!-- Mobile Number -->
                <div class="form-group">

                    <label for="phone">
                        Mobile Number
                    </label>

                    <input
                        type="text"
                        name="phone"
                        id="phone"
                        value="{{ old('phone', $driver->phone) }}"
                    >

                    @error('phone')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <!-- Vehicle Number -->
                <div class="form-group">

                    <label for="vehicle_number">
                        Vehicle Number
                    </label>

                    <input
                        type="text"
                        name="vehicle_number"
                        id="vehicle_number"
                        value="{{ old('vehicle_number', $driver->vehicle_number) }}"
                        placeholder="Enter vehicle number"
                    >

                    @error('vehicle_number')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <!-- Vehicle Name -->
                <div class="form-group">

                    <label for="vehicle_name">
                        Vehicle Name
                    </label>

                    <input
                        type="text"
                        name="vehicle_name"
                        id="vehicle_name"
                        value="{{ old('vehicle_name', $driver->vehicle_name) }}"
                        placeholder="Enter vehicle name"
                    >

                    @error('vehicle_name')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <!-- Status -->
                <div class="form-group">

                    <label for="status">
                        Status *
                    </label>

                    <select
                        name="status"
                        id="status"
                        required
                    >

                        <option
                            value="active"
                            {{ old('status', $driver->status) == 'active' ? 'selected' : '' }}
                        >
                            Active
                        </option>

                        <option
                            value="inactive"
                            {{ old('status', $driver->status) == 'inactive' ? 'selected' : '' }}
                        >
                            Inactive
                        </option>

                    </select>

                    @error('status')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            <!-- Buttons -->
            <div class="buttons">

                <a
                    href="{{ route('drivers.index') }}"
                    class="btn btn-cancel"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn btn-save"
                >
                    Save Changes
                </button>

            </div>

        </form>

    </div>

</div>

</body>
</html>