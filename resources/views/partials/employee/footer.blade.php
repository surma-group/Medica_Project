<footer class="main-footer text-center">
    <div class="footer-left">
        &copy; {{ date('Y') }} SURMA GROUP
    </div>
</footer>

<!-- General JS Scripts -->
<script src="{{ asset('admin/assets/js/app.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/scripts.js') }}"></script>
<script src="{{ asset('admin/assets/js/custom.js') }}"></script>
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
