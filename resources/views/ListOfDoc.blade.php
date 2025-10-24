@extends('layouts.app')

@section('title')

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if($doctors->count())
    @foreach($doctors as $doctor)
    
        <table class="table table-bordered mb-4">
            <thead class="table-primary">
                <tr>
                    <th style="background-color: #74C0FC" colspan="2" class="text-center">
                        Dr {{ $doctor->name }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Contact:</strong></td>
                    <td>{{ $doctor->phone }}</td>
                </tr>
                <tr>
                    <td><strong>Address:</strong></td>
                    <td>{{ $doctor->address }}</td>
                </tr>
                <tr>
                    <td><strong>Reservation Price:</strong></td>
                    <td>{{ $doctor->price }} L.E</td>
                </tr>
                <tr>
                    <td><strong>Appointments:</strong></td>
                    <td>
                        @if($doctor->appointments->count())
                            <ul class="list-unstyled mb-2">
                                @foreach($doctor->appointments as $appointment)
                                    <li>
                                     <strong> {{ $appointment->day}}</strong> </br>
                                        <strong>From:</strong>
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $appointment->time_start)->format('g:i A') }}
                                        <strong>To:</strong>
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $appointment->time_end)->format('g:i A') }} 
                                    </li>
                                @endforeach
                            
                            </ul>
                        @else
                            <span class="text-muted">No appointments available</span>
                        @endif  
                        @if( Auth::check() && Auth::user()->is_admin)
                        <div colspan="2" class="text-end" style="direction: ltr; text-align: right;">
                            <a  href="{{ route('doctor.edit', $doctor->id) }}" class="btn btn-sm" style="background-color:#74C0FC;">Edit</a>
                            <form action="{{ route('doctor.destroy', $doctor->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>       
                        </div>
                        @else
                        @auth
                        <div colspan="2" class="text-end" style="direction: ltr; text-align: right;">
                            <a href="{{route('makeAppointment', ['doctor' => $doctor->id])}}" class="btn btn-sm" style="background-color:#74C0FC;">Make An Appointment</a>           
                        </div>
                        @endauth
                        @endif
                    </td>     
                    </tr>
             </tbody>
        </table>
    @endforeach
@else
    <p>No doctors found.</p>
@endif
<div class="text-center mt-4">
    {{ $doctors->links('pagination::bootstrap-5') }}
</div>

@endsection
