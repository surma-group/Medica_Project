@extends('layouts.admin')

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Calendar</h4>
                    </div>
                    <div class="card-body">
                        <div class="fc-overflow">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {

        let calendarEl = document.getElementById("calendar");

        let calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                left: "prevYear,prev,next,nextYear today",
                center: "title",
                right: "dayGridMonth,dayGridWeek,dayGridDay",
            },

            initialView: "dayGridMonth",
            navLinks: true,
            editable: false,
            dayMaxEvents: true,

            events: {
                url: "{{ route('admin.holiday.calendar.events') }}",
                method: "GET",
                failure: function() {
                    alert("There was an error while fetching holidays!");
                }
            }
        });

        calendar.render();
    });
</script>

@endpush