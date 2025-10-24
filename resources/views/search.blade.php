@extends('layouts.app')

@section('title')

@section('content')

    <main class="container mt-4">
        <h3>Search Results for: "{{  $query }}"</h3>

        <hr>
        <h4>Doctors</h4>
        @if($doctors->count())
            @foreach($doctors as $doctor)
                <div class="card mb-2">
                    <div class="card-body">
                        <a href="{{ route('makeAppointment', ['doctor' => $doctor->id]) }}" 
                            >{{ $doctor->name }}</a>
                        <p>{{ Str::limit($doctor->phone, 100) }}</p>
                        <p>{{ $doctor->address }}</p>
                    </div>
                </div>
            @endforeach
        @else
            <p>No Doctors found.</p>
        @endif

            <!-- Pagination Links -->
        <div class="d-flex justify-content-center mt-4">
        {{ $doctors->links('pagination::bootstrap-5') }}
        </div>
    </main>
    @endsection
