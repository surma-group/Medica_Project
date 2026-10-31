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

            {{-- Withdraw --}}
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

            {{-- Statement --}}
            <div class="col-4">

                <a href="{{ route('employee.statement') }}"
                    class="text-decoration-none">

                    <div class="card cursor-pointer">

                        <div class="card-body p-3 text-center">

                            <i class="fas fa-file-invoice text-danger fa-2x"></i>

                            <p class="mb-0 mt-2">
                                Statement
                            </p>

                        </div>
                    </div>

                </a>

            </div>

            {{-- Withdraw History --}}
            <div class="col-4">

                <a href="{{ route('employee.withdraw.requests') }}"
                    class="text-decoration-none">

                    <div class="card cursor-pointer">

                        <div class="card-body p-3 text-center">

                            <i class="fas fa-list-alt text-success fa-2x"></i>

                            <p class="mb-0 mt-2">
                                Withdraw History
                            </p>

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
                                    <strong>{{ $entry->note ?? 'Wallet Transaction' }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $entry->created_at->format('d M Y, h:i A') }}
                                    </small>
                                </div>

                                <div class="text-end">
                                    @if($entry->credit > 0)
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

{{-- Withdraw Modal --}}
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="withdrawModalLabel">
                    <i class="fas fa-wallet text-danger"></i> Withdraw Money
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="withdrawForm" action="{{ route('employee.withdraw.request') }}" method="POST">
                @csrf

                <div class="modal-body">

                    {{-- Accountant Search --}}
                    <div class="form-group mb-3">
                        <label>Accountant ID</label>
                        <div class="input-group">
                            <input type="text" id="accountantId" class="form-control"
                                placeholder="Enter accountant ID">

                            <button type="button" id="searchAccountantBtn" class="btn btn-primary">
                                Search
                            </button>
                        </div>
                    </div>

                    {{-- Accountant Information --}}
                    <div id="accountantInfo" class="border rounded p-3 mb-3"
                        style="display:none; background:#f8f9fa;">

                        <input type="hidden" name="accountant_id" id="selectedAccountantId">

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
                        <input type="number" name="amount" class="form-control"
                            placeholder="Enter amount" min="1" step="0.01" required>
                    </div>

                    {{-- Withdraw Method --}}
                    <div class="form-group mb-3">
                        <label class="form-label me-3">Withdraw Method</label>
                        <br>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="method"
                                id="methodCash" value="{{ WITHDRAW_TYPE_CASH }}" checked required>
                            <label class="form-check-label" for="methodCash">
                                Cash To Cash
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="method"
                                id="methodBank" value="{{ WITHDRAW_TYPE_BANK }}" required>
                            <label class="form-check-label" for="methodBank">
                                Bank To Bank
                            </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="method"
                                id="methodMobile" value="{{ WITHDRAW_TYPE_MOBILE_BANKING }}" required>
                            <label class="form-check-label" for="methodMobile">
                                Mobile Banking To Cash
                            </label>
                        </div>
                    </div>

                    {{-- Mobile Banking Type --}}
                    <div class="form-group mb-3" id="mobileBankingTypeField" style="display:none;">
                        <label>Mobile Banking Type</label>

                        <select class="form-control" name="mobile_banking_type" id="mobileBankingType">
                            <option value="">Select Mobile Banking Type</option>
                            <option value="{{ MOBILE_BANKING_TYPE_BKASH }}">bKash</option>
                            <option value="{{ MOBILE_BANKING_TYPE_NAGAD }}">Nagad</option>
                            <option value="{{ MOBILE_BANKING_TYPE_ROCKET }}">Rocket</option>
                        </select>
                    </div>

                    {{-- Mobile Banking Information --}}
                    <div id="mobileBankingInfo" class="border rounded p-3 mb-3"
                        style="display:none; background:#f8f9fa;">

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
                    <div id="bankInfoDisplay" class="border rounded p-3 mb-3"
                        style="display:none; background:#f8f9fa;">

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
                        <textarea class="form-control" name="note" rows="2"
                            placeholder="Withdraw note"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-danger" id="withdrawSubmitBtn" disabled>
                        Withdraw
                    </button>
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
                            <option value="employee">Employee to Employee</option>
                            <option value="dealer">Employee to Dealer</option>
                            <option value="agent">Employee to Agent</option>
                        </select>
                    </div>

                    {{-- Receiver Account Number --}}
                    <div class="form-group mb-3">
                        <label for="receiverAccountNumber">
                            Receiver Account Number
                        </label>

                        <div class="input-group">
                            <input type="text" id="receiverAccountNumber"
                                name="account_number" class="form-control"
                                placeholder="Enter account number" required>

                            <button type="button" class="btn btn-primary"
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

                        <input type="number" id="transferAmount"
                            name="amount" class="form-control"
                            min="0.01" step="0.01"
                            placeholder="Enter amount" required>
                    </div>

                    {{-- Note --}}
                    <div class="form-group mb-3">
                        <label for="transferNote">Note (optional)</label>

                        <textarea id="transferNote" name="note"
                            class="form-control" rows="2"
                            placeholder="Transfer note"></textarea>
                    </div>

                    <div id="balanceTransferMessage"></div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-success"
                        id="balanceTransferSubmit" disabled>
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

        // Employee beneficiary data
        const beneficiary = @json($beneficiary ?? null);

        const mobileTypeField = document.getElementById('mobileBankingTypeField');
        const mobileInfo = document.getElementById('mobileBankingInfo');
        const bankInfo = document.getElementById('bankInfoDisplay');
        const mobileTypeSelect = document.getElementById('mobileBankingType');
        const submitBtn = document.getElementById('withdrawSubmitBtn');
        const searchBtn = document.getElementById('searchAccountantBtn');

        // Withdraw method change
        document.querySelectorAll('input[name="method"]').forEach(function(el) {
            el.addEventListener('change', function() {
                mobileInfo.style.display = 'none';
                bankInfo.style.display = 'none';
                mobileTypeField.style.display = 'none';
                mobileTypeSelect.value = '';

                if (this.value === '{{ WITHDRAW_TYPE_MOBILE_BANKING }}') {
                    mobileTypeField.style.display = 'block';

                } else if (this.value === '{{ WITHDRAW_TYPE_BANK }}') {
                    if (beneficiary && beneficiary.account_number) {
                        bankInfo.style.display = 'block';

                        document.getElementById('bankNameText').textContent =
                            beneficiary.bank_name || '-';

                        document.getElementById('bankBranchText').textContent =
                            beneficiary.branch_name || '-';

                        document.getElementById('bankAccountText').textContent =
                            beneficiary.account_number || '-';
                    }
                }
            });
        });

        // Mobile banking type change
        mobileTypeSelect.addEventListener('change', function() {
            if (!this.value || !beneficiary) {
                mobileInfo.style.display = 'none';
                return;
            }

            let typeLabel = '';
            let number = '';

            if (this.value === '{{ MOBILE_BANKING_TYPE_BKASH }}') {
                typeLabel = 'bKash';
                number = beneficiary.bkash_number || '';

            } else if (this.value === '{{ MOBILE_BANKING_TYPE_NAGAD }}') {
                typeLabel = 'Nagad';
                number = beneficiary.nagad_number || '';

            } else if (this.value === '{{ MOBILE_BANKING_TYPE_ROCKET }}') {
                typeLabel = 'Rocket';
                number = beneficiary.rocket_number || '';
            }

            if (number) {
                mobileInfo.style.display = 'block';

                document.getElementById('mobileTypeText').textContent = typeLabel;
                document.getElementById('mobileNumberText').textContent = number;

            } else {
                mobileInfo.style.display = 'none';
            }
        });

        // Initialize default withdraw method
        const selectedMethod = document.querySelector('input[name="method"]:checked');

        if (selectedMethod) {
            selectedMethod.dispatchEvent(new Event('change'));
        }

        // Accountant Search
        $('#searchAccountantBtn').on('click', function() {
            const accId = $('#accountantId').val().trim();

            if (!accId) {
                alert('Please enter Accountant ID');
                return;
            }

            searchBtn.disabled = true;
            searchBtn.textContent = 'Searching...';

            fetch(`/employee/accountant/search/${encodeURIComponent(accId)}`)
                .then(function(res) {
                    if (!res.ok) {
                        throw new Error('Server error');
                    }

                    return res.json();
                })
                .then(function(data) {
                    if (!data.success) {
                        alert('Accountant not found');

                        $('#accountantInfo').hide();
                        $('#selectedAccountantId').val('');

                        submitBtn.disabled = true;
                        return;
                    }

                    $('#accName').text(data.name || '-');
                    $('#accBranch').text(data.branch || '-');
                    $('#accDesignation').text(data.designation || '-');

                    $('#selectedAccountantId').val(data.id);
                    $('#accountantInfo').slideDown(150);

                    submitBtn.disabled = false;
                })
                .catch(function() {
                    alert('Server error. Try again.');
                })
                .finally(function() {
                    searchBtn.disabled = false;
                    searchBtn.textContent = 'Search';
                });
        });

        // Withdraw AJAX
        $('#withdrawForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const accountantId = $('#selectedAccountantId').val();
            const amount = parseFloat(form.find('input[name="amount"]').val());
            const method = form.find('input[name="method"]:checked').val();
            const mobileType = $('#mobileBankingType').val();

            if (!accountantId) {
                alert('Please search and select an accountant first.');
                return;
            }

            if (!Number.isFinite(amount) || amount < 1) {
                alert('Please enter a valid amount.');
                return;
            }

            if (
                method === '{{ WITHDRAW_TYPE_MOBILE_BANKING }}' &&
                !mobileType
            ) {
                alert('Please select a mobile banking type.');
                return;
            }

            submitBtn.disabled = true;

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),

                success: function(response) {
                    const modalElement = document.getElementById('withdrawModal');

                    if (window.bootstrap && bootstrap.Modal) {
                        bootstrap.Modal.getOrCreateInstance(modalElement).hide();
                    } else {
                        $('#withdrawModal').modal('hide');
                    }

                    form.trigger('reset');

                    $(
                        '#mobileBankingTypeField, ' +
                        '#mobileBankingInfo, ' +
                        '#bankInfoDisplay, ' +
                        '#accountantInfo'
                    ).hide();

                    $('#selectedAccountantId').val('');
                    $('#accountantId').val('');

                    submitBtn.disabled = true;

                    const alertElement = $(`
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong>
                        <span class="alert-message"></span>
                        <button type="button" class="btn-close"
                            data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);

                    alertElement.find('.alert-message').text(
                        response.message || 'Withdrawal request submitted.'
                    );

                    $('#ajaxAlertContainer').append(alertElement);
                },

                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;

                    const message = errors ?
                        Object.values(errors).flat().join('\n') :
                        (xhr.responseJSON?.message || 'Something went wrong!');

                    alert(message);
                },

                complete: function() {
                    submitBtn.disabled = !$('#selectedAccountantId').val();
                }
            });
        });

    });



    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const typeSelect = document.getElementById('transferType');
        const accountInput = document.getElementById('receiverAccountNumber');
        const searchButton = document.getElementById('searchTransferReceiver');
        const receiverInfo = document.getElementById('transferReceiverInfo');
        const receiverId = document.getElementById('transferReceiverId');
        const submitButton = document.getElementById('balanceTransferSubmit');
        const messageBox = document.getElementById('balanceTransferMessage');
        const transferForm = document.getElementById('balanceTransferForm');

        // Laravel routes
        const searchUrl = @json(route('employee.transfer.receiver.search'));
        const transferUrl = @json(route('employee.transfer.submit'));

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

        // Reset receiver verification whenever account/type changes
        typeSelect.addEventListener('change', resetReceiver);
        accountInput.addEventListener('input', resetReceiver);

        // ========================================
        // 1. Receiver Search
        // ========================================
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
                    error.message || 'Receiver search ব্যর্থ হয়েছে।',
                    'danger'
                );
            } finally {
                searchButton.disabled = false;
                searchButton.textContent = 'Search';
            }
        });

        // ========================================
        // 2. Balance Transfer Submit
        // ========================================
        transferForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            if (!receiverVerified || !receiverId.value) {
                showMessage(
                    'আগে Receiver Account Search করে যাচাই করুন।',
                    'warning'
                );
                return;
            }

            const amountInput = document.getElementById('transferAmount');
            const amount = Number(amountInput.value);

            if (!Number.isFinite(amount) || amount <= 0) {
                showMessage('সঠিক Transfer Amount লিখুন।', 'warning');
                return;
            }

            if (!typeSelect.value || !accountInput.value.trim()) {
                showMessage(
                    'Transfer Type এবং Account Number দিন।',
                    'warning'
                );
                return;
            }

            const confirmed = window.confirm(
                'আপনি কি এই Transfer করতে নিশ্চিত?'
            );

            if (!confirmed) {
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';
            searchButton.disabled = true;

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
                        transfer_type: typeSelect.value,
                        account_number: accountInput.value.trim(),
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

                // Reload after success to show the updated wallet balance.
                setTimeout(function() {
                    window.location.reload();
                }, 1200);

            } catch (error) {
                showMessage(
                    error.message || 'Transfer ব্যর্থ হয়েছে। আবার চেষ্টা করুন।',
                    'danger'
                );

                submitButton.disabled = false;
                submitButton.textContent = 'Transfer';
                searchButton.disabled = false;
            }
        });
    });
</script>
@endpush