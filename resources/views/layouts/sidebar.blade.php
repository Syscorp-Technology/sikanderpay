<!--sidebar wrapper -->
<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        {{-- <div>
            <img src="assets/images/logo-icon.png" class="logo-icon" alt="logo icon">
        </div> --}}
        <div>
            <h4 class="logo-text">SikanderPlayx</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        @can('Dashboard')
            <li>
                <a href="{{ route('dashboard') }}">
                    <div class="parent-icon">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div class="menu-title">Dashboard</div>
                </a>
            </li>
        @endcan

        @can('User')
            <li>
                <a href="#" class="has-arrow">
                    <div class="parent-icon"><i class="fa-regular fa-user"></i></div>
                    <div class="menu-title">Players</div>
                </a>
                <ul>
                    @can('All User')
                        <li>
                            <a href="{{ route('UserRegister.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>All Player
                            </a>
                        </li>
                    @endcan
                    @can('Upload Player')
                        <li>
                            <a href="{{ route('player-files') }}">
                                <i class="bx bx-right-arrow-alt"></i>Upload Player
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('Dashboard')
            <li>
                <a href="{{ route('benificiary.index') }}">
                    <div class="parent-icon">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div class="menu-title">Benificiary</div>
                </a>
            </li>
        @endcan
        @can('Payments')
            <li>
                <a href="#" class="has-arrow">
                    <div class="parent-icon"><i class="fa-regular fa-credit-card"></i></div>
                    <div class="menu-title">Payments</div>
                </a>
                <ul>
                    {{-- @can('All Payment')
                        <li>
                            <a href="{{ route('payment.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>All Payments
                            </a>
                        </li>
                    @endcan --}}
                    @can('All Deposit')
                        <li>
                            <a href="{{ route('deposit.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>All Deposit
                            </a>
                        </li>
                    @endcan
                    @can('All Withdraw')
                        <li>
                            <a href="{{ route('withdraw.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>All Withdraw
                            </a>
                        </li>
                    @endcan

                </ul>
            </li>
        @endcan

        @can('Report')
            <li>
                <a href="#" class="has-arrow">
                    <div class="parent-icon"><i class="fa-regular fa-flag"></i></div>
                    <div class="menu-title">Reports</div>
                </a>
                <ul>
                    @can('User Report')
                        <li>
                            <a href="{{ route('deposit.report') }}">
                                <i class="bx bx-right-arrow-alt"></i>Deposit
                            </a>
                        </li>
                    @endcan
                    @can('Payment Report')
                        <li>
                            <a href="{{ route('withdraw.report') }}">
                                <i class="bx bx-right-arrow-alt"></i>Withdraw
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan

{{--
        <div class="col-md-3 d-flex justify-content-start">

            <a type="button" href="{{route('withdraw.gateway.pending')}}"  class="position-relative mb-0 text-uppercase" >
               <h6> Razorpay</h6>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{$pendingCount}}
                </span>
            </a>

         </div> --}}

        @can('Income And Expense Report')
            <li>
                <a href="#" class="has-arrow">
                    <div class="parent-icon"><i class="fa-regular fa-flag"></i></div>
                    <div class="menu-title">Income & Transfers</div>
                </a>
                <ul>
                    @can('Income Report')
                        <li>
                            <a href="{{ route('income.report') }}">
                                <i class="bx bx-right-arrow-alt"></i>Income
                            </a>
                        </li>
                    @endcan
                    @can('Expense Report')
                        <li>
                            <a href="{{ route('expense.report') }}">
                                <i class="bx bx-right-arrow-alt"></i>Transfers
                            </a>
                            {{-- expense as transfer changed user role permission time checkout --}}

                        </li>
                    @endcan
                    @can('InternalTransfer Report')
                        <li>
                            <a href="{{ route('internalTransfer.report') }}">
                                <i class="bx bx-right-arrow-alt"></i>Internal Transfer
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('Withdraw Banker Enable')
        <li>

            <a href="{{route('withdraw.gateway.pending')}}" >
                <div class="parent-icon"><i class="fa-regular fa-flag"></i></div>
                @php
                 $gatewayProcessCount = App\Models\Withdraw::where('status', 'Gateway Process')->count();
                @endphp
                <div class="menu-title">Gateway <span class="badge text-bg-danger">{{$gatewayProcessCount}}</span></div>
            </a>

        </li>
        @endcan

    </ul>
    <!--end navigation-->
</div>
<!--end sidebar wrapper -->
