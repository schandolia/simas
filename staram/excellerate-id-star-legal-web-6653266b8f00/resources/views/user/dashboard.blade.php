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
            <br/>
            <div class="row">
                <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4"><div style="max-width: 300px">
                                <h3>Tell us,<br>What service do you need?</h3>
                            </div>
                        </div>
                        <div class="col-md-8 card-group mb-4">
                            <div class="col-sm-6 col-lg-3">
                            <a href="{{route('request')}}">
                                <div class="card card-hover">
                                  <div class="card-body">
                                    <div class="h1 text-right mb-5">
                                      <i class="icon-tag"></i>
                                    </div>
                                    <div class="text-value text-center pt-4">Request Docs</div>
                                  </div>
                                </div>
                            </a>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                            <a href="{{route('review')}}">
                                <div class="card card-hover">
                                  <div class="card-body">
                                    <div class="h1 text-right mb-5">
                                      <i class="icon-magnifier"></i>
                                    </div>
                                    <div class="text-value text-center pt-4">Review Docs</div>
                                  </div>
                                </div>
                            </a>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                            <a href="{{route('complete')}}">
                                <div class="card card-hover">
                                  <div class="card-body">
                                    <div class="h1 text-right mb-5">
                                      <i class="icon-pencil"></i>
                                    </div>
                                    <div class="text-value text-center pt-4">Revise Docs</div>
                                  </div>
                                </div>
                            </a>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                            <a href="#email-modal" style="width:25%" data-toggle="modal" data-target="#email-modal">
                                <div class="card card-hover">
                                  <div class="card-body">
                                    <div class="h1 text-right mb-5">
                                      <i class="icon-envelope"></i>
                                    </div>
                                    <div class="text-value text-center pt-4">Email Us</div>
                                  </div>
                                </div>
                            </a>
                            </div>
                        </div>
                    </div>
                <!-- /.col-->
                </div>
                <!-- /.row-->
          </div>
        </div>
      </main>
@endsection
@section ('modal')
<!-- Modal Send Email-->
<div class="modal fade" id="email-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="form-title">
                    <div class="col">
                        <h2 class="modal-title"><b>Email Us</b></h2>
                        <strong>Connect to us, via email</strong>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <form onkeydown="return event.key!='Enter';" id="form-emailus" name="form-emailus" class="form-horizontal" action="{{route('emailUs')}}" method="POST">
            <div class="modal-body">
                <div>
                    <div class="form-group row">
                        <label class="col-md-1 col-form-label" for="purpose"><b>Title</b></label>
                        <div class="col">
                            <input class="form-control" id="email-title" type="text" name="email-title" placeholder="Title">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <div id="email-body" style="height: 300px"></div>
                        </div>
                    </div>
                </div>
            </div>
            @csrf
            <div class="modal-footer">
                @csrf
                <button type="button" class="btn btn-secondary" id="back-btn" style="width:90px" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="submit-review-btn" style="width:90px;">Send</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection
