@extends('layout.app')

@section('content')
<main class="main">
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="icon-speedometer icon-gradient bg-mean-fruit"></i>
                </div>
                <div>Dashboard</div>
            </div>
            <!-- /.card-->
            <div class="row">
                <div class="col-sm-6 col-lg-3">
                    <a href="{{route('tobeApproved')}}">
                        <div class="brand-card card-hover">
                            <div class="brand-card-header bg-facebook">
                                <i class="fa fa-pencil-square-o "></i>
                                <div class="chart-wrapper">
                                    <canvas id="social-box-chart-1" height="90"></canvas>
                                </div>
                            </div>
                            <div class="brand-card-body text-center">
                                <div>
                                    <div class="text-value">{{$tobeApprovedDocsCnt}}</div>
                                    <div class="text-muted small">Need To Approve</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- /.col-->
                <div class="col-sm-6 col-lg-3">
                    <a href="{{route('request')}}">
                        <div class="brand-card card-hover">
                            <div class="brand-card-header bg-twitter">
                                <i class="fa fa-tags"></i>
                                <div class="chart-wrapper">
                                    <canvas id="social-box-chart-2" height="90"></canvas>
                                </div>
                            </div>
                            <div class="brand-card-body text-center">
                                <div>
                                    <div class="text-value">{{$newRequestCnt}}</div>
                                    <div class="text-muted small">Requested Docs</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- /.col-->
                <div class="col-sm-6 col-lg-3">
                    <a href="{{route('review')}}">
                        <div class="brand-card card-hover">
                            <div class="brand-card-header bg-linkedin">
                                <i class="fa fa-search"></i>
                                <div class="chart-wrapper">
                                    <canvas id="social-box-chart-3" height="90"></canvas>
                                </div>
                            </div>
                            <div class="brand-card-body text-center">
                                <div>
                                    <div class="text-value">{{$newReviewCnt}}</div>
                                    <div class="text-muted small">Reviewed Docs</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
              <!-- /.col-->
              <div class="col-sm-6 col-lg-3">
                  <a href="{{route('docProcessed')}}">
                    <div class="brand-card card-hover">
                        <div class="brand-card-header bg-google-plus">
                            <i class="fa fa-gears"></i>
                            <div class="chart-wrapper">
                                <canvas id="social-box-chart-4" height="90"></canvas>
                            </div>
                        </div>
                        <div class="brand-card-body text-center">
                            <div>
                                <div class="text-value">{{$processedDocsCnt}}</div>
                                <div class="text-muted small">Document In Process</div>
                            </div>
                        </div>
                    </div>
                </a>
              </div>
              <!-- /.col-->
            </div>
            <div class="row">
                @if (($userInfo->role_id==4)||($userInfo->role_id==7))
                <div class="col-sm-6 col-lg-3">
                    <a href="{{route('availablePIC')}}">
                        <div class="brand-card card-hover">
                            <div class="brand-card-header bg-github">
                                <i class="fa fa-group"></i>
                                <div class="chart-wrapper">
                                    <canvas id="social-box-chart-1" height="90"></canvas>
                                </div>
                            </div>
                            <div class="brand-card-body text-center">
                                <div>
                                    <div class="text-value">{{$availablePIC}}</div>
                                    <div class="text-muted small">PIC Available</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endif
                <!-- /.col-->
                <div class="col-sm-6 col-lg-3">
                    <a href="{{route('approved')}}">
                        <div class="brand-card card-hover">
                            <div class="brand-card-header bg-html5">
                                <i class="fa fa-legal"></i>
                                <div class="chart-wrapper">
                                    <canvas id="social-box-chart-2" height="90"></canvas>
                                </div>
                            </div>
                            <div class="brand-card-body text-center">
                                <div>
                                    <div class="text-value">{{$approvedDocsCnt}}</div>
                                    <div class="text-muted small">Approved Docs</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- /.col-->
                <div class="col-sm-6 col-lg-3">
                    <a href="{{route('hold')}}">
                        <div class="brand-card card-hover">
                        <div class="brand-card-header bg-openid">
                            <i class="fa fa-hourglass-1"></i>
                            <div class="chart-wrapper">
                            <canvas id="social-box-chart-3" height="90"></canvas>
                            </div>
                        </div>
                        <div class="brand-card-body text-center">
                            <div>
                            <div class="text-value">{{$holdDocsCnt}}</div>
                            <div class="text-muted small">Rejected Docs</div>
                            </div>
                        </div>
                        </div>
                    </a>
                </div>
                <!-- /.col-->
                <div class="col-sm-6 col-lg-3">
                    <a href="{{route('complete')}}">
                        <div class="brand-card card-hover">
                            <div class="brand-card-header bg-stack-overflow">
                                <i class="fa fa-check-square-o"></i>
                                <div class="chart-wrapper">
                                    <canvas id="social-box-chart-4" height="90"></canvas>
                                </div>
                            </div>
                            <div class="brand-card-body text-center">
                                <div>
                                    <div class="text-value">{{$completedDocsCnt}}</div>
                                    <div class="text-muted small">Completed Docs</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- /.col-->
            </div>
            <!-- /.row-->
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header" style="font-weight:600">User Activities Chart / year </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col">
                        <div class="row">
                          <div class="col-sm-6">
                            <div class="callout callout-info">
                              <small class="text-muted">Request</small>
                              <br>
                              <strong class="h4" id="total-req-txt">0</strong>
                              <div class="chart-wrapper">
                                <canvas id="sparkline-chart-1" width="100" height="30"></canvas>
                              </div>
                            </div>
                          </div>
                          <!-- /.col-->
                          <div class="col-sm-6">
                            <div class="callout callout-success">
                              <small class="text-muted">Completed</small>
                              <br>
                              <strong class="h4" id="total-complete-txt">0</strong>
                              <div class="chart-wrapper">
                                <canvas id="sparkline-chart-2" width="100" height="30"></canvas>
                              </div>
                            </div>
                          </div>
                          <!-- /.col-->
                        </div>
                        <!-- /.row-->
                        <hr class="mt-0">
                        <canvas id="canvas" style="display: block; width: 1000px; height: 300px;" width="1000" height="300" class="chartjs-render-monitor"></canvas>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.col-->
            </div>
            <!-- /.row-->
        </div>
      </main>
@endsection
