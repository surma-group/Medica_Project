<footer class="main-footer text-center">
    <div class="footer-left">
        &copy; {{ date('Y') }} SURMA GROUP
    </div>
</footer>

<!-- General JS Scripts -->
<script src="{{ asset('admin/assets/js/app.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/scripts.js') }}"></script>
<script src="{{ asset('admin/assets/js/custom.js') }}"></script>
<script src="{{ asset('admin/assets/bundles/fullcalendar/index.global.min.js') }}"></script>

<!-- DataTables JS & Buttons -->

<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>



@stack('scripts')  
<!-- Select2 JS (global) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            placeholder: "Select an option",
            allowClear: true
        });
    });
</script>
