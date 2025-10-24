<section id="team" data-stellar-background-ratio="1">
    <div class="container">
         <div class="row">

              <div class="col-md-6 col-sm-6">
                   <div class="about-info">
                        <h2 class="wow fadeInUp" data-wow-delay="0.1s">Our Top Doctors</h2>
                   </div>
              </div>

              <div class="clearfix"></div>
              @foreach($doctors as $doctor)
              <div class="col-md-4 col-sm-6">
                   <div class="team-thumb wow fadeInUp" data-wow-delay="0.2s">
                    @if($doctor->image)
                        <img  src="{{ asset('storage/' . $doctor->image ) }} "
                        class="img-fluid rounded" alt=""  style="height: 300px; width: 100%;">
                    @else 
                    <img  src="{{ asset('images/Unknown profile.webp' ) }} "
                        class="img-fluid rounded" alt="" style="height: 300px; width: 100%; ">
                    @endif       
                             <div class="team-info">
                                  <h3> {{$doctor->name}}</h3>
                                  <div class="team-contact-info">
                                       <p><i class="fa fa-phone"></i> {{ $doctor->phone}}</p>
                                       <p><strong>Address</strong> {{ $doctor->address}}</p>
                                       <p><strong>Price</strong> {{$doctor->price}}</p>    
                                  </div>
                             </div>
                             <div class="text-center mb-5" style="margin-top: 40px;">
                              <a href="{{ route('makeAppointment', ['doctor' => $doctor->id]) }}" 
                                 class="btn btn-primary">View Profile</a>
                              </div>     
                   </div>
              </div>
              @endforeach              
         </div>
    </div>
</section>
