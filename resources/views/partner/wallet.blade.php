@extends('layouts.partner')

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
                <div class="card cursor-pointer"
                    data-bs-toggle="modal"
                    data-bs-target="#addMoneyModal">
                    <div class="card-body p-3 text-center">
                        <i class="fas fa-plus-circle text-success fa-2x"></i>
                        <p class="mb-0 mt-2">Add Money</p>
                    </div>
                </div>
            </div>

            {{-- Balance Transfer --}}
            <div class="col-4">
                <div class="card cursor-pointer"
                    role="button"
                    data-bs-toggle="modal"
                    data-bs-target="#balanceTransferModal">

                    <div class="card-body p-3 text-center">
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
                <a href="{{ route('partner.statement') }}" class="text-decoration-none">
                    <div class="card cursor-pointer">
                        <div class="card-body p-3 text-center">
                            <i class="fas fa-file-invoice text-danger fa-2x"></i>
                            <p class="mb-0 mt-2">Statement</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-4">
                <a href="{{ route('partner.withdraw.requests') }}" class="text-decoration-none">
                    <div class="card cursor-pointer">
                        <div class="card-body p-3 text-center">
                            <i class="fas fa-list-alt text-success fa-2x"></i>
                            <p class="mb-0 mt-2">Withdraw Requests</p>
                        </div>
                    </div>
                </a>
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
                        <a href="{{ route('partner.statement') }}" class="text-primary">
                            View Full Statement
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<div class="modal fade" id="withdrawModal" tabindex="-1"
    aria-labelledby="withdrawModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            {{-- Modal Header --}}
            <div class="modal-header">
                <h5 class="modal-title" id="withdrawModalLabel">
                    <i class="fas fa-wallet text-danger"></i> Withdraw Money
                </h5>

                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Withdrawal Form --}}
            <form id="withdrawForm"
                action="{{ route('partner.withdraw.request') }}"
                method="POST">

                @csrf

                <div class="modal-body">

                    {{-- Accountant Search --}}
                    <div class="form-group mb-3">
                        <label for="accountantId" class="form-label">
                            Accountant ID
                        </label>

                        <div class="input-group">
                            <input type="text"
                                id="accountantId"
                                class="form-control"
                                placeholder="Enter accountant ID"
                                autocomplete="off"
                                required>

                            <button type="button"
                                id="searchAccountantBtn"
                                class="btn btn-primary">
                                Search
                            </button>
                        </div>
                    </div>

                    {{-- Accountant Information --}}
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
                        <label for="withdrawAmount" class="form-label">
                            Amount
                        </label>

                        <input type="number"
                            name="amount"
                            id="withdrawAmount"
                            class="form-control"
                            placeholder="Enter amount"
                            min="1"
                            step="0.01"
                            required>
                    </div>

                    {{-- Withdrawal Method --}}
                    <div class="form-group mb-3">
                        <label class="form-label">
                            Withdraw Method
                        </label>

                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                    type="radio"
                                    name="method"
                                    id="methodCash"
                                    value="{{ WITHDRAW_TYPE_CASH }}"
                                    checked
                                    required>

                                <label class="form-check-label" for="methodCash">
                                    Cash To Cash
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                    type="radio"
                                    name="method"
                                    id="methodBank"
                                    value="{{ WITHDRAW_TYPE_BANK }}"
                                    required>

                                <label class="form-check-label" for="methodBank">
                                    Bank To Bank
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                    type="radio"
                                    name="method"
                                    id="methodMobile"
                                    value="{{ WITHDRAW_TYPE_MOBILE_BANKING }}"
                                    required>

                                <label class="form-check-label" for="methodMobile">
                                    Mobile Banking To Cash
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Mobile Banking Type --}}
                    <div class="form-group mb-3"
                        id="mobileBankingTypeField"
                        style="display: none;">

                        <label for="mobileBankingType" class="form-label">
                            Mobile Banking Type
                        </label>

                        <select class="form-control"
                            name="mobile_banking_type"
                            id="mobileBankingType"
                            disabled>

                            <option value="">Select Mobile Banking Type</option>

                            <option value="{{ MOBILE_BANKING_TYPE_BKASH }}">
                                bKash
                            </option>

                            <option value="{{ MOBILE_BANKING_TYPE_NAGAD }}">
                                Nagad
                            </option>

                            <option value="{{ MOBILE_BANKING_TYPE_ROCKET }}">
                                Rocket
                            </option>
                        </select>
                    </div>

                    {{-- Mobile Banking Information --}}
                    <div id="mobileBankingInfo"
                        class="border rounded p-3 mb-3"
                        style="display: none; background: #f8f9fa;">

                        <p class="mb-1">
                            <strong>Mobile Banking Type:</strong>
                            <span id="mobileTypeText"></span>
                        </p>

                        <p class="mb-0">
                            <strong>Mobile Number:</strong>
                            <span id="mobileNumberText"></span>
                        </p>
                    </div>

                    {{-- Bank Information --}}
                    <div id="bankInfoDisplay"
                        class="border rounded p-3 mb-3"
                        style="display: none; background: #f8f9fa;">

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
                        <label for="withdrawNote" class="form-label">
                            Note (optional)
                        </label>

                        <textarea class="form-control"
                            name="note"
                            id="withdrawNote"
                            rows="2"
                            placeholder="Withdrawal note"></textarea>
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


