<h1>Add Driver</h1>


<a href="{{ route('drivers.index') }}">
Back to Drivers
</a>


<br><br>


<form method="POST" action="{{ route('drivers.store') }}">

@csrf


<label>
Driver Name
</label>

<br>

<input type="text" name="name">


<br><br>


<label>
Phone
</label>

<br>

<input type="text" name="phone">


<br><br>


<label>
Vehicle Number
</label>

<br>

<input type="text" name="vehicle_number">


<br><br>


<button type="submit">
Save Driver
</button>


</form>