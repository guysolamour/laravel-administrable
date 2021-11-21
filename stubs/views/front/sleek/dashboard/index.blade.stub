@extends(front_view_path('layouts.dashboard'))

<x-administrable::seotags :force="true" title="Tableau de bord" />

@section('content')

<div class="content">
    <!-- Top Statistics -->
    <div class="row">
        <div class="col-xl-3 col-sm-6">
            <div class="card card-mini mb-4">
                <div class="card-body">
                    <h2 class="mb-1">71,503</h2>
                    <p>Online Signups</p>
                    <div class="chartjs-wrapper">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card card-mini  mb-4">
                <div class="card-body">
                    <h2 class="mb-1">9,503</h2>
                    <p>New Visitors Today</p>
                    <div class="chartjs-wrapper">
                        <canvas id="dual-line"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card card-mini mb-4">
                <div class="card-body">
                    <h2 class="mb-1">71,503</h2>
                    <p>Monthly Total Order</p>
                    <div class="chartjs-wrapper">
                        <canvas id="area-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card card-mini mb-4">
                <div class="card-body">
                    <h2 class="mb-1">9,503</h2>
                    <p>Total Revenue This Year</p>
                    <div class="chartjs-wrapper">
                        <canvas id="line"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-xl-8 col-md-12">
            <!-- Sales Graph -->
            <div class="card card-default" data-scroll-height="675">
                <div class="card-header">
                    <h2>Sales Of The Year</h2>
                </div>
                <div class="card-body">
                    <canvas id="linechart" class="chartjs"></canvas>
                </div>
                <div class="card-footer d-flex flex-wrap bg-white p-0">
                    <div class="col-6 px-0">
                        <div class="text-center p-4">
                            <h4>$6,308</h4>
                            <p class="mt-2">Total orders of this year</p>
                        </div>
                    </div>
                    <div class="col-6 px-0">
                        <div class="text-center p-4 border-left">
                            <h4>$70,506</h4>
                            <p class="mt-2">Total revenue of this year</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12">
            <!-- Doughnut Chart -->
            <div class="card card-default" data-scroll-height="675">
                <div class="card-header justify-content-center">
                    <h2>Orders Overview</h2>
                </div>
                <div class="card-body">
                    <canvas id="doChart"></canvas>
                </div>
                <a href="#" class="pb-5 d-block text-center text-muted"><i class="mdi mdi-download mr-2"></i> Download
                    overall report</a>
                <div class="card-footer d-flex flex-wrap bg-white p-0">
                    <div class="col-6">
                        <div class="py-4 px-4">
                            <ul class="d-flex flex-column justify-content-between">
                                <li class="mb-2"><i class="mdi mdi-checkbox-blank-circle-outline mr-2"
                                        style="color: #4c84ff"></i>Order Completed</li>
                                <li><i class="mdi mdi-checkbox-blank-circle-outline mr-2"
                                        style="color: #80e1c1 "></i>Order Unpaid</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-6 border-left">
                        <div class="py-4 px-4 ">
                            <ul class="d-flex flex-column justify-content-between">
                                <li class="mb-2"><i class="mdi mdi-checkbox-blank-circle-outline mr-2"
                                        style="color: #8061ef"></i>Order Pending</li>
                                <li><i class="mdi mdi-checkbox-blank-circle-outline mr-2"
                                        style="color: #ffa128"></i>Order Canceled</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-6 col-12">

            <!-- Polar and Radar Chart -->
            <div class="card card-default">
                <div class="card-header justify-content-center">
                    <h2>Sales Overview</h2>
                </div>
                <div class="card-body pt-0">
                    <ul class="nav nav-pills mb-5 mt-5 nav-style-fill nav-justified" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home"
                                role="tab" aria-controls="pills-home" aria-selected="true">Sales Status</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile"
                                role="tab" aria-controls="pills-profile" aria-selected="false">Monthly Sales</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                            aria-labelledby="pills-home-tab">
                            <canvas id="polar"></canvas>
                        </div>
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                            aria-labelledby="pills-profile-tab">
                            <canvas id="radar"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-xl-4 col-lg-6 col-12">
            <!-- Top Sell Table -->
            <div class="card card-table-border-none" data-scroll-height="550">
                <div class="card-header justify-content-between">
                    <h2>Sold by Units</h2>
                    <div>
                        <button class="text-black-50 mr-2 font-size-20"><i class="mdi mdi-cached"></i></button>
                        <div class="dropdown show d-inline-block widget-dropdown">
                            <a class="dropdown-toggle icon-burger-mini" href="#" role="button" id="dropdown-units"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                data-display="static"></a>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-units">
                                <li class="dropdown-item"><a href="#">Action</a></li>
                                <li class="dropdown-item"><a href="#">Another action</a></li>
                                <li class="dropdown-item"><a href="#">Something else here</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body slim-scroll py-0">
                    <table class="table ">
                        <tbody>
                            <tr>
                                <td class="text-dark">Backpack</td>
                                <td class="text-center">9</td>
                                <td class="text-right">33% <i
                                        class="mdi mdi-arrow-up-bold text-success pl-1 font-size-12"></i> </td>
                            </tr>
                            <tr>
                                <td class="text-dark">T-Shirt</td>
                                <td class="text-center">6</td>
                                <td class="text-right">150% <i
                                        class="mdi mdi-arrow-up-bold text-success pl-1 font-size-12"></i> </td>
                            </tr>
                            <tr>
                                <td class="text-dark">Coat</td>
                                <td class="text-center">3</td>
                                <td class="text-right">50% <i
                                        class="mdi mdi-arrow-up-bold text-success pl-1 font-size-12"></i> </td>
                            </tr>
                            <tr>
                                <td class="text-dark">Necklace</td>
                                <td class="text-center">7</td>
                                <td class="text-right">150% <i
                                        class="mdi mdi-arrow-up-bold text-success pl-1 font-size-12"></i> </td>
                            </tr>
                            <tr>
                                <td class="text-dark">Jeans Pant</td>
                                <td class="text-center">10</td>
                                <td class="text-right">300% <i
                                        class="mdi mdi-arrow-down-bold text-danger pl-1 font-size-12"></i> </td>
                            </tr>
                            <tr>
                                <td class="text-dark">Shoes</td>
                                <td class="text-center">5</td>
                                <td class="text-right">100% <i
                                        class="mdi mdi-arrow-up-bold text-success pl-1 font-size-12"></i> </td>
                            </tr>
                            <tr>
                                <td class="text-dark">T-Shirt</td>
                                <td class="text-center">6</td>
                                <td class="text-right">150% <i
                                        class="mdi mdi-arrow-up-bold text-success pl-1 font-size-12"></i> </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <div class="card-footer bg-white py-4">
                    <a href="#" class="btn-link py-3 text-uppercase">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-12">
            <!-- Notification Table -->
            <div class="card card-default" data-scroll-height="550">
                <div class="card-header justify-content-between ">
                    <h2>Latest Notifications</h2>
                    <div>
                        <button class="text-black-50 mr-2 font-size-20"><i class="mdi mdi-cached"></i></button>
                        <div class="dropdown show d-inline-block widget-dropdown">
                            <a class="dropdown-toggle icon-burger-mini" href="#" role="button"
                                id="dropdown-notification" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false" data-display="static"></a>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-notification">
                                <li class="dropdown-item"><a href="#">Action</a></li>
                                <li class="dropdown-item"><a href="#">Another action</a></li>
                                <li class="dropdown-item"><a href="#">Something else here</a></li>
                            </ul>
                        </div>
                    </div>

                </div>
                <div class="card-body slim-scroll">
                    <div class="media pb-3 align-items-center justify-content-between">
                        <div
                            class="d-flex rounded-circle align-items-center justify-content-center mr-3 media-icon iconbox-45 bg-primary text-white">
                            <i class="mdi mdi-cart-outline font-size-20"></i>
                        </div>
                        <div class="media-body pr-3 ">
                            <a class="mt-0 mb-1 font-size-15 text-dark" href="#">New Order</a>
                            <p>Selena has placed an new order</p>
                        </div>
                        <span class=" font-size-12 d-inline-block"><i class="mdi mdi-clock-outline"></i> 10 AM</span>
                    </div>

                    <div class="media py-3 align-items-center justify-content-between">
                        <div
                            class="d-flex rounded-circle align-items-center justify-content-center mr-3 media-icon iconbox-45 bg-success text-white">
                            <i class="mdi mdi-email-outline font-size-20"></i>
                        </div>
                        <div class="media-body pr-3">
                            <a class="mt-0 mb-1 font-size-15 text-dark" href="#">New Enquiry</a>
                            <p>Phileine has placed an new order</p>
                        </div>
                        <span class=" font-size-12 d-inline-block"><i class="mdi mdi-clock-outline"></i> 9 AM</span>
                    </div>


                    <div class="media py-3 align-items-center justify-content-between">
                        <div
                            class="d-flex rounded-circle align-items-center justify-content-center mr-3 media-icon iconbox-45 bg-warning text-white">
                            <i class="mdi mdi-stack-exchange font-size-20"></i>
                        </div>
                        <div class="media-body pr-3">
                            <a class="mt-0 mb-1 font-size-15 text-dark" href="#">Support Ticket</a>
                            <p>Emma has placed an new order</p>
                        </div>
                        <span class=" font-size-12 d-inline-block"><i class="mdi mdi-clock-outline"></i> 10 AM</span>
                    </div>

                    <div class="media py-3 align-items-center justify-content-between">
                        <div
                            class="d-flex rounded-circle align-items-center justify-content-center mr-3 media-icon iconbox-45 bg-primary text-white">
                            <i class="mdi mdi-cart-outline font-size-20"></i>
                        </div>
                        <div class="media-body pr-3">
                            <a class="mt-0 mb-1 font-size-15 text-dark" href="#">New order</a>
                            <p>Ryan has placed an new order</p>
                        </div>
                        <span class=" font-size-12 d-inline-block"><i class="mdi mdi-clock-outline"></i> 10 AM</span>
                    </div>

                    <div class="media py-3 align-items-center justify-content-between">
                        <div
                            class="d-flex rounded-circle align-items-center justify-content-center mr-3 media-icon iconbox-45 bg-info text-white">
                            <i class="mdi mdi-calendar-blank font-size-20"></i>
                        </div>
                        <div class="media-body pr-3">
                            <a class="mt-0 mb-1 font-size-15 text-dark" href="">Comapny Meetup</a>
                            <p>Phileine has placed an new order</p>
                        </div>
                        <span class=" font-size-12 d-inline-block"><i class="mdi mdi-clock-outline"></i> 10 AM</span>
                    </div>

                    <div class="media py-3 align-items-center justify-content-between">
                        <div
                            class="d-flex rounded-circle align-items-center justify-content-center mr-3 media-icon iconbox-45 bg-warning text-white">
                            <i class="mdi mdi-stack-exchange font-size-20"></i>
                        </div>
                        <div class="media-body pr-3">
                            <a class="mt-0 mb-1 font-size-15 text-dark" href="#">Support Ticket</a>
                            <p>Emma has placed an new order</p>
                        </div>
                        <span class=" font-size-12 d-inline-block"><i class="mdi mdi-clock-outline"></i> 10 AM</span>
                    </div>

                    <div class="media py-3 align-items-center justify-content-between">
                        <div
                            class="d-flex rounded-circle align-items-center justify-content-center mr-3 media-icon iconbox-45 bg-success text-white">
                            <i class="mdi mdi-email-outline font-size-20"></i>
                        </div>
                        <div class="media-body pr-3">
                            <a class="mt-0 mb-1 font-size-15 text-dark" href="#">New Enquiry</a>
                            <p>Phileine has placed an new order</p>
                        </div>
                        <span class=" font-size-12 d-inline-block"><i class="mdi mdi-clock-outline"></i> 9 AM</span>
                    </div>

                </div>
                <div class="mt-3"></div>
            </div>
        </div>
    </div>

</div>

@endsection
