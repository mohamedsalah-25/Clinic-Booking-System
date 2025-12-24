@extends('layouts.app')

@section('title')

@section('content')
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center;">
    <h3 style="margin-top:10px;">All Users</h3>
        <form class="nav-link form-inline mt-2 mt-md-0" style="margin-top:0px;"action="{{ route('AllUsers') }}" method="GET">
          <div class="input-group">
            <input type="text" class="form-control" name="q" placeholder="Search" aria-label="Search"/>
            <div class="input-group-append">
              <span class="input-group-text">
                <i class="mdi mdi-magnify"></i>
              </span>
            </div>
          </div>
        </form>
</div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User</th>
                <th>Phone</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>
                        <a href="{{ route('reservation',  ['user_id' => $user->id]) }}" 
                            >{{ $user->name }}
                        </a>
                    </td>
                    <td>
                        {{ $user->phone  }}
                    </td>
                    <td>
                        <form action="{{ route('user.delete', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>   
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center">No Reservations Found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
