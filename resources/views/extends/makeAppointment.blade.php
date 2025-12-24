 <!-- MAKE AN APPOINTMENT -->
 <section id="appointment" data-stellar-background-ratio="3">
    <div class="container">
         <div class="row">

              <div class="col-md-6 col-sm-6">
                   <img src="images/appointment-image.jpg" class="img-responsive" alt="">
              </div>

              <div class="col-md-6 col-sm-6">
                   <!-- CONTACT FORM HERE -->
                   
                        <!-- SECTION TITLE -->
                        <div class="section-title wow fadeInUp" data-wow-delay="0.4s">
                            <h2>Make an appointment</h2>
                       </div>
                        <p style="margin-bottom: 40px;">We have a group of the best and most qualified dentists in Alexandria Governorate who strive to provide the patient with the best possible care.
                        </p>
                        @if( Auth::check() && Auth::user()->is_admin)
                        <form id="appointment-form" role="form"
                        action="{{ isset($doctor) ? route('makeAppointment', ['doctor' => $doctor->id]) : route('reservation') }}"
                        method="GET">
                  
                         @csrf
                             <div class="col-md-12 col-sm-12">               
                                  <button type="submit" class="form-control" id="cf-submit" name="submit">Make an appointment</button>
                                </div>
                        @else
                        <form id="appointment-form" role="form"
                        action="{{ isset($doctor) ? route('makeAppointment', ['doctor' => $doctor->id]) : route('makeAppointment') }}"
                        method="GET">
                  
                         @csrf
                             <div class="col-md-12 col-sm-12">               
                                  <button type="submit" class="form-control" id="cf-submit" name="submit">Make an appointment</button>
                                </div>
                         @endif       
                        </div>
                  </form>
              </div>

         </div>
    </div>
</section>