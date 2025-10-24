
@extends('layouts.app')

@section('title')

@section('content')

<div class="container">
     {{-- اختيار الدكتور --}}
     <div class="mb-4">
        <label for="doctorSelect" class="form-label"><strong>Select Doctor:</strong></label>
        <select id="doctorSelect" class="form-select">
            <option value="">-- Choose a Doctor --</option>
            @foreach($doctors as $d)
                <option value="{{ $d->id }}"
                        data-phone="{{ $d->phone }}"
                        data-address="{{ $d->address }}"
                        data-price="{{ $d->price }}"
                        data-appointments='@json($d->appointments)'
                        {{ isset($doctor) && $doctor->id == $d->id ? 'selected' : '' }}>
                    Dr {{ $d->name }}
                </option>
            @endforeach
        </select>
        
    </div>
    {{-- جدول التفاصيل --}}
    <table class="table table-bordered mb-4 d-none" id="doctorDetails">
        <thead class="table-primary">
            <tr>
                <th style="background-color: #74C0FC" colspan="2" class="text-center" id="doctorName">
                    <!-- Doctor name will appear here -->
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Contact:</strong></td>
                <td id="doctorPhone"></td>
            </tr>
            <tr>
                <td><strong>Address:</strong></td>
                <td id="doctorAddress"></td>
            </tr>
            <tr>
                <td><strong>Reservation Price:</strong></td>
                <td id="doctorPrice"></td>
            </tr>
            <tr>
                <td><strong>Appointments:</strong></td>
                <td id="doctorAppointments">
                    <label for="appointmentSelect" class="form-label mt-3"><strong>Available Appointments:</strong></label>
                    <select id="appointmentSelect" class="form-select">
                        <option value="">-- Choose an Appointment --</option>
                    </select>
                    @if( Auth::check() && Auth::user()->is_admin)
                    <div colspan="2" class="text-end" style="direction: ltr; text-align: right;">
                        <a  href="{{ route('doctor.edit', $doctor->id) }}" class="btn btn-sm" style="background-color:#74C0FC;">Edit</a>
                    </div>    
                    @elseif( Auth::check() && Auth::user())
                    <form action="{{ route('reservations.store') }}" method="POST" id="reservationForm">
                        @csrf
                        <input type="hidden" name="doctor_id" id="doctor_id"> 
                        <input type="hidden" name="appointment_id" id="appointment_id">
                        <div colspan="2" class="text-end" style="direction: ltr; text-align: right;">
                            <button type="submit" class="btn btn-sm" style="background-color:#74C0FC;">Confirm</button>         
                        </div>
                    </form>
                    
                    @else
                    <div colspan="2" class="text-end" style="direction: ltr; text-align: right;">
                        <a href="{{route('login')}}" class="btn btn-sm" style="background-color:#74C0FC;">Login</a>           
                    </div>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- JavaScript --}}
<script>
    document.getElementById("doctorSelect").addEventListener("change", function() {
    let selected = this.options[this.selectedIndex];
    let detailsTable = document.getElementById("doctorDetails");
    let doctorIdField = document.getElementById("doctor_id");

    if (this.value === "") {
        detailsTable.classList.add("d-none");
        return;
    }

    // حط doctor_id في الفورم
    doctorIdField.value = this.value;

    // باقي الكود زي ما هو ...
    document.getElementById("doctorName").innerText = selected.text;
    document.getElementById("doctorPhone").innerText = selected.getAttribute("data-phone");
    document.getElementById("doctorAddress").innerText = selected.getAttribute("data-address");
    document.getElementById("doctorPrice").innerText = selected.getAttribute("data-price") + " L.E";

    let doctorId = this.value;
    let appointmentSelect = document.getElementById('appointmentSelect');

    appointmentSelect.innerHTML = '<option value="">-- Choose an Appointment --</option>';

    if (doctorId) {
        fetch(`/doctor/${doctorId}/appointments`)
            .then(response => response.json())
            .then(data => {
        data.forEach(app => {
            let option = document.createElement('option');
            option.value = app.id;

            // ✅ Now we use the ISO date directly
            let date = new Date(`${app.day}T00:00:00`);

            // Format date nicely
            let formattedDate = date.toLocaleDateString('en-US', {
                weekday: 'short', // e.g., Wed
                month: 'short',   // e.g., Oct
                day: 'numeric'    // e.g., 22
            });

            option.text = `${formattedDate} | From: ${app.time_start} To: ${app.time_end}`;
            appointmentSelect.appendChild(option);
        });
    })


            .catch(error => console.error('Error:', error));
    }

    detailsTable.classList.remove("d-none");
});

// لما المستخدم يختار appointment، نحطها في الفورم
document.getElementById("appointmentSelect").addEventListener("change", function() {
    document.getElementById("appointment_id").value = this.value;
});

document.addEventListener('DOMContentLoaded', function () {
    let doctorSelect = document.getElementById("doctorSelect");
    if (doctorSelect.value !== "") {
        // Trigger change to display doctor info automatically
        doctorSelect.dispatchEvent(new Event('change'));
    }
});

</script>

@endsection