<div class="modal fade" id="addMoneyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle text-success"></i> Add Money
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('partner.add.money') }}" id="addMoneyForm" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Method</label>
                        <select class="form-control" name="method" id="paymentMethod">
                            <option value="">Select Method</option>

                            @foreach($paymentAccounts as $account)
                            <option
                                value="{{ $account->id }}"
                                data-name="{{ $account->name }}"
                                data-type="{{ $account->type_name }}"
                                data-number="{{ $account->number }}">
                                {{ $account->name }} ({{ $account->type_name }})
                            </option>
                            @endforeach

                        </select>
                    </div>
                    <div id="paymentAccountInfo" class="border rounded p-3 mt-3" style="display:none; background:#f8f9fa;">
                        <p class="mb-1">
                            <strong>Method:</strong> <span id="accName"></span>
                        </p>
                        <p class="mb-1">
                            <strong>Type:</strong> <span id="accType"></span>
                        </p>
                        <p class="mb-0">
                            <strong>Number:</strong> <span id="accNumber"></span>
                        </p>
                    </div>
                    <div class="form-group mb-3">
                        <label>Amount</label>
                        <input type="number" name="amount" class="form-control" placeholder="Enter amount" min="1" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Transaction ID</label>
                        <input type="text"
                            name="transaction_id"
                            class="form-control"
                            placeholder="Enter Transaction ID (e.g. TXN123456)"
                            required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Note (optional)</label>
                        <textarea
                            name="note"
                            class="form-control"
                            rows="2"
                            placeholder="Write note (optional)"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Money</button>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- Balance Transfer Modal --}}
<div class="modal fade" id="balanceTransferModal" tabindex="-1"
    aria-labelledby="balanceTransferModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="balanceTransferModalLabel">
                    <i class="fas fa-exchange-alt text-success"></i>
                    Balance Transfer
                </h5>

                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="balanceTransferForm">
                @csrf

                <div class="modal-body">

                    {{-- Available Balance --}}
                    <div class="alert alert-info">
                        <strong>Available Balance:</strong>
                        ৳ {{ number_format($balance, 2) }}
                    </div>

                    {{-- Transfer Type --}}
                    <div class="form-group mb-3">
                        <label for="transferType">Transfer Type</label>

                        <select name="transfer_type" id="transferType"
                            class="form-control" required>
                            <option value="">Select Transfer Type</option>
                            <option value="employee">Partner to Employee</option>
                            <option value="dealer">Partner to Dealer</option>
                            <option value="agent">Partner to Agent</option>
                        </select>
                    </div>

                    {{-- Receiver Account Number --}}
                    <div class="form-group mb-3">
                        <label for="receiverAccountNumber">
                            Receiver Account Number
                        </label>

                        <div class="input-group">
                            <input type="text"
                                id="receiverAccountNumber"
                                name="account_number"
                                class="form-control"
                                placeholder="Enter account number"
                                required>

                            <button type="button"
                                class="btn btn-primary"
                                id="searchTransferReceiver">
                                Search
                            </button>
                        </div>
                    </div>

                    {{-- Receiver Information --}}
                    <div id="transferReceiverInfo"
                        class="border rounded p-3 mb-3"
                        style="display:none; background:#f8f9fa;">

                        <input type="hidden" name="receiver_id"
                            id="transferReceiverId">

                        <p class="mb-1">
                            <strong>Name:</strong>
                            <span id="transferReceiverName"></span>
                        </p>

                        <p class="mb-1">
                            <strong>Account Number:</strong>
                            <span id="transferReceiverAccount"></span>
                        </p>

                        <p class="mb-0">
                            <strong>Type:</strong>
                            <span id="transferReceiverType"></span>
                        </p>
                    </div>

                    {{-- Amount --}}
                    <div class="form-group mb-3">
                        <label for="transferAmount">Amount</label>

                        <input type="number"
                            id="transferAmount"
                            name="amount"
                            class="form-control"
                            min="0.01"
                            step="0.01"
                            placeholder="Enter amount"
                            required>
                    </div>

                    {{-- Note --}}
                    <div class="form-group mb-3">
                        <label for="transferNote">Note (optional)</label>

                        <textarea id="transferNote"
                            name="note"
                            class="form-control"
                            rows="2"
                            placeholder="Transfer note"></textarea>
                    </div>

                    <div id="balanceTransferMessage"
                        style="display:none;"></div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                        class="btn btn-success"
                        id="balanceTransferSubmit"
                        disabled>
                        Transfer
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>



@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const select = document.getElementById('paymentMethod');
        const infoBox = document.getElementById('paymentAccountInfo');

        const accName = document.getElementById('accName');
        const accType = document.getElementById('accType');
        const accNumber = document.getElementById('accNumber');

        select.addEventListener('change', function() {

            const selected = this.options[this.selectedIndex];

            const name = selected.getAttribute('data-name');
            const type = selected.getAttribute('data-type');
            const number = selected.getAttribute('data-number');

            if (!this.value) {
                infoBox.style.display = 'none';
                return;
            }

            accName.textContent = name || '-';
            accType.textContent = type || '-';
            accNumber.textContent = number || '-';

            infoBox.style.display = 'block';
        });

        $('#addMoneyForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(res) {

                    $('#addMoneyModal').modal('hide');
                    form.trigger('reset');

                    const alertHtml = `
                <div class="alert alert-success alert-dismissible fade show">
                    ${res.message || 'Request submitted successfully'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;

                    $('#ajaxAlertContainer').append(alertHtml);
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Something went wrong');
                }
            });
        });

    });


    $(document).ready(function() {
        const beneficiary = @json($beneficiary ?? null);

        const mobileTypeField = document.getElementById('mobileBankingTypeField');
        const submitBtn = document.getElementById('withdrawSubmitBtn');

        const mobileInfo = document.getElementById('mobileBankingInfo');
        const bankInfo = document.getElementById('bankInfoDisplay');

        const mobileTypeSelect = document.getElementById('mobileBankingType');
        const mobileTypeText = document.getElementById('mobileTypeText');
        const mobileNumberText = document.getElementById('mobileNumberText');

        const bankNameText = document.getElementById('bankNameText');
        const bankBranchText = document.getElementById('bankBranchText');
        const bankAccountText = document.getElementById('bankAccountText');

        const withdrawForm = document.getElementById('withdrawForm');

        if (!withdrawForm || !submitBtn) {
            return;
        }

        let submittingWithdrawal = false;
        const originalButtonText = $(submitBtn).text();

        // Laravel named route URL
        const withdrawUrl = @json(route('partner.withdraw.request'));

        function resetBeneficiaryInfo() {
            if (mobileInfo) mobileInfo.style.display = 'none';
            if (bankInfo) bankInfo.style.display = 'none';

            if (mobileTypeText) mobileTypeText.textContent = '';
            if (mobileNumberText) mobileNumberText.textContent = '';

            if (bankNameText) bankNameText.textContent = '';
            if (bankBranchText) bankBranchText.textContent = '';
            if (bankAccountText) bankAccountText.textContent = '';
        }

        function showAlert(type, message, timeout = 5000) {
            const safeMessage = $('<div>').text(message).html();

            const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <strong>${type === 'success' ? 'Success!' : type === 'warning' ? 'Warning!' : 'Error!'}</strong>
                ${safeMessage.replace(/\n/g, '<br>')}
                <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

            $('#ajaxAlertContainer').append(alertHtml);

            const $alert = $('#ajaxAlertContainer .alert').last();

            setTimeout(function() {
                if ($alert.length) {
                    if (window.bootstrap) {
                        bootstrap.Alert.getOrCreateInstance($alert[0]).close();
                    } else {
                        $alert.remove();
                    }
                }
            }, timeout);
        }

        function getMobileNumber(type) {
            if (!beneficiary) return '';

            if (type === '{{ MOBILE_BANKING_TYPE_BKASH }}') {
                return beneficiary.bkash_number || '';
            }

            if (type === '{{ MOBILE_BANKING_TYPE_NAGAD }}') {
                return beneficiary.nagad_number || '';
            }

            if (type === '{{ MOBILE_BANKING_TYPE_ROCKET }}') {
                return beneficiary.rocket_number || '';
            }

            return '';
        }

        function updateSubmitState() {
            const accountantId = $('#selectedAccountantId').val();

            submitBtn.disabled = !accountantId || submittingWithdrawal;
        }

        function resetMobileBankingSelection() {
            if (!mobileTypeSelect) return;

            mobileTypeSelect.value = '';
            mobileTypeSelect.disabled = true;
            mobileTypeSelect.required = false;

            if (mobileTypeField) {
                mobileTypeField.style.display = 'none';
            }
        }

        function resetWithdrawalFormState() {
            $('#accountantInfo').hide();
            $('#selectedAccountantId').val('');
            $('#accountantId').val('');

            resetBeneficiaryInfo();
            resetMobileBankingSelection();

            const checkedMethod = document.querySelector(
                'input[name="method"]:checked'
            );

            if (checkedMethod) {
                checkedMethod.dispatchEvent(new Event('change'));
            }

            updateSubmitState();
        }

        // Withdrawal method selection
        document.querySelectorAll('input[name="method"]').forEach(function(el) {
            el.addEventListener('change', function() {
                resetBeneficiaryInfo();
                resetMobileBankingSelection();

                if (
                    this.value === '{{ WITHDRAW_TYPE_MOBILE_BANKING }}' &&
                    mobileTypeSelect
                ) {
                    if (mobileTypeField) {
                        mobileTypeField.style.display = 'block';
                    }

                    mobileTypeSelect.disabled = false;
                    mobileTypeSelect.required = true;
                }

                if (
                    this.value === '{{ WITHDRAW_TYPE_BANK }}' &&
                    beneficiary &&
                    beneficiary.account_number
                ) {
                    if (bankInfo) bankInfo.style.display = 'block';

                    if (bankNameText) {
                        bankNameText.textContent = beneficiary.bank_name || '-';
                    }

                    if (bankBranchText) {
                        bankBranchText.textContent = beneficiary.branch_name || '-';
                    }

                    if (bankAccountText) {
                        bankAccountText.textContent = beneficiary.account_number || '-';
                    }
                }
            });
        });

        // Mobile banking type selection
        if (mobileTypeSelect) {
            mobileTypeSelect.addEventListener('change', function() {
                if (mobileInfo) mobileInfo.style.display = 'none';
                if (mobileTypeText) mobileTypeText.textContent = '';
                if (mobileNumberText) mobileNumberText.textContent = '';

                const selectedValue = this.value;

                if (!selectedValue || !beneficiary) return;

                const number = getMobileNumber(selectedValue);
                let typeLabel = '';

                if (selectedValue === '{{ MOBILE_BANKING_TYPE_BKASH }}') {
                    typeLabel = 'bKash';
                } else if (selectedValue === '{{ MOBILE_BANKING_TYPE_NAGAD }}') {
                    typeLabel = 'Nagad';
                } else if (selectedValue === '{{ MOBILE_BANKING_TYPE_ROCKET }}') {
                    typeLabel = 'Rocket';
                }

                if (number) {
                    if (mobileInfo) mobileInfo.style.display = 'block';
                    if (mobileTypeText) mobileTypeText.textContent = typeLabel;
                    if (mobileNumberText) mobileNumberText.textContent = number;
                } else {
                    showAlert(
                        'warning',
                        'The selected mobile banking number was not found in your beneficiary information.'
                    );
                }
            });
        }

        // Initialize selected withdrawal method
        const checkedMethod = document.querySelector(
            'input[name="method"]:checked'
        );

        if (checkedMethod) {
            checkedMethod.dispatchEvent(new Event('change'));
        }

        // Accountant search
        $('#searchAccountantBtn').on('click', function(e) {
            e.preventDefault();

            const $searchBtn = $(this);
            const accId = $('#accountantId').val().trim();

            if (!accId) {
                alert('Please enter Accountant ID.');
                return;
            }

            $searchBtn.prop('disabled', true).text('Searching...');
            $('#accountantInfo').hide();
            $('#selectedAccountantId').val('');
            updateSubmitState();

            const searchUrl = @json(
                route('partner.accountant.search', [
                    'accountantId' => '__ACCOUNTANT_ID__'
                ])
            );

            const url = searchUrl.replace(
                '__ACCOUNTANT_ID__',
                encodeURIComponent(accId)
            );

            fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(async function(response) {
                    const contentType = response.headers.get('content-type') || '';

                    if (!contentType.includes('application/json')) {
                        throw new Error(
                            'Invalid server response. Check your session, route, and middleware.'
                        );
                    }

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Accountant not found.');
                    }

                    return data;
                })
                .then(function(data) {
                    $('#accName').text(data.name || '-');
                    $('#accBranch').text(data.branch || '-');
                    $('#accDesignation').text(data.designation || '-');

                    $('#selectedAccountantId').val(data.id);
                    $('#accountantInfo').slideDown(150);

                    updateSubmitState();
                })
                .catch(function(error) {
                    $('#accountantInfo').hide();
                    $('#selectedAccountantId').val('');
                    updateSubmitState();

                    alert(error.message || 'Unable to search accountant.');
                })
                .finally(function() {
                    $searchBtn.prop('disabled', false).text('Search');
                });
        });

        // Withdrawal form submission
        $('#withdrawForm').on('submit', function(e) {
            e.preventDefault();

            if (submittingWithdrawal) return;

            const $form = $(this);
            const accountantId = $('#selectedAccountantId').val();
            const amount = parseFloat($form.find('[name="amount"]').val());
            const method = $form.find('[name="method"]:checked').val();
            const mobileType = mobileTypeSelect ? mobileTypeSelect.value : '';

            if (!accountantId) {
                alert('Please search and select an accountant first.');
                return;
            }

            if (!Number.isFinite(amount) || amount < 1) {
                alert('Please enter a valid amount.');
                return;
            }

            if (!method) {
                alert('Please select a withdrawal method.');
                return;
            }

            if (
                method === '{{ WITHDRAW_TYPE_BANK }}' &&
                (!beneficiary || !beneficiary.account_number)
            ) {
                alert('Please add your bank beneficiary information first.');
                return;
            }

            if (method === '{{ WITHDRAW_TYPE_MOBILE_BANKING }}') {
                if (!mobileType) {
                    alert('Please select a mobile banking type.');
                    return;
                }

                if (!getMobileNumber(mobileType)) {
                    alert(
                        'The selected mobile banking number is not available in your beneficiary information.'
                    );
                    return;
                }
            }

            submittingWithdrawal = true;
            submitBtn.disabled = true;
            $(submitBtn).text('Submitting...');

            $.ajax({
                url: withdrawUrl,
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },

                success: function(response) {
                    if (!response || response.success === false) {
                        showAlert(
                            'danger',
                            response?.message ||
                            'Withdrawal request could not be submitted.'
                        );
                        return;
                    }

                    const modalElement = document.getElementById('withdrawModal');

                    if (modalElement && window.bootstrap) {
                        bootstrap.Modal.getOrCreateInstance(modalElement).hide();
                    } else {
                        $('#withdrawModal').modal('hide');
                    }

                    $form[0].reset();
                    resetWithdrawalFormState();

                    showAlert(
                        'success',
                        response.message ||
                        'Withdrawal request submitted successfully.'
                    );
                },

                error: function(xhr) {
                    let message = 'Something went wrong. Please try again.';

                    if (xhr.status === 401) {
                        message = 'Please log in again.';
                    } else if (xhr.status === 403) {
                        message = 'You are not authorized to submit this request.';
                    } else if (xhr.status === 419) {
                        message =
                            'Your session or CSRF token has expired. Refresh the page and try again.';
                    } else if (xhr.status === 404) {
                        message =
                            'Withdrawal route not found (404). Check the generated URL and Laravel route registration.';
                    } else if (xhr.status === 405) {
                        message =
                            'HTTP method not allowed. Check that the withdrawal route accepts POST.';
                    } else if (xhr.responseJSON?.errors) {
                        message = Object.values(xhr.responseJSON.errors)
                            .flat()
                            .join('\n');
                    } else if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.status >= 500) {
                        message = 'Server error. Check the Laravel log for details.';
                    }

                    showAlert('danger', message, 7000);
                },

                complete: function() {
                    submittingWithdrawal = false;
                    $(submitBtn).text(originalButtonText);
                    updateSubmitState();
                }
            });
        });

        // Clear previous data when the modal is closed
        const modalElement = document.getElementById('withdrawModal');

        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', function() {
                if (submittingWithdrawal) return;

                withdrawForm.reset();
                resetWithdrawalFormState();

                $(submitBtn).text(originalButtonText);
            });
        }

        // Invalidate accountant verification when ID changes
        $('#accountantId').on('input', function() {
            $('#selectedAccountantId').val('');
            $('#accountantInfo').hide();
            updateSubmitState();
        });

        updateSubmitState();
    });


    document.addEventListener('DOMContentLoaded', function() {

        const typeSelect = document.getElementById('transferType');
        const accountInput = document.getElementById('receiverAccountNumber');
        const searchButton = document.getElementById('searchTransferReceiver');
        const receiverInfo = document.getElementById('transferReceiverInfo');
        const receiverId = document.getElementById('transferReceiverId');
        const submitButton = document.getElementById('balanceTransferSubmit');
        const messageBox = document.getElementById('balanceTransferMessage');
        const transferForm = document.getElementById('balanceTransferForm');
        const amountInput = document.getElementById('transferAmount');

        if (!typeSelect || !accountInput || !transferForm) {
            return;
        }

        // Partner routes: এগুলো routes ফাইলে তৈরি করতে হবে।
        const searchUrl = @json(route('partner.transfer.receiver.search'));
        const transferUrl = @json(route('partner.transfer.submit'));

        let receiverVerified = false;

        function showMessage(message, type = 'danger') {
            messageBox.className = 'alert alert-' + type;
            messageBox.textContent = message;
            messageBox.style.display = 'block';
        }

        function clearMessage() {
            messageBox.textContent = '';
            messageBox.className = '';
            messageBox.style.display = 'none';
        }

        function resetReceiver() {
            receiverId.value = '';
            receiverInfo.style.display = 'none';
            submitButton.disabled = true;
            receiverVerified = false;
            clearMessage();
        }

        // Receiver Type বা Account Number পরিবর্তন হলে পুনরায় Search করতে হবে।
        typeSelect.addEventListener('change', resetReceiver);
        accountInput.addEventListener('input', resetReceiver);

        // 1. Receiver Search
        searchButton.addEventListener('click', async function() {

            const transferType = typeSelect.value;
            const accountNumber = accountInput.value.trim();

            resetReceiver();

            if (!transferType || !accountNumber) {
                showMessage(
                    'Transfer Type এবং Account Number দিন।',
                    'warning'
                );
                return;
            }

            searchButton.disabled = true;
            searchButton.textContent = 'Searching...';

            try {
                const url = new URL(searchUrl, window.location.origin);

                url.searchParams.set('transfer_type', transferType);
                url.searchParams.set('account_number', accountNumber);

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (!response.ok || !data.success || !data.receiver) {
                    throw new Error(
                        data.message || 'Receiver account পাওয়া যায়নি।'
                    );
                }

                const receiver = data.receiver;

                receiverId.value = receiver.id;

                document.getElementById('transferReceiverName').textContent =
                    receiver.name || '-';

                document.getElementById('transferReceiverAccount').textContent =
                    receiver.account_number || '-';

                document.getElementById('transferReceiverType').textContent =
                    receiver.type || '-';

                receiverInfo.style.display = 'block';
                receiverVerified = true;
                submitButton.disabled = false;

                showMessage(
                    'Receiver account সফলভাবে যাচাই হয়েছে।',
                    'success'
                );

            } catch (error) {
                resetReceiver();

                showMessage(
                    error.message || 'Receiver Search ব্যর্থ হয়েছে।',
                    'danger'
                );

            } finally {
                searchButton.disabled = false;
                searchButton.textContent = 'Search';
            }
        });

        // 2. Submit Balance Transfer
        transferForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            if (!receiverVerified || !receiverId.value) {
                showMessage(
                    'আগে Receiver Account Search করে যাচাই করুন।',
                    'warning'
                );
                return;
            }

            const transferType = typeSelect.value;
            const accountNumber = accountInput.value.trim();
            const amount = Number(amountInput.value);

            if (!transferType || !accountNumber) {
                showMessage(
                    'Transfer Type এবং Account Number দিন।',
                    'warning'
                );
                return;
            }

            if (!Number.isFinite(amount) || amount <= 0) {
                showMessage('সঠিক Transfer Amount লিখুন।', 'warning');
                return;
            }

            if (!window.confirm('আপনি কি এই Balance Transfer করতে নিশ্চিত?')) {
                return;
            }

            submitButton.disabled = true;
            searchButton.disabled = true;
            submitButton.textContent = 'Processing...';

            try {
                const response = await fetch(transferUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': transferForm.querySelector(
                            'input[name="_token"]'
                        ).value
                    },
                    body: JSON.stringify({
                        transfer_type: transferType,
                        account_number: accountNumber,
                        receiver_id: receiverId.value,
                        amount: amount,
                        note: document.getElementById('transferNote').value
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(
                        data.message || 'Balance Transfer ব্যর্থ হয়েছে।'
                    );
                }

                showMessage(
                    data.message || 'Balance Transfer সফল হয়েছে।',
                    'success'
                );

                // সফল Transfer-এর পর নতুন Balance দেখাবে।
                setTimeout(function() {
                    window.location.reload();
                }, 1200);

            } catch (error) {
                showMessage(
                    error.message || 'Transfer ব্যর্থ হয়েছে। আবার চেষ্টা করুন।',
                    'danger'
                );

                submitButton.disabled = false;
                searchButton.disabled = false;

            } finally {
                if (receiverVerified) {
                    submitButton.textContent = 'Transfer';
                }
            }
        });

        // Modal বন্ধ করে আবার খুললে আগের Receiver তথ্য মুছে যাবে।
        const modalElement = document.getElementById('balanceTransferModal');

        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', function() {
                transferForm.reset();
                resetReceiver();
                submitButton.textContent = 'Transfer';
                searchButton.disabled = false;
            });
        }

    });
</script>
@endpush