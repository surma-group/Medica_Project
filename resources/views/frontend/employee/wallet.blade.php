@extends('layouts.employee')

@section('content')
<div id="ajaxAlertContainer" style="position: fixed; top: 20px; right: 20px; z-index: 1055; width: 350px;"></div>

<section class="section">
    <div class="section-body">

        {{-- Wallet Balance Card --}}
        <div class="row">
            <div class="col-12">
                <div class="card text-white" style="background: linear-gradient(135deg,#e2136e,#ff5a8a)">
                    <div class="card-body">
                        <h6 class="mb-2">My Accounts</h6>
                        <h2 class="mb-1">
                            ৳ {{ number_format($balance, 2) }}
                        </h2>
                        <small>Available Balance</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Wallet Actions --}}
        <div class="row text-center mt-3">
            <div class="col-4">
                <div class="card">
                    <div class="card-body p-3">
                        <i class="fas fa-exchange-alt text-success fa-2x"></i>
                        <p class="mb-0 mt-2">Balance Transfer</p>
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="card cursor-pointer"
                    data-bs-toggle="modal"
                    data-bs-target="#withdrawModal">
                    <div class="card-body p-3 text-center">
                        <i class="fas fa-wallet text-danger fa-2x"></i>
                        <p class="mb-0 mt-2">Withdraw</p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <a href="{{ route('employee.statement') }}" class="text-decoration-none">
                    <div class="card cursor-pointer">
                        <div class="card-body p-3 text-center">
                            <i class="fas fa-file-invoice text-danger fa-2x"></i>
                            <p class="mb-0 mt-2">Statement</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-4">
                <a href="{{ route('employee.withdraw.requests') }}" class="text-decoration-none">
                    <div class="card cursor-pointer">
                        <div class="card-body p-3 text-center">
                            <i class="fas fa-list-alt text-success fa-2x"></i>
                            <p class="mb-0 mt-2">Withdraw Requests</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body p-3">
                        <i class="fas fa-piggy-bank text-success fa-2x"></i>
                        <p class="mb-0 mt-2">provident fund</p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body p-3">
                        <i class="fas fa-hand-holding-usd text-success fa-2x"></i>
                        <p class="mb-0 mt-2">Loan</p>
                    </div>
                </div>
            </div>
          
        </div>

        {{-- Recent Transactions --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Recent Transactions</h4>
                    </div>

                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">

                            @forelse($entries as $entry)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $entry->note ?? 'Wallet Transaction' }}</strong><br>
                                    <small class="text-muted">
                                        {{ $entry->created_at->format('d M Y, h:i A') }}
                                    </small>
                                </div>

                                <div class="text-end">
                                    @if($entry->credit>0)
                                    <span class="text-success fw-bold">
                                        +৳ {{ number_format($entry->credit, 2) }}
                                    </span>
                                    @else
                                    <span class="text-danger fw-bold">
                                        -৳ {{ number_format($entry->debit, 2) }}
                                    </span>
                                    @endif
                                </div>
                            </li>
                            @empty
                            <li class="list-group-item text-center">
                                No transactions found
                            </li>
                            @endforelse

                        </ul>
                    </div>

                    <div class="card-footer text-center">
                        <a href="{{ route('employee.statement') }}" class="text-primary">
                            View Full Statement
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<div class="modal fade" id="withdrawModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            {{-- Modal Header --}}
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-wallet text-danger"></i> Withdraw Money
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- Form --}}
            <form id="withdrawForm" action="{{ route('employee.withdraw.request') }}" method="POST">
                @csrf

                <div class="modal-body">

                    {{-- Accountant Search --}}
                    <div class="form-group mb-3">
                        <label>Accountant ID</label>
                        <div class="input-group">
                            <input type="text"
                                id="accountantId"
                                class="form-control"
                                placeholder="Enter accountant ID">
                            <button type="button" id="searchAccountantBtn" class="btn btn-primary">
                                Search
                            </button>
                        </div>
                    </div>

                    {{-- Accountant Info --}}
                    <div id="accountantInfo"
                        class="border rounded p-3 mb-3"
                        style="display: none; background: #f8f9fa;">
                        <input type="hidden"
                            name="accountant_id"
                            id="selectedAccountantId">

                        <p class="mb-1">
                            <strong>Name:</strong>
                            <span id="accName"></span>
                        </p>

                        <p class="mb-1">
                            <strong>Branch:</strong>
                            <span id="accBranch"></span>
                        </p>

                        <p class="mb-0">
                            <strong>Designation:</strong>
                            <span id="accDesignation"></span>
                        </p>
                    </div>

                    {{-- Amount --}}
                    <div class="form-group mb-3">
                        <label>Amount</label>
                        <input type="number"
                            name="amount"
                            class="form-control"
                            placeholder="Enter amount"
                            min="1"
                            required>
                    </div>

                    {{-- Withdraw Method --}}
                    <div class="form-group mb-3">
                        <label class="form-label me-3">Withdraw Method</label>
                        <br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="method" id="methodCash" value="{{ WITHDRAW_TYPE_CASH }}" checked required>
                            <label class="form-check-label" for="methodCash">Cash To Cash</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="method" id="methodBank" value="{{ WITHDRAW_TYPE_BANK }}" required>
                            <label class="form-check-label" for="methodBank">Bank To Bank</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="method" id="methodMobile" value="{{ WITHDRAW_TYPE_MOBILE_BANKING }}" required>
                            <label class="form-check-label" for="methodMobile">Mobile Banking To Cash</label>
                        </div>
                    </div>

                    {{-- Mobile Banking Type --}}
                    <div class="form-group mb-3" id="mobileBankingTypeField" style="display: none;">
                        <label>Mobile Banking Type</label>
                        <select class="form-control" name="mobile_banking_type" id="mobileBankingType">
                            <option value="">Select Mobile Banking Type</option>
                            <option value="{{ MOBILE_BANKING_TYPE_BKASH }}">bKash</option>
                            <option value="{{ MOBILE_BANKING_TYPE_NAGAD }}">Nagad</option>
                            <option value="{{ MOBILE_BANKING_TYPE_ROCKET }}">Rocket</option>
                        </select>
                    </div>

                    {{-- Mobile Banking Info --}}
                    <div id="mobileBankingInfo" class="border rounded p-3 mb-3" style="display: none; background: #f8f9fa;">
                        <p class="mb-1">
                            <strong>Mobile Banking Type:</strong>
                            <span id="mobileTypeText"></span>
                        </p>
                        <p class="mb-0">
                            <strong>Mobile Number:</strong>
                            <span id="mobileNumberText"></span>
                        </p>
                    </div>

                    {{-- Bank Info --}}
                    <div id="bankInfoDisplay" class="border rounded p-3 mb-3" style="display: none; background: #f8f9fa;">
                        <p class="mb-1">
                            <strong>Bank Name:</strong>
                            <span id="bankNameText"></span>
                        </p>
                        <p class="mb-1">
                            <strong>Branch:</strong>
                            <span id="bankBranchText"></span>
                        </p>
                        <p class="mb-0">
                            <strong>Account Number:</strong>
                            <span id="bankAccountText"></span>
                        </p>
                    </div>

                    {{-- Note --}}
                    <div class="form-group">
                        <label>Note (optional)</label>
                        <textarea class="form-control"
                            name="note"
                            rows="2"
                            placeholder="Withdraw note"></textarea>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                        class="btn btn-danger"
                        id="withdrawSubmitBtn"
                        disabled>
                        Withdraw
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const beneficiary = @json($beneficiary ?? null);

        const phoneField = document.getElementById('phoneNumberField');
        const mobileTypeField = document.getElementById('mobileBankingTypeField');
        const submitBtn = document.getElementById('withdrawSubmitBtn');

        /* ===============================
           Withdraw method → phone & mobile type toggle
        =============================== */
        const mobileInfo = document.getElementById('mobileBankingInfo');
        const bankInfo = document.getElementById('bankInfoDisplay');

        // Elements
        const mobileTypeSelect = document.getElementById('mobileBankingType');
        const mobileTypeText = document.getElementById('mobileTypeText');
        const mobileNumberText = document.getElementById('mobileNumberText');
        const bankNameText = document.getElementById('bankNameText');
        const bankBranchText = document.getElementById('bankBranchText');
        const bankAccountText = document.getElementById('bankAccountText');
        document.querySelectorAll('input[name="method"]').forEach(el => {
            el.addEventListener('change', function() {

                // Reset everything first
                mobileInfo.style.display = 'none';
                bankInfo.style.display = 'none';
                mobileTypeField.style.display = 'none';
                mobileTypeSelect.value = '';

                if (this.value === '{{ WITHDRAW_TYPE_MOBILE_BANKING }}') {
                    // Show only the Mobile Banking Type selector
                    mobileTypeField.style.display = 'block';
                } else if (this.value === '{{ WITHDRAW_TYPE_BANK }}') {
                    if (beneficiary && beneficiary.account_number) {
                        bankInfo.style.display = 'block';
                        bankNameText.innerText = beneficiary.bank_name;
                        bankBranchText.innerText = beneficiary.branch_name;
                        bankAccountText.innerText = beneficiary.account_number;
                    }
                }
            });
        });

        // Step 2: Show mobile number when type is selected
        mobileTypeSelect.addEventListener('change', function() {
            const selectedValue = this.value;

            // Hide if nothing selected or no beneficiary
            if (!selectedValue || !beneficiary) {
                mobileInfo.style.display = 'none';
                mobileTypeText.innerText = '';
                mobileNumberText.innerText = '';
                return;
            }

            // Map value to label & get number
            let typeLabel = '';
            let number = '';

            if (selectedValue === '{{ MOBILE_BANKING_TYPE_BKASH }}') {
                typeLabel = 'bKash';
                number = beneficiary.bkash_number || '';
            } else if (selectedValue === '{{ MOBILE_BANKING_TYPE_NAGAD }}') {
                typeLabel = 'Nagad';
                number = beneficiary.nagad_number || '';
            } else if (selectedValue === '{{ MOBILE_BANKING_TYPE_ROCKET }}') {
                typeLabel = 'Rocket';
                number = beneficiary.rocket_number || '';
            }

            // Show only if number exists
            if (number) {
                mobileInfo.style.display = 'block';
                mobileTypeText.innerText = typeLabel; // Show human-readable name
                mobileNumberText.innerText = number;
            } else {
                mobileInfo.style.display = 'none';
                mobileTypeText.innerText = '';
                mobileNumberText.innerText = '';
            }
        });

        // Trigger change on page load to handle default selection (Cash)
        document.querySelector('input[name="method"]:checked').dispatchEvent(new Event('change'));

        /* ===============================
           Accountant search
        =============================== */
        $('#searchAccountantBtn').on('click', function() {

            const accId = $('#accountantId').val().trim();

            if (!accId) {
                alert('Please enter Accountant ID');
                return;
            }

            // Disable button while searching
            $('#searchAccountantBtn').prop('disabled', true).text('Searching...');

            fetch(`/employee/accountant/search/${accId}`)
                .then(res => res.json())
                .then(data => {

                    if (!data.success) {
                        alert('Accountant not found');

                        $('#accountantInfo').hide();
                        $('#selectedAccountantId').val('');
                        submitBtn.disabled = true;
                        return;
                    }

                    // Fill accountant info
                    $('#accName').text(data.name);
                    $('#accBranch').text(data.branch);
                    $('#accDesignation').text(data.designation);
                    $('#selectedAccountantId').val(data.id);

                    $('#accountantInfo').slideDown(150);
                    submitBtn.disabled = false;
                })
                .catch(() => {
                    alert('Server error. Try again.');
                })
                .finally(() => {
                    $('#searchAccountantBtn').prop('disabled', false).text('Search');
                });
        });

        /* ===============================
           Withdraw form submit (AJAX)
        =============================== */
        $('#withdrawForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = $('#withdrawSubmitBtn')[0];

            // Basic validations
            const accountantId = $('#selectedAccountantId').val();
            const amount = form.find('input[name="amount"]').val();
            const method = form.find('input[name="method"]:checked').val();
            const mobileType = form.find('select[name="mobile_banking_type"]').val();

            // Check accountant
            if (!accountantId) {
                alert('Please search and select an accountant first.');
                return;
            }

            // Check amount
            if (!amount || parseFloat(amount) < 1) {
                alert('Please enter a valid amount.');
                return;
            }

            // Check withdraw method
            if (!method) {
                alert('Please select a withdraw method.');
                return;
            }

            // If mobile banking, check mobile type
            if (method === '{{ WITHDRAW_TYPE_MOBILE_BANKING }}' && !mobileType) {
                alert('Please select a mobile banking type.');
                return;
            }

            const url = form.attr('action');
            const data = form.serialize();

            submitBtn.disabled = true;

            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                success: function(response) {
                    $('#withdrawModal').modal('hide');
                    form.trigger('reset');
                    $('#mobileBankingTypeField').hide();
                    $('#mobileBankingInfo').hide();
                    $('#bankInfoDisplay').hide();
                    $('#accountantInfo').hide();
                    $('#selectedAccountantId').val('');
                    submitBtn.disabled = true;

                    const alertHtml = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> ${response.message || 'Withdrawal request submitted.'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;

                    $('#ajaxAlertContainer').append(alertHtml);

                    setTimeout(() => {
                        $('#ajaxAlertContainer .alert').first().alert('close');
                    }, 5000);
                },
                error: function(xhr) {
                    let message = 'Something went wrong!';
                    if (xhr.responseJSON?.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    }

                    const alertHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;

                    $('#ajaxAlertContainer').append(alertHtml);

                    setTimeout(() => {
                        $('#ajaxAlertContainer .alert').first().alert('close');
                    }, 7000);
                },
                complete: function() {
                    submitBtn.disabled = false;
                }
            });
        });

    });
</script>
@endpush