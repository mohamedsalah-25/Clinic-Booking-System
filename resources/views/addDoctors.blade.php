
@extends('layouts.app')

@section('title', isset($doctor) ? 'Edit Doctor' : 'Add Doctor')
@section('content')

<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <a href="/" class="navbar-brand" style="display:flex; align-items:center; gap:8px;">
                <img src="images/D.jpg" alt="Logo" style="width:40px; height:40px;"><span>entist clinic</span>
              </a>
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ isset($doctor) ? route('doctor.update', $doctor->id) : route('doctors.store') }}"
             enctype="multipart/form-data">
            @csrf
            @if(isset($doctor))
                @method('PUT')
            @endif

            <div>
                <x-label for="name" value="{{ __('Name of Doctor') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" : value="{{ old('name', $doctor->name ?? '') }}" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="phone" value="{{ __('Phone') }}" />
                <x-input id="phone" class="block mt-1 w-full" type="tel" name="phone" : value="{{ old('phone', $doctor->phone ?? '') }}" pattern="[0-9]+"  required autocomplete="phone" />
            </div>

            <div class="mt-4">
                <x-label for="address" value="{{ __('Address') }}" />
                <x-input id="address" class="block mt-1 w-full" type="text" name="address" required  value="{{ old('address', $doctor->address ?? '') }}"  />
            </div>

            <div class="mt-4">
                <x-label for="price" value="{{ __('Price') }}" />
                <x-input id="price" class="block mt-1 w-full" type="number" name="price" required  value="{{ old('price', $doctor->price ?? '') }}"/>
            </div>

            <div class="mt-4">
                <x-label for="appointments" value="{{ __('Appointments') }}" style="font-weight: bold" />
            
                <div id="appointments-wrapper">
                    @if(isset($doctor) && $doctor->appointments->count() > 0)
                        @foreach($doctor->appointments as $index => $appointment)
                            <div class="appointment-item border p-3 rounded mt-3">
                                <input type="hidden" name="appointments[{{ $index }}][id]" value="{{ $appointment->id }}">
            
                                <div class="mt-2">
                                    <x-label for="day" value="{{ __('Day') }}" />
                                    <select name="appointments[{{ $index }}][day]" class="block mt-1 w-full" required>
                                        @foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)
                                            <option value="{{ $day }}" {{ $appointment->day == $day ? 'selected' : '' }}>
                                                {{ ucfirst($day) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
            
                                <div class="mt-2">
                                    <x-label value="{{ __('Start Time') }}" />
                                    <x-input class="block mt-1 w-full" type="time" 
                                        name="appointments[{{ $index }}][time_start]" 
                                        value="{{ $appointment->time_start }}" required />
                                </div>
            
                                <div class="mt-2">
                                    <x-label value="{{ __('End Time') }}" />
                                    <x-input class="block mt-1 w-full" type="time" 
                                        name="appointments[{{ $index }}][time_end]" 
                                        value="{{ $appointment->time_end }}" required />
                                </div>
            
                                <button type="button" class="remove-appointment mt-3 bg-red-500 text-white px-3 py-1 rounded">Remove</button>
                            </div>
                        @endforeach
                    @else
                        {{-- If no appointments yet, show one blank --}}
                        <div class="appointment-item border p-3 rounded mt-3">
                            <div class="mt-2">
                                <x-label for="day" value="{{ __('Day') }}" />
                                <select name="appointments[0][day]" class="block mt-1 w-full" required>
                                    <option value="monday">Monday</option>
                                    <option value="tuesday">Tuesday</option>
                                    <option value="wednesday">Wednesday</option>
                                    <option value="thursday">Thursday</option>
                                    <option value="friday">Friday</option>
                                    <option value="saturday">Saturday</option>
                                    <option value="sunday">Sunday</option>
                                </select>
                            </div>
            
                            <div class="mt-2">
                                <x-label value="{{ __('Start Time') }}" />
                                <x-input class="block mt-1 w-full" type="time" name="appointments[0][time_start]" required />
                            </div>
            
                            <div class="mt-2">
                                <x-label value="{{ __('End Time') }}" />
                                <x-input class="block mt-1 w-full" type="time" name="appointments[0][time_end]" required />
                            </div>
                        </div>
                    @endif
                </div>
            
                {{-- Add more --}}
                <button type="button" id="add-appointment" class="mt-3 bg-blue-500 text-white px-4 py-2 rounded">
                    + Add More Appointments
                </button>
            </div>

            <script>
                let index = {{ isset($doctor) ? $doctor->appointments->count() : 1 }};
                
                document.getElementById('add-appointment').addEventListener('click', function(e) {
                    e.preventDefault();
                
                    const wrapper = document.getElementById('appointments-wrapper');
                    const newItem = document.createElement('div');
                    newItem.classList.add('appointment-item', 'border', 'p-3', 'rounded', 'mt-3');
                
                    newItem.innerHTML = `
                        <div class="mt-2">
                            <label class="block font-bold">Day</label>
                            <select name="appointments[${index}][day]" class="block mt-1 w-full" required>
                                <option value="monday">Monday</option>
                                <option value="tuesday">Tuesday</option>
                                <option value="wednesday">Wednesday</option>
                                <option value="thursday">Thursday</option>
                                <option value="friday">Friday</option>
                                <option value="saturday">Saturday</option>
                                <option value="sunday">Sunday</option>
                            </select>
                        </div>
                
                        <div class="mt-2">
                            <label class="block font-bold">Start Time</label>
                            <input class="block mt-1 w-full" type="time" name="appointments[${index}][time_start]" required />
                        </div>
                
                        <div class="mt-2">
                            <label class="block font-bold">End Time</label>
                            <input class="block mt-1 w-full" type="time" name="appointments[${index}][time_end]" required />
                        </div>
                
                        <button type="button" class="remove-appointment mt-3 bg-red-500 text-white px-3 py-1 rounded">Remove</button>
                    `;
                    wrapper.appendChild(newItem);
                    index++;
                });
                
                // Remove appointment dynamically
                document.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-appointment')) {
                        e.target.closest('.appointment-item').remove();
                    }
                });
                </script>
                
            
            <script>
                let index = 1;
            
                document.getElementById('add-appointment').addEventListener('click', function (e) {
                    e.preventDefault(); // امنع الإرسال
            
                    let wrapper = document.getElementById('appointments-wrapper');
                    let newItem = document.querySelector('.appointment-item').cloneNode(true);
            
                    // عدل أسماء الحقول عشان تبقى [index]
                    newItem.querySelectorAll('select, input').forEach(el => {
                        let name = el.getAttribute('name');
                        el.setAttribute('name', name.replace(/\d+/, index));
                        el.value = ''; // reset value
                    });
            
                    wrapper.appendChild(newItem);
                    index++;
                });
            </script>
            

            <script>
                let index = 1;
            
                document.getElementById('add-appointment').addEventListener('click', function (e) {
                    e.preventDefault();
            
                    let wrapper = document.getElementById('appointments-wrapper');
                    let newItem = document.querySelector('.appointment-item').cloneNode(true);
            
                    // عدل أسماء الحقول بشكل صحيح
                    newItem.querySelectorAll('select, input').forEach(el => {
                        let name = el.getAttribute('name'); 
                        if (name) {
                            // استبدل الـ [0] بالـ [index] الجديد
                            name = name.replace(/\[\d+\]/, `[${index}]`);
                            el.setAttribute('name', name);
                            el.value = ''; // reset value
                        }
                    });
            
                    wrapper.appendChild(newItem);
                    index++;
                });
            </script>
            
            

            <div class="mt-4">
                <x-label for="image" value="{{ __('Image') }}" />
                <x-input id="image" class="block mt-1 w-full" type="file" name="image"  autocomplete="image" />
            
                @if(isset($doctor->image))
                <img src="{{ asset('storage/' . $doctor->image) }}" alt="Doctor Image" style="width:100px; margin-top:10px;">
            @endif
            </div>

            

            <div class="flex items-center justify-end mt-4">

                <x-button class="ms-4">
                    {{ isset($doctor) ? __('Update') : __('Add') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
@endsection