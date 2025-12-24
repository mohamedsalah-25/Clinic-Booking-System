@extends('layouts.app')

@section('title')

@section('content')
<div class="container">
    @if( Auth::check() && Auth::user()->is_admin)
    <h3>All Reservations</h3>
    @else
    <h3>Your Reservations</h3>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Doctor</th>
                <th>Appointment</th>
                <th>Statue</th>
                @if(Auth::check() && Auth::user()->is_admin)
                    <th>User</th>                   
                    <th>Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($reservations as $reservation)
                <tr>
                    <td>
                        {{ optional($reservation->doctor)->name ?? 'N/A' }}
                    </td>
                    <td>
                        @if($reservation->appointment)
                        <strong>{{ \Carbon\Carbon::parse($reservation->appointment->day)->format('D, M j') }}</strong>
                    </br>
                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $reservation->appointment->time_start)->format('g:i A') }}
                        -
                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $reservation->appointment->time_end)->format('g:i A') }}
                        @else
                        N/A
                    @endif
                    </td>
                    <td>
                        @if($reservation->status == 'confirmed')
                            <span class="badge" style="background-color: #28a745; color: white;">Confirmed</span>
                        @elseif($reservation->status == 'cancelled')
                            <span class="badge" style="background-color: #dc3545; color: white;">Cancelled</span>
                        @else
                            <span class="badge" style="background-color: #525150; color: white;">{{ ucfirst($reservation->status) }}</span>
                        @endif
                    </td>
                    
                    
                    
                    @if(Auth::check() && Auth::user()->is_admin)
                    <td>
                        <a href="{{ route('reservation',  ['user_id' =>  $reservation->user->id]) }}" 
                            > 
                        {{ $reservation->user->name ?? 'N/A' }}</a></td>
                    <td>
                        @if($reservation->status === 'pending')
                        {{-- Confirm Button --}}
                        <form action="{{ route('reservations.confirm', $reservation->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-sm">Confirm</button>
                        </form>
                
                        {{-- Cancel Button --}}
                        <form action="{{ route('reservations.cancel', $reservation->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                        </form>
                    @endif
                    </td>
                @endif
                </tr>
            @empty
                <tr><td colspan="3" class="text-center">No Reservations Found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="text-center mt-4">
    {{ $reservations->links('pagination::bootstrap-5') }}
</div>
@endsection